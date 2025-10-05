<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AdminController extends BaseController
{
    /**
     * Displays a paginated list of all users.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function index()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->paginate(10); // Paginate users
        $data['pager'] = $userModel->pager; // Pass the pager instance to the view
        $data['total_users'] = $userModel->pager->getTotal(); // Get total users for accurate stats
        $data['total_balance'] = $userModel->getTotalBalance(); // Assuming this method exists in UserModel

        return $this->response->setBody(view('admin/index_view', $data));
    }

    /**
     * Handles user search functionality.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function searchUsers()
    {
        $userModel = new UserModel();
        $searchQuery = $this->request->getGet('q'); // Get search query from GET parameter 'q'

        if (empty($searchQuery)) {
            // If no search query, redirect to the main user list
            return redirect()->to(url_to('admin.index'));
        }

        // Perform search. Assuming UserModel has a searchByKeyword method.
        // If not, we'll need to add it or implement the logic here.
        // For now, let's assume a basic search by username or email.
        $data['users'] = $userModel->like('username', $searchQuery)
                                   ->orLike('email', $searchQuery)
                                   ->findAll(); // Using findAll for search results, pagination might be complex here.
                                                // For simplicity, we'll show all matching users.
        $data['search_query'] = $searchQuery;
        $data['total_users'] = count($data['users']); // Count of search results

        // We might want to pass the pager if we implement pagination for search results
        // $data['pager'] = $userModel->pager;

        return $this->response->setBody(view('admin/user_search_results', $data)); // Assuming a new view for search results
    }

    /**
     * Displays details of a specific user.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function show($id)
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->find($id);

        if (!$data['user']) {
            // User not found, redirect back with an error message
            return redirect()->back()->with('error', 'User not found.');
        }

        return $this->response->setBody(view('admin/user_details', $data));
    }

    /**
     * Updates a user's balance.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function updateBalance($id)
    {
        $userModel = new UserModel();
        // Check if bcmath extension is loaded for precise calculations
        if (!extension_loaded('bcmath')) {
            log_message('error', 'bcmath extension is not loaded. Balance calculations may be inaccurate.');
            // Optionally, return an error to the user if bcmath is critical
            // return redirect()->back()->with('error', 'Server configuration error: bcmath extension missing.');
        }

        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Input validation for amount and action
        $rules = [
            'amount' => 'required|numeric|greater_than[0]',
            'action' => 'required|in_list[deposit,withdraw]',
        ];

        if (! $this->validate($rules)) {
            // Redirect back with input and validation errors
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amount = (float) $this->request->getPost('amount'); // Cast to float for precision
        $action = $this->request->getPost('action');

        $newBalance = $user->balance; // Initialize with current balance

        if ($action === 'deposit') {
            // Use bcadd for precise float addition
            $newBalance = bcadd((string) $user->balance, (string) $amount, 2);
        } elseif ($action === 'withdraw') {
            // Check for sufficient balance before withdrawal
            if (bccomp((string) $user->balance, (string) $amount, 2) >= 0) {
                // Use bcsub for precise float subtraction
                $newBalance = bcsub((string) $user->balance, (string) $amount, 2);
            } else {
                // Insufficient balance, redirect back with error
                return redirect()->back()->withInput()->with('error', 'Insufficient balance.');
            }
        }

        // Update user balance
        if ($userModel->update($id, ['balance' => $newBalance])) {
            // Success message
            return redirect()->to(url_to('admin.users.show', $id))->with('success', 'Balance updated successfully.');
        } else {
            // Error updating balance
            return redirect()->back()->withInput()->with('error', 'Failed to update balance.');
        }
    }

    /**
     * Deletes a user.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function delete($id)
    {
        $userModel = new UserModel();
        $currentUserId = session()->get('userId'); // Get the ID of the currently logged-in user

        // Prevent admin from deleting themselves
        if ($id == $currentUserId) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Attempt to delete the user
        if ($userModel->delete($id)) {
            // Success message
            return redirect()->to(url_to('admin.index'))->with('success', 'User deleted successfully.');
        } else {
            // Error deleting user
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }
}
