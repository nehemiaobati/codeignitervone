<?php declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class HomeController extends BaseController
{
    protected $userModel; // Declare UserModel property

    public function __construct()
    {
        $this->userModel = new User(); // Instantiate UserModel
    }

    public function index(): string
    {
        $userId = session()->get('userId'); // Get user ID from session
        $user = null;
        $balance = '0.00'; // Default balance

        if ($userId) {
            $user = $this->userModel->find($userId);
            if ($user && isset($user->balance)) {
                $balance = $user->balance;
            }
        }

        $data = [
            'pageTitle' => 'Welcome, ' . session()->get('username'),
            'username'  => session()->get('username'),
            'email'     => session()->get('userEmail'), // Corrected to match session key
            'member_since' => $user->created_at ?? null, // Get from user object if available
            'balance'   => $balance, // Pass balance to the view
        ];
        return view('home/welcome_user', $data);
    }

    public function landing(): string
    {
        $data = [
            'pageTitle' => 'Welcome to Our Custom Landing Page!',
            'heroTitle' => 'Build Your Dreams with Us',
            'heroSubtitle' => 'We provide innovative solutions to help you succeed.',
        ];
        return view('home/landing_page', $data);
    }

    public function terms(): string
    {
        $data = [
            'pageTitle' => 'Terms of Service',
        ];
        return view('home/terms', $data);
    }

    public function privacy(): string
    {
        $data = [
            'pageTitle' => 'Privacy Policy',
        ];
        return view('home/privacy', $data);
    }
}
