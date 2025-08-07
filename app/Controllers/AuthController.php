<?php

namespace App\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        
    }

    public function register()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('home');
        }

        $data = [
            'title' => 'Register - ' . SITENAME,
            'username' => '',
            'email' => '',
            'full_name' => '',
            'phone_number' => '',
            'address' => '',
            'password' => '',
            'confirm_password' => '',
            'username_err' => '',
            'email_err' => '',
            'full_name_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        if ($this->isPost()) {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => 'Register - ' . SITENAME,
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'full_name' => trim($_POST['full_name']),
                'phone_number' => trim($_POST['phone_number']),
                'address' => trim($_POST['address']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'username_err' => '',
                'email_err' => '',
                'full_name_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Load model
            $userModel = $this->model('User');

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            } elseif ($userModel->findUserByUsername($data['username'])) {
                $data['username_err'] = 'Username is already taken';
            }

            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email';
            } elseif ($userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email is already taken';
            }

            // Validate full name
            if (empty($data['full_name'])) {
                $data['full_name_err'] = 'Please enter full name';
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate confirm password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['email_err']) && empty($data['full_name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register user
                if ($userModel->register($data)) {
                    // Create cart for new user
                    $userId = $userModel->register($data);
                    $userModel->createCart($userId);
                    
                    $this->setFlash('register_success', 'You are registered and can log in');
                    $this->redirect('auth/login');
                } else {
                    die('Something went wrong');
                }
            }
        }

        $this->view('auth/register', $data);
    }

    public function login()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('home');
        }

        $data = [
            'title' => 'Login - ' . SITENAME,
            'username' => '',
            'password' => '',
            'username_err' => '',
            'password_err' => ''
        ];

        if ($this->isPost()) {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => 'Login - ' . SITENAME,
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => ''
            ];

            // Validate username
            if (empty($data['username'])) {
                $data['username_err'] = 'Please enter username or email';
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/email
            $userModel = $this->model('User');
            if ($userModel->findUserByUsername($data['username']) || $userModel->findUserByEmail($data['username'])) {
                // User found
            } else {
                $data['username_err'] = 'No user found';
            }

            // Make sure errors are empty
            if (empty($data['username_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $userModel->login($data['username'], $data['password']);

                if ($loggedInUser) {
                    // Create session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/login', $data);
            }
        } else {
            // Load view
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        $_SESSION['role'] = $user->role;
        $this->redirect('home');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        unset($_SESSION['role']);
        session_destroy();
        $this->redirect('home');
    }
}