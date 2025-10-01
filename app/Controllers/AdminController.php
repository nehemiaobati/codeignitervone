<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class AdminController extends BaseController
{
    public function index()
    {
        $userModel = new User();
        $data['users'] = $userModel->findAll(); // Fetch all users as the view expects them
        $totalBalanceData = $userModel->selectSum('balance')->first();
        $data['total_balance'] = $totalBalanceData ? $totalBalanceData->balance : '0.00';

        return view('admin/index', $data);
    }

    public function show($id)
    {
        $userModel = new User();
        $data['user'] = $userModel->find($id);

        return view('admin/show', $data);
    }

    public function updateBalance($id)
    {
        $userModel = new User();
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
        $userModel = new User();
        $currentUserId = session()->get('userId'); // Get the ID of the currently logged-in user

        // Prevent admin from deleting themselves
        if ($id == $currentUserId) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userModel->delete($id);

        return redirect()->to(url_to('admin.index'))->with('success', 'User deleted successfully.');
    }
}
