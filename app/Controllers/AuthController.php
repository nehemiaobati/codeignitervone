<?php declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function register(): string
    {
        helper(['form']);
        $data = [];
        return view('auth/register', $data);
    }

    public function store(): ResponseInterface
    {
        helper(['form']);
        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[255]',
            'confirmpassword' => 'matches[password]',
        ];

        if (! $this->validate($rules)) {
            $response = $this->response;
            $response->setBody(view('auth/register', [
                'validation' => $this->validator,
            ]));
            return $response;
        }

        $userModel = new UserModel();
        $data = [
            'username' => $this->request->getVar('username'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];
        $userModel->save($data);

        return redirect()->to(url_to('login'))->with('success', 'Registration Successful');
    }

    public function login(): string
    {
        helper(['form']);
        $data = [];
        return view('auth/login', $data);
    }

    public function authenticate(): ResponseInterface
    {
        helper(['form']);
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            $response = $this->response;
            $response->setBody(view('auth/login', [
                'validation' => $this->validator,
            ]));
            return $response;
        }

        $userModel = new UserModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $userModel->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user->password)) {
            return redirect()->back()->withInput()->with('error', 'Invalid login credentials.');
        }

        $this->session->set([
            'isLoggedIn' => true,
            'userId'     => $user->id,
            'userEmail'  => $user->email,
            'username'   => $user->username, // Add username to session
            'is_admin'   => $user->is_admin,
            'member_since' =>$user->created_at, // Set member_since from created_at timestamp as string
        ]);

        return redirect()->to(url_to('home'))->with('success', 'Login Successful');
    }

    public function logout(): ResponseInterface
    {
        $this->session->destroy();
        return redirect()->to(url_to('login'))->with('success', 'Logged out successfully.');
    }

}
