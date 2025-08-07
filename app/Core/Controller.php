<?php

namespace App\Core;

class Controller
{
    public function model($model)
    {
        require_once '../app/Models/' . $model . '.php';
        $modelClass = "App\\Models\\" . $model;
        return new $modelClass();
    }

    public function view($view, $data = [])
    {
        // Extract data array to variables
        extract($data);
        
        // Check if view file exists
        if (file_exists('../app/Views/' . $view . '.php')) {
            require_once '../app/Views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }

    public function redirect($url)
    {
        header('Location: ' . URLROOT . '/' . $url);
        exit();
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }

    public function requireAdmin()
    {
        if (!$this->isLoggedIn() || $_SESSION['role'] !== 'admin') {
            $this->redirect('home');
        }
    }

    public function setFlash($name, $message, $type = 'success')
    {
        $_SESSION['flash_' . $name] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public function getFlash($name)
    {
        if (isset($_SESSION['flash_' . $name])) {
            $flash = $_SESSION['flash_' . $name];
            unset($_SESSION['flash_' . $name]);
            return $flash;
        }
        return false;
    }
}