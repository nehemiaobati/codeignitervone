<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User; // Import the User model
use App\Libraries\GeminiService;
use CodeIgniter\HTTP\ResponseInterface;

class GeminiController extends BaseController
{
    protected $userModel;
    protected $geminiService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->geminiService = new GeminiService();
    }

    public function index()
    {
        $data = [
            'title' => 'Gemini AI Query',
            'result' => session()->getFlashdata('result'),
            'errors' => session()->getFlashdata('errors')
        ];
        return view('gemini/index', $data);
    }

    public function generate()
    {
        // Check user balance before proceeding
        $userId = session()->get('userId');
        if (!$userId) {
            return redirect()->back()->withInput()->with('errors', ['error' => 'You must be logged in to use this feature.']);
        }

        $user = $this->userModel->find($userId);
        $deductionAmount = 10; // Cost per AI query

        if (!$user || $user->balance < $deductionAmount) {
            return redirect()->back()->withInput()->with('errors', ['error' => 'Insufficient balance. Please top up your account.']);
        }

        // Get input text from the request
        $inputText = $this->request->getPost('prompt');
        // Get uploaded media reliably (works for single and multiple files)
        $uploadedFiles = $this->request->getFileMultiple('media') ?: [];

        // Define supported MIME types based on Gemini API documentation
        $supportedMimeTypes = [
            'image/png', 'image/jpeg', 'image/webp',
            'audio/mpeg', 'audio/mp3', 'audio/wav',
            'video/mov', 'video/mpeg', 'video/mp4', 'video/mpg', 'video/avi', 'video/wmv', 'video/mpegps', 'video/flv',
            'application/pdf',
            'text/plain'
        ];

        $parts = [];
        if ($inputText) {
            $parts[] = ['text' => $inputText];
        }

        // Limit per-file size (bytes) to avoid memory issues when base64 encoding
        $maxFileSize = 10 * 1024 * 1024; // 10 MB (adjust as needed)

        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                if ($file->isValid()) {
                    $mimeType = $file->getMimeType();

                    // Validate MIME type
                    if (!in_array($mimeType, $supportedMimeTypes)) {
                        // Return an error if any file type is unsupported
                        return redirect()->back()->withInput()->with('errors', ['error' => "Unsupported file type: {$mimeType}. Please upload only supported media types."]);
                    }

                    // Validate file size
                    if ($file->getSize() > $maxFileSize) {
                        return redirect()->back()->withInput()->with('errors', ['error' => 'Uploaded file is too large. Maximum allowed size is 10 MB.']);
                    }

                    $filePath = $file->getTempName();

                    // Read file content and base64 encode it
                    $fileContent = file_get_contents($filePath);
                    $base64Content = base64_encode($fileContent);

                    $parts[] = [
                        'inlineData' => [
                            'mimeType' => $mimeType,
                            'data' => $base64Content
                        ]
                    ];
                }
            }
        }

        // Check if there's any input (prompt or supported media)
        if (empty($parts)) {
            return redirect()->back()->withInput()->with('errors', ['error' => 'Prompt or supported media is required.']);
        }

        $response = $this->geminiService->generateContent($parts);

        if (isset($response['error'])) {
            return redirect()->back()->withInput()->with('errors', ['error' => $response['error']]);
        }

        // Deduct balance only on successful API call
        if ($this->userModel->deductBalance($userId, $deductionAmount)) {
            session()->setFlashdata('success', "{$deductionAmount} units deducted for your AI query.");
        } else {
            log_message('error', 'Failed to update user balance after successful AI query for user ID: ' . $userId);
            // Optionally, you could set an error flash message here
        }

        // Store the result in flashdata and redirect back
        return redirect()->back()->withInput()->with('result', $response['result']);
    }
}
