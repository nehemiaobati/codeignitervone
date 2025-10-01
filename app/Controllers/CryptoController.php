<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\CryptoService;

class CryptoController extends BaseController
{
    protected $cryptoService;

    protected $userModel;

    public function __construct()
    {
        $this->cryptoService = new CryptoService();
        $this->userModel = new User();
    }

    public function index()
    {
        $data = [
            'title' => 'Crypto Query',
            'result' => session()->getFlashdata('result'),
            'errors' => session()->getFlashdata('errors')
        ];
        return view('crypto/index', $data);
    }

    public function query()
    {
        

        $rules = [
            'asset' => 'required|in_list[btc,ltc]',
            'query_type' => 'required|in_list[balance,tx]',
            'address' => 'required|min_length[26]|max_length[55]', // Common BTC/LTC address length
            'limit' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[50]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $asset = $this->request->getPost('asset');
        $queryType = $this->request->getPost('query_type');
        $address = $this->request->getPost('address');
        $limit = $this->request->getPost('limit');

        $result = [];
        $errors = [];

        try {
            if ($asset === 'btc') {
                if ($queryType === 'balance') {
                    $result = $this->cryptoService->getBtcBalance($address);
                } else { // tx
                    $result = $this->cryptoService->getBtcTransactions($address, $limit);
                }
            } elseif ($asset === 'ltc') {
                if ($queryType === 'balance') {
                    $result = $this->cryptoService->getLtcBalance($address);
                } else { // tx
                    $result = $this->cryptoService->getLtcTransactions($address, $limit);
                }
            }

            if (isset($result['error'])) {
                $errors[] = $result['error'];
            }

            // Deduct balance if query was successful and user has enough balance
            if (empty($errors)) { // Check if crypto query itself had errors
                $userId = session()->get('userId'); // Corrected session key
                $deductionAmount = 10; // Amount to deduct for a successful query

                if ($userId) {
                    if ($this->userModel->deductBalance($userId, $deductionAmount)) {
                        session()->setFlashdata('success', "{$deductionAmount} units deducted for query.");
                    } else {
                        $errors[] = 'Insufficient balance or failed to update balance.';
                    }
                } else {
                    $errors[] = 'User not logged in. Cannot deduct balance.';
                    log_message('error', 'User not logged in during balance deduction.');
                }
            }

        } catch (\Exception $e) {
            $errors[] = 'An unexpected error occurred: ' . $e->getMessage();
            log_message('error', 'Crypto query error: ' . $e->getMessage());
        }

        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->back()->withInput()->with('result', $result);
    }
}
