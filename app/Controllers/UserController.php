<?php

namespace App\Controllers;

use App\Core\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        
    }

    public function profile()
    {
        $this->requireLogin();

        $userModel = $this->model('User');

        $data = [
            'title' => 'My Profile - ' . SITENAME,
            'user' => $userModel->getUserById($_SESSION['user_id']),
            'user_stats' => $userModel->getUserStats($_SESSION['user_id']),
            'full_name' => '',
            'email' => '',
            'phone_number' => '',
            'address' => '',
            'current_password' => '',
            'new_password' => '',
            'confirm_password' => '',
            'full_name_err' => '',
            'email_err' => '',
            'current_password_err' => '',
            'new_password_err' => '',
            'confirm_password_err' => ''
        ];

        if ($this->isPost()) {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $updateType = $_POST['update_type'] ?? '';

            if ($updateType === 'profile') {
                $this->updateProfile($data, $userModel);
            } elseif ($updateType === 'password') {
                $this->updatePassword($data, $userModel);
            }
        }

        $this->view('users/profile', $data);
    }

    private function updateProfile(&$data, $userModel)
    {
        $data['full_name'] = trim($_POST['full_name']);
        $data['email'] = trim($_POST['email']);
        $data['phone_number'] = trim($_POST['phone_number']);
        $data['address'] = trim($_POST['address']);

        // Validate full name
        if (empty($data['full_name'])) {
            $data['full_name_err'] = 'Please enter full name';
        }

        // Validate email
        if (empty($data['email'])) {
            $data['email_err'] = 'Please enter email';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $data['email_err'] = 'Please enter a valid email';
        } elseif ($userModel->emailExists($data['email'], $_SESSION['user_id'])) {
            $data['email_err'] = 'Email is already taken';
        }

        // Make sure no errors
        if (empty($data['full_name_err']) && empty($data['email_err'])) {
            $profileData = [
                'user_id' => $_SESSION['user_id'],
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'address' => $data['address']
            ];

            if ($userModel->updateProfile($profileData)) {
                $_SESSION['email'] = $data['email'];
                $this->setFlash('profile_success', 'Profile updated successfully!');
                $this->redirect('users/profile');
            } else {
                $this->setFlash('profile_error', 'Failed to update profile', 'alert-danger');
            }
        }
    }

    private function updatePassword(&$data, $userModel)
    {
        $data['current_password'] = trim($_POST['current_password']);
        $data['new_password'] = trim($_POST['new_password']);
        $data['confirm_password'] = trim($_POST['confirm_password']);

        // Validate current password
        if (empty($data['current_password'])) {
            $data['current_password_err'] = 'Please enter current password';
        } elseif (!$userModel->verifyPassword($_SESSION['user_id'], $data['current_password'])) {
            $data['current_password_err'] = 'Current password is incorrect';
        }

        // Validate new password
        if (empty($data['new_password'])) {
            $data['new_password_err'] = 'Please enter new password';
        } elseif (strlen($data['new_password']) < 6) {
            $data['new_password_err'] = 'Password must be at least 6 characters';
        }

        // Validate confirm password
        if (empty($data['confirm_password'])) {
            $data['confirm_password_err'] = 'Please confirm new password';
        } elseif ($data['new_password'] !== $data['confirm_password']) {
            $data['confirm_password_err'] = 'Passwords do not match';
        }

        // Make sure no errors
        if (empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])) {
            $passwordData = [
                'user_id' => $_SESSION['user_id'],
                'password' => password_hash($data['new_password'], PASSWORD_DEFAULT),
                'full_name' => $data['user']->full_name,
                'email' => $data['user']->email,
                'phone_number' => $data['user']->phone_number,
                'address' => $data['user']->address
            ];

            if ($userModel->updateProfile($passwordData)) {
                $this->setFlash('password_success', 'Password updated successfully!');
                $this->redirect('users/profile');
            } else {
                $this->setFlash('password_error', 'Failed to update password', 'alert-danger');
            }
        }
    }
}