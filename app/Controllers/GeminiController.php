<?php declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\GeminiService;
use CodeIgniter\HTTP\RedirectResponse;

class GeminiController extends BaseController
{
    protected UserModel $userModel;
    protected GeminiService $geminiService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->geminiService = new GeminiService();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Gemini AI Query',
            'result' => session()->getFlashdata('result'),
            'errors' => session()->getFlashdata('errors')
        ];
        return view('gemini/query_form', $data); // View name updated
    }

    public function generate(): RedirectResponse
    {
        $userId = (int) session()->get('userId'); // Cast userId to integer
        if ($userId > 0) { // Check if userId is valid (greater than 0)
            $user = $this->userModel->find($userId);
            $deductionAmount = 10;

            if (!$user || $user->balance < $deductionAmount) {
                return redirect()->back()->withInput()->with('errors', ['error' => 'Insufficient balance. Please top up your account.']);
            }
        } else {
            return redirect()->back()->withInput()->with('errors', ['error' => 'User not logged in or invalid user ID. Cannot deduct balance.']);
        }

        $user = $this->userModel->find($userId);
        $deductionAmount = 10;

        if (!$user || $user->balance < $deductionAmount) {
            return redirect()->back()->withInput()->with('errors', ['error' => 'Insufficient balance. Please top up your account.']);
        }

        $inputText = $this->request->getPost('prompt');
        $uploadedFiles = $this->request->getFileMultiple('media') ?: [];

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

        $maxFileSize = 10 * 1024 * 1024;

        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                if ($file->isValid()) {
                    $mimeType = $file->getMimeType();

                    if (!in_array($mimeType, $supportedMimeTypes)) {
                        return redirect()->back()->withInput()->with('errors', ['error' => "Unsupported file type: {$mimeType}. Please upload only supported media types."]);
                    }

                    if ($file->getSize() > $maxFileSize) {
                        return redirect()->back()->withInput()->with('errors', ['error' => 'Uploaded file is too large. Maximum allowed size is 10 MB.']);
                    }

                    $filePath = $file->getTempName();
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

        if (empty($parts)) {
            return redirect()->back()->withInput()->with('errors', ['error' => 'Prompt or supported media is required.']);
        }

        $response = $this->geminiService->generateContent($parts);

        if (isset($response['error'])) {
            return redirect()->back()->withInput()->with('errors', ['error' => $response['error']]);
        }

        if ($this->userModel->deductBalance($userId, $deductionAmount)) {
            session()->setFlashdata('success', "{$deductionAmount} units deducted for your AI query.");
        } else {
            log_message('error', 'Failed to update user balance after successful AI query for user ID: ' . $userId);
        }

        return redirect()->back()->withInput()->with('result', $response['result']);
    }
}
