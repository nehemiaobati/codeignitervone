<?php declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\PaystackService;
use App\Models\Payment;
use App\Models\User;
use CodeIgniter\HTTP\RedirectResponse;

class PaymentsController extends BaseController
{
    protected $paymentModel;
    protected $paystackService;
    protected $userModel; // Declare userModel property

    public function __construct()
    {
        $this->paymentModel    = new Payment();
        $this->paystackService = \Config\Services::paystackService();
        $this->userModel       = new User(); // Instantiate UserModel
        helper(['form', 'url']);
    }

    public function index(): string
    {
        $data = [
            'email' => session()->get('userEmail') ?? '', // Pre-fill email if user is logged in
            'errors' => session()->getFlashdata('errors'),
        ];

        return view('payment/form', $data);
    }

    public function initiate(): RedirectResponse
    {
        $rules = [
            'email'  => 'required|valid_email',
            'amount' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email  = $this->request->getPost('email');
        $amount = (int) $this->request->getPost('amount');
        $userId = session()->get('userId'); // Correctly retrieve user ID from session

        // Generate a unique reference
        $reference = 'PAY-' . time() . '-' . bin2hex(random_bytes(5));

        // Save payment as pending
        $this->paymentModel->insert([
            'user_id'   => $userId,
            'email'     => $email,
            'amount'    => $amount,
            'reference' => $reference,
            'status'    => 'pending',
        ]);

        $callbackUrl = url_to('payment.verify') . '?app_reference=' . $reference;

        $response = $this->paystackService->initializeTransaction($email, $amount, $callbackUrl);

        if ($response['status'] === true) {
            return redirect()->to($response['data']['authorization_url']);
        }

        return redirect()->back()->with('errors', ['paystack' => $response['message']]);
    }

    public function verify(): RedirectResponse
    {
        $appReference = $this->request->getGet('app_reference'); // Our internal reference
        $paystackReference = $this->request->getGet('trxref'); // Paystack's transaction reference

        if (empty($appReference) || empty($paystackReference)) {
            return redirect()->to(url_to('payment.index'))->with('errors', ['payment' => 'Payment reference not found.']);
        }

        $payment = $this->paymentModel->where('reference', $appReference)->first();

        if ($payment === null) {
            return redirect()->to(url_to('payment.index'))->with('errors', ['payment' => 'Invalid payment reference.']);
        }

        if ($payment->status === 'success') {
            return redirect()->to(url_to('payment.index'))->with('success', 'Payment already verified.');
        }

        $response = $this->paystackService->verifyTransaction($paystackReference);

        if ($response['status'] === true && $response['data']['status'] === 'success') {
            // Update payment status
            $this->paymentModel->update($payment->id, [
                'status'            => 'success',
                'paystack_response' => json_encode($response['data']),
            ]);

            // Accumulate balance
            if ($payment->user_id) { // Ensure user_id is available
                $user = $this->userModel->find($payment->user_id); // Find the user

                if ($user) {
                    // Use the entity to update balance
                    // Ensure balance is treated as a string for bcadd
                    $currentBalance = is_string($user->balance) ? $user->balance : (string) $user->balance;
                    $paymentAmount = is_string($payment->amount) ? $payment->amount : (string) $payment->amount;
                    
                    $user->balance = bcadd($currentBalance ?? '0.00', $paymentAmount, 2);
                    $this->userModel->save($user); // Save the updated user
                }
            }

            return redirect()->to(url_to('payment.index'))->with('success', 'Payment successful!');
        }

        $this->paymentModel->update($payment->id, [
            'status'            => 'failed',
            'paystack_response' => json_encode($response['data'] ?? $response),
        ]);

        return redirect()->to(url_to('payment.index'))->with('errors', ['payment' => $response['message'] ?? 'Payment verification failed.']);
    }
}
