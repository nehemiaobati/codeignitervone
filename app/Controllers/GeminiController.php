<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class GeminiController extends BaseController
{
    public function index()
    {
        return view('gemini/index');
    }

    public function generate()
    {
        // Get API key from .env file
        // Prefer CodeIgniter's env() helper
        $geminiApiKey = env('GEMINI_API_KEY') ?? getenv('GEMINI_API_KEY');
        if (!$geminiApiKey) {
            // Return JSON error if API key is not set
            return $this->response->setStatusCode(500)->setJSON(['error' => 'GEMINI_API_KEY not set in .env file.']);
        }

        $modelId = "gemini-flash-lite-latest";
        $generateContentApi = "streamGenerateContent";

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
                        return $this->response->setStatusCode(400)->setJSON(['error' => "Unsupported file type: {$mimeType}. Please upload only supported media types."]);
                    }

                    // Validate file size
                    if ($file->getSize() > $maxFileSize) {
                        return $this->response->setStatusCode(413)->setJSON(['error' => 'Uploaded file is too large. Maximum allowed size is 10 MB.']);
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
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Prompt or supported media is required.']);
        }

        $requestPayload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => $parts
                ]
            ],
            "generationConfig" => [
                "thinkingConfig" => [
                    "thinkingBudget" => -1,
                ],
            ],
            "tools" => [
                [
                    "googleSearch" => (object)[] // Empty object for googleSearch tool
                ]
            ],
        ];

        // Prepare the request body for curl
        $requestBody = json_encode($requestPayload, JSON_PRETTY_PRINT); // Use JSON_PRETTY_PRINT for readability

        // Write the request payload to a file for debugging
        $logFilePath = WRITEPATH . 'logs/gemini_payload.log';
        file_put_contents($logFilePath, "--- Request Payload (" . date('Y-m-d H:i:s') . ") ---\n", FILE_APPEND);
        file_put_contents($logFilePath, $requestBody . "\n\n", FILE_APPEND);

        // Use CodeIgniter's HTTP client service for a robust solution.
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->setBody($requestBody)
                               ->setHeader('Content-Type', 'application/json')
                               ->post("https://generativelanguage.googleapis.com/v1beta/models/{$modelId}:{$generateContentApi}?key={$geminiApiKey}");

            $responseBody = $response->getBody();
            $responseData = json_decode($responseBody, true);

            if ($response->getStatusCode() !== 200) {
                // Handle API errors
                $errorMessage = $responseData['error']['message'] ?? 'Unknown API error';
                return $this->response->setStatusCode($response->getStatusCode())->setJSON(['error' => $errorMessage]);
            }

            $processedText = '';

            // --- MODIFIED LOGIC FOR EXTRACTING TEXT ---
            // Gemini API responses can be an array of chunks (for streamed responses)
            // or a single object (for non-streamed or single chunk responses).
            if (is_array($responseData)) {
                foreach ($responseData as $chunk) {
                    if (isset($chunk['candidates']) && is_array($chunk['candidates'])) {
                        foreach ($chunk['candidates'] as $candidate) {
                            if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
                                foreach ($candidate['content']['parts'] as $part) {
                                    if (isset($part['text'])) {
                                        $processedText .= $part['text'];
                                    }
                                }
                            }
                        }
                    }
                }
            } elseif (isset($responseData['candidates']) && is_array($responseData['candidates'])) {
                // Handle case where responseData is a single object with candidates
                foreach ($responseData['candidates'] as $candidate) {
                    if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
                        foreach ($candidate['content']['parts'] as $part) {
                            if (isset($part['text'])) {
                                $processedText .= $part['text'];
                            }
                        }
                    }
                }
            }
            // --- END MODIFIED LOGIC ---

            // Check if full response is requested
            if ($this->request->getGet('full_response')) {
                return $this->response->setJSON($responseData); // Return raw response
            } else {
                // Return the processed text
                return $this->response->setJSON(['response' => $processedText]);
            }

        } catch (\Exception $e) {
            // Handle exceptions during the request
            return $this->response->setStatusCode(500)->setJSON(['error' => 'An error occurred while processing your request: ' . $e->getMessage()]);
        }
    }
}
