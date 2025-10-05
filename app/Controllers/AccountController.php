<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PaymentModel;

class AccountController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $paymentModel = new PaymentModel();

        // Assuming user ID is stored in session after login
        $userId = session()->get('userId'); // Corrected session key to match AuthController

        if (!$userId) {
            // Redirect to login if user is not logged in
            return redirect()->to(url_to('login'));
        }

        $user = $userModel->find($userId);

        // Pass user data to the view
        $data['user'] = $user;

        // Replace findAll() with paginate() for better performance
        // Setting items per page to 5 to test if pagination triggers with 11 records
        $data['transactions'] = $paymentModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->paginate(5);
        $data['pager'] = $paymentModel->pager; // Pass the pager instance to the view

        if (!$user) {
            // Handle case where user is not found (should not happen if logged in)
            return redirect()->to(url_to('home'))->with('error', 'User not found.');
        }

        // Initialize display_references array
        $data['display_references'] = [];

        // Fetch total transaction count for debugging pagination
        $totalTransactions = $paymentModel->where('user_id', $userId)->countAllResults();
        log_message('debug', 'Total transactions for user ID ' . $userId . ': ' . $totalTransactions);

        // Process transactions to determine the reference to display
        if (!empty($data['transactions'])) {
            foreach ($data['transactions'] as $transaction) { // Added foreach loop here
                $paystack_ref = null;
                $db_ref = $transaction->reference ?? null; // Reference from payments table
                $display_ref = 'N/A'; // Default value

                // Attempt to get reference from paystack_response if available
                if (!empty($transaction->paystack_response)) {
                    $paystackResponse = json_decode($transaction->paystack_response, true); // Decode as associative array

                    if (is_array($paystackResponse)) {
                        // Try to get reference from paystack response, regardless of status
                        $paystack_ref = $paystackResponse['reference'] ?? null;
                    }
                }

                // Prioritize paystack_ref if available, otherwise use db_ref
                if (!empty($paystack_ref)) {
                    $display_ref = $paystack_ref;
                } elseif (!empty($db_ref)) {
                    $display_ref = $db_ref;
                }
                // If both are empty, $display_ref remains 'N/A'

                // Add the determined reference to the data array
                $data['display_references'][] = $display_ref;
            }
        }

        return view('account/index', $data);
    }
}
