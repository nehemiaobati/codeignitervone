<?php declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class AdminController extends BaseController
{
    public function index(): string
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->findAll(); // This will be updated in Phase 2
        $data['total_balance'] = $userModel->getTotalBalance();

        return view('admin/index_view', $data); // View name updated
    }

    public function show($id): string
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->find($id);

        return view('admin/user_details', $data); // View name updated
    }

    public function updateBalance($id): RedirectResponse
    {
        $userModel = new UserModel();
        if (!extension_loaded('bcmath')) {
            log_message('error', 'bcmath extension is not loaded. Balance calculations may be inaccurate.');
        }

        $user = $userModel->find($id);

        $rules = [
            'amount' => 'required|numeric|greater_than[0]',
            'action' => 'required|in_list[deposit,withdraw]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $amount = (float) $this->request->getPost('amount');
        $action = $this->request->getPost('action');

        $newBalance = $user->balance;

        if ($action === 'deposit') {
            $newBalance = bcadd((string) $user->balance, (string) $amount, 2);
        } elseif ($action === 'withdraw') {
            if (bccomp((string) $user->balance, (string) $amount, 2) >= 0) {
                $newBalance = bcsub((string) $user->balance, (string) $amount, 2);
            } else {
                return redirect()->back()->withInput()->with('error', 'Insufficient balance.');
            }
        }

        $userModel->update($id, ['balance' => $newBalance]);

        return redirect()->to(url_to('admin.users.show', $id))->with('success', 'Balance updated successfully.');
    }

    public function delete($id): RedirectResponse
    {
        $userModel = new UserModel();
        $currentUserId = session()->get('userId');

        if ($id == $currentUserId) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userModel->delete($id);

        return redirect()->to(url_to('admin.index'))->with('success', 'User deleted successfully.');
    }
}
