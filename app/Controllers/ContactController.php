<?php

namespace App\Controllers;

helper('form');

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Email\Email;

class ContactController extends BaseController
{
    public function form()
    {
        return view('contact/form');
    }

    public function send()
    {
        $rules = [
            'name'    => 'required|min_length[3]',
            'email'   => 'required|valid_email',
            'subject' => 'required|min_length[5]',
            'message' => 'required|min_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name    = $this->request->getPost('name');
        $email   = $this->request->getPost('email');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');

        $emailService = service('email');

        $emailService->setFrom(config('Email')->fromEmail, config('Email')->fromName);
        $emailService->setTo('nehemiahobati@gmail.com'); // Replace with your recipient email
        $emailService->setSubject($subject);
        $emailService->setMessage("Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}");

        if ($emailService->send()) {
            // Set a warning message indicating potential delays, even if sent successfully
            session()->setFlashdata('warning', 'Your message has been sent. Please note that email delivery may experience slight delays.');
            return redirect()->back()->with('success', 'Your message has been sent successfully!');
        } else {
            $data = $emailService->printDebugger(['headers']);
            log_message('error', 'Email sending failed: ' . print_r($data, true));
            return redirect()->back()->with('error', 'Failed to send your message. Please try again later.');
        }
    }
}
