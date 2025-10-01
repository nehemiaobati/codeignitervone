<?php

namespace App\Libraries;

class PaystackService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';
    protected string $currency = 'KES'; // Default currency

    public function __construct()
    {
        $this->secretKey = env('PAYSTACK_SECRET_KEY');
        if (empty($this->secretKey)) {
            throw new \Exception('Paystack secret key is not set in .env file.');
        }
    }

    public function initializeTransaction(string $email, int $amount, string $callbackUrl, string $currency = null): array
    {
        $url = $this->baseUrl . '/transaction/initialize';
        $fields = [
            'email'        => $email,
            'amount'       => $amount * 100, // Amount in kobo
            'callback_url' => $callbackUrl,
            'currency'     => $currency ?? $this->currency,
        ];

        return $this->sendRequest('POST', $url, $fields);
    }

    public function verifyTransaction(string $reference): array
    {
        $url = $this->baseUrl . '/transaction/verify/' . rawurlencode($reference);

        return $this->sendRequest('GET', $url);
    }

    private function sendRequest(string $method, string $url, array $fields = []): array
    {
        $client = \Config\Services::curlrequest();

        $headers = [
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ];

        try {
            if ($method === 'POST') {
                $response = $client->post($url, [
                    'headers' => $headers,
                    'json'    => $fields,
                ]);
            } else {
                $response = $client->get($url, [
                    'headers' => $headers,
                ]);
            }

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            log_message('error', 'Paystack API Error: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Error communicating with Paystack: ' . $e->getMessage(),
            ];
        }
    }
}
