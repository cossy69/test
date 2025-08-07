<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Register user
    public function register($data)
    {
        $this->query('INSERT INTO users (username, email, password, full_name, phone_number, address) VALUES (:username, :email, :password, :full_name, :phone_number, :address)');
        
        // Bind values
        $this->bind(':username', $data['username']);
        $this->bind(':email', $data['email']);
        $this->bind(':password', $data['password']);
        $this->bind(':full_name', $data['full_name']);
        $this->bind(':phone_number', $data['phone_number']);
        $this->bind(':address', $data['address']);

        // Execute
        if ($this->execute()) {
            return $this->lastInsertId();
        } else {
            return false;
        }
    }

    // Login User
    public function login($username, $password)
    {
        $this->query('SELECT * FROM users WHERE username = :username OR email = :username');
        $this->bind(':username', $username);

        $row = $this->single();

        if ($row) {
            $hashedPassword = $row->password;
            if (password_verify($password, $hashedPassword)) {
                return $row;
            }
        }

        return false;
    }

    // Find user by email
    public function findUserByEmail($email)
    {
        $this->query('SELECT * FROM users WHERE email = :email');
        $this->bind(':email', $email);

        $row = $this->single();

        // Check row
        if ($this->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Find user by username
    public function findUserByUsername($username)
    {
        $this->query('SELECT * FROM users WHERE username = :username');
        $this->bind(':username', $username);

        $row = $this->single();

        // Check row
        if ($this->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get user by ID
    public function getUserById($id)
    {
        $this->query('SELECT * FROM users WHERE user_id = :id');
        $this->bind(':id', $id);

        return $this->single();
    }

    // Update user profile
    public function updateProfile($data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $this->query('UPDATE users SET full_name = :full_name, email = :email, phone_number = :phone_number, address = :address, password = :password WHERE user_id = :user_id');
            $this->bind(':password', $data['password']);
        } else {
            $this->query('UPDATE users SET full_name = :full_name, email = :email, phone_number = :phone_number, address = :address WHERE user_id = :user_id');
        }

        $this->bind(':full_name', $data['full_name']);
        $this->bind(':email', $data['email']);
        $this->bind(':phone_number', $data['phone_number']);
        $this->bind(':address', $data['address']);
        $this->bind(':user_id', $data['user_id']);

        return $this->execute();
    }

    // Get all users (admin)
    public function getUsers()
    {
        $this->query('SELECT user_id, username, email, full_name, role, created_at FROM users ORDER BY created_at DESC');
        return $this->resultSet();
    }

    // Get user statistics
    public function getUserStats($userId)
    {
        $this->query('SELECT 
                        (SELECT COUNT(*) FROM orders WHERE user_id = :user_id) as total_orders,
                        (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = :user_id) as total_spent,
                        (SELECT COUNT(*) FROM comments WHERE user_id = :user_id) as total_reviews
                     ');
        $this->bind(':user_id', $userId);
        return $this->single();
    }

    // Create cart for user
    public function createCart($userId)
    {
        $this->query('INSERT INTO carts (user_id) VALUES (:user_id)');
        $this->bind(':user_id', $userId);
        return $this->execute();
    }

    // Check if email exists (excluding current user)
    public function emailExists($email, $excludeUserId = null)
    {
        if ($excludeUserId) {
            $this->query('SELECT user_id FROM users WHERE email = :email AND user_id != :user_id');
            $this->bind(':user_id', $excludeUserId);
        } else {
            $this->query('SELECT user_id FROM users WHERE email = :email');
        }
        
        $this->bind(':email', $email);
        return $this->rowCount() > 0;
    }

    // Verify current password
    public function verifyPassword($userId, $password)
    {
        $this->query('SELECT password FROM users WHERE user_id = :user_id');
        $this->bind(':user_id', $userId);
        
        $user = $this->single();
        if ($user) {
            return password_verify($password, $user->password);
        }
        
        return false;
    }
}