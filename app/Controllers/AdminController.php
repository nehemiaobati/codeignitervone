<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AdminController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        // Replace findAll() with paginate() for better performance
        $data['users'] = $userModel->paginate(10);
        $data['pager'] = $userModel->pager; // Pass the pager instance to the view
        $data['total_users'] = $userModel->pager->getTotal(); // Get total users for accurate stats
        $data['total_balance'] = $userModel->getTotalBalance();

        return view('admin/index_view', $data);
    }

    public function show($id)
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->find($id);

        return view('admin/user_details', $data);
    }

    public function updateBalance($id)
    {
        $userModel = new UserModel();
        // Check if bcmath extension is loaded for precise calculations
        if (!extension_loaded('bcmath')) {
            log_message('error', 'bcmath extension is not loaded. Balance calculations may be inaccurate.');
        }

        $user = $userModel->find($id);

        // Input validation for amount and action
        $rules = [
            'amount' => 'required|numeric|greater_than[0]',
            'action' => 'required|in_list[deposit,withdraw]',
        ];

        if (! $this->validate($rules)) {
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
                return redirect()->back()->withInput()->with('error', 'Insufficient balance.');
            }
        }

        $userModel->update($id, ['balance' => $newBalance]);

        return redirect()->to(url_to('admin.users.show', $id))->with('success', 'Balance updated successfully.');
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $currentUserId = session()->get('userId'); // Get the ID of the currently logged-in user

        // Prevent admin from deleting themselves
        if ($id == $currentUserId) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userModel->delete($id);

        return redirect()->to(url_to('admin.index'))->with('success', 'User deleted successfully.');
    }
}
