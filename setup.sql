-- Nekomata E-commerce Database Setup
CREATE DATABASE IF NOT EXISTS nekomata_db;
USE nekomata_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    full_name VARCHAR(255),
    address TEXT,
    phone_number VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    PRIMARY KEY (category_id)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT(11) DEFAULT 0,
    category_id INT(11),
    image_url VARCHAR(255),
    view_count INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Carts table
CREATE TABLE IF NOT EXISTS carts (
    cart_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (cart_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Cart items table
CREATE TABLE IF NOT EXISTS cart_items (
    cart_item_id INT(11) NOT NULL AUTO_INCREMENT,
    cart_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (cart_item_id),
    FOREIGN KEY (cart_id) REFERENCES carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    phone_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (order_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT(11) NOT NULL AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (order_item_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    comment_id INT(11) NOT NULL AUTO_INCREMENT,
    product_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    comment_text TEXT NOT NULL,
    rating INT(11) CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (comment_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO categories (name, description, image_url) VALUES
('Electronics', 'Electronic devices and gadgets', 'https://via.placeholder.com/300x200?text=Electronics'),
('Clothing', 'Fashion and apparel', 'https://via.placeholder.com/300x200?text=Clothing'),
('Books', 'Books and literature', 'https://via.placeholder.com/300x200?text=Books'),
('Home & Garden', 'Home improvement and garden supplies', 'https://via.placeholder.com/300x200?text=Home+Garden');

INSERT INTO products (name, description, price, stock_quantity, category_id, image_url) VALUES
('Smartphone Pro', 'Latest smartphone with advanced features', 899.99, 50, 1, 'https://via.placeholder.com/300x300?text=Smartphone'),
('Laptop Gaming', 'High-performance gaming laptop', 1299.99, 25, 1, 'https://via.placeholder.com/300x300?text=Laptop'),
('Wireless Headphones', 'Premium noise-canceling headphones', 199.99, 100, 1, 'https://via.placeholder.com/300x300?text=Headphones'),
('Designer T-Shirt', 'Premium cotton designer t-shirt', 49.99, 200, 2, 'https://via.placeholder.com/300x300?text=T-Shirt'),
('Jeans Classic', 'Classic fit denim jeans', 79.99, 150, 2, 'https://via.placeholder.com/300x300?text=Jeans'),
('Programming Book', 'Learn web development from scratch', 39.99, 75, 3, 'https://via.placeholder.com/300x300?text=Book'),
('Coffee Maker', 'Automatic coffee brewing machine', 149.99, 30, 4, 'https://via.placeholder.com/300x300?text=Coffee+Maker');

-- Wishlist table
CREATE TABLE wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Wishlist shares table for sharing wishlists
CREATE TABLE wishlist_shares (
    share_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    share_token VARCHAR(64) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_share_token (share_token),
    INDEX idx_expires_at (expires_at)
);

-- Create admin user (password: admin123)
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@nekomata.com', 'Administrator', 'admin');