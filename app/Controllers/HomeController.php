<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        
    }

    public function index()
    {
        // Load models
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');

        // Get featured products
        $featuredProducts = $productModel->getFeaturedProducts(8);
        
        // Get categories
        $categories = $categoryModel->getCategories();

        $data = [
            'title' => 'Welcome to ' . SITENAME,
            'featured_products' => $featuredProducts,
            'categories' => $categories
        ];

        $this->view('home/index', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About Us - ' . SITENAME
        ];

        $this->view('home/about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Contact Us - ' . SITENAME,
            'name' => '',
            'email' => '',
            'message' => '',
            'name_err' => '',
            'email_err' => '',
            'message_err' => ''
        ];

        if ($this->isPost()) {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => 'Contact Us - ' . SITENAME,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'message' => trim($_POST['message']),
                'name_err' => '',
                'email_err' => '',
                'message_err' => ''
            ];

            // Validate name
            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter your name';
            }

            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter your email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email';
            }

            // Validate message
            if (empty($data['message'])) {
                $data['message_err'] = 'Please enter your message';
            }

            // Make sure no errors
            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['message_err'])) {
                // Process contact form (send email, save to database, etc.)
                // For now, just show success message
                $this->setFlash('contact_message', 'Thank you for your message. We will get back to you soon!');
                $this->redirect('home/contact');
            }
        }

        $this->view('home/contact', $data);
    }
}