# Nekomata Store - MVC E-commerce Application

A complete, modern e-commerce website built with PHP using the Model-View-Controller (MVC) architecture pattern. Features a clean black and white design, secure user authentication, shopping cart functionality, order management, and an admin panel.

## 🚀 Features

### Customer Features
- **User Authentication**: Secure registration, login, and profile management
- **Product Catalog**: Browse products with advanced filtering, sorting, and search
- **Shopping Cart**: Add/remove items, update quantities, persistent cart storage
- **Checkout Process**: Secure order placement with shipping and billing information
- **Order Management**: View order history, track orders, cancel pending orders
- **Product Reviews**: Rate and review products, view customer feedback
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5

### Admin Features
- **Dashboard**: Overview of sales, orders, and key metrics
- **Product Management**: Add, edit, and delete products
- **Order Management**: View and manage customer orders
- **User Management**: View registered users and their activity
- **Category Management**: Organize products into categories
- **Low Stock Alerts**: Monitor inventory levels

### Technical Features
- **MVC Architecture**: Clean separation of concerns
- **Secure Authentication**: Password hashing, session management
- **SQL Injection Protection**: Prepared statements throughout
- **XSS Protection**: Input sanitization and output escaping
- **AJAX Integration**: Dynamic cart updates and notifications
- **Clean URLs**: SEO-friendly routing system
- **Error Handling**: Comprehensive error management
- **Database Transactions**: Ensure data consistency

## 🏗️ MVC Architecture

```
📦 Nekomata Store
├── 📂 app/
│   ├── 📂 Core/              # Framework core classes
│   │   ├── App.php           # Main application router
│   │   ├── Controller.php    # Base controller class
│   │   └── Model.php         # Base model class
│   ├── 📂 Controllers/       # Business logic controllers
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   └── UserController.php
│   ├── 📂 Models/           # Data access layer
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   └── Comment.php
│   ├── 📂 Views/            # Presentation layer
│   │   ├── 📂 inc/          # Shared components
│   │   ├── 📂 home/         # Homepage views
│   │   ├── 📂 auth/         # Authentication views
│   │   ├── 📂 products/     # Product views
│   │   ├── 📂 cart/         # Cart views
│   │   ├── 📂 orders/       # Order views
│   │   └── 📂 users/        # User profile views
│   └── 📂 config/
│       └── config.php       # Configuration and helpers
├── 📂 public/               # Web-accessible directory
│   ├── index.php           # Application entry point
│   ├── .htaccess           # URL rewriting rules
│   └── 📂 assets/          # Static assets
│       ├── 📂 css/
│       ├── 📂 js/
│       └── 📂 images/
└── setup.sql               # Database schema
```

## 🔧 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

### Setup Instructions

1. **Clone/Download the Repository**
   ```bash
   git clone <repository-url>
   cd nekomata-store
   ```

2. **Configure Web Server**
   - Set document root to the `public/` directory
   - Ensure mod_rewrite is enabled (Apache)
   - Configure virtual host if necessary

3. **Database Setup**
   ```sql
   -- Create database
   CREATE DATABASE nekomata_db;
   
   -- Import schema and sample data
   mysql -u root -p nekomata_db < setup.sql
   ```

4. **Configure Application**
   - Edit `app/config/config.php`
   - Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'nekomata_db');
   
   define('URLROOT', 'http://your-domain.com');
   ```

5. **Set Permissions**
   ```bash
   chmod 755 public/
   chmod -R 644 public/assets/
   ```

### Default Admin Account
- **Username**: `admin`
- **Email**: `admin@nekomata.com`
- **Password**: `admin123`

## 🌐 URL Structure

The application uses clean, SEO-friendly URLs:

```
/                           # Homepage
/products                   # Product catalog
/products/show/1            # Product details
/products/categories        # Category listing
/auth/login                 # User login
/auth/register              # User registration
/cart                       # Shopping cart
/cart/checkout              # Checkout process
/orders                     # Order history
/orders/show/1              # Order details
/users/profile              # User profile
/admin                      # Admin dashboard
```

## 🔐 Security Features

### Authentication & Authorization
- **Password Hashing**: Using PHP's `password_hash()` function
- **Session Security**: HTTP-only cookies, secure flags
- **Role-based Access**: Admin vs regular user permissions
- **Login Protection**: Secure session management

### Data Security
- **SQL Injection Prevention**: All queries use prepared statements
- **XSS Protection**: Input sanitization with `htmlspecialchars()`
- **CSRF Protection**: Form validation and origin checking
- **Input Validation**: Server-side validation for all forms

### Server Security
- **File Access Control**: `.htaccess` restrictions
- **Directory Protection**: Prevents directory listing
- **Error Handling**: No sensitive information in error messages
- **HTTP Headers**: Security headers for XSS and clickjacking protection

## 🛠️ Development

### Adding New Features

1. **Create Model** (if needed):
   ```php
   <?php
   namespace App\Models;
   use App\Core\Model;
   
   class YourModel extends Model {
       // Your model methods
   }
   ```

2. **Create Controller**:
   ```php
   <?php
   namespace App\Controllers;
   use App\Core\Controller;
   
   class YourController extends Controller {
       public function index() {
           $this->view('your/index', $data);
       }
   }
   ```

3. **Create Views**:
   ```php
   <?php require APPROOT . '/Views/inc/header.php'; ?>
   <!-- Your view content -->
   <?php require APPROOT . '/Views/inc/footer.php'; ?>
   ```

### Database Queries
Use the base Model class methods:
```php
// Select data
$this->query('SELECT * FROM table WHERE id = :id');
$this->bind(':id', $id);
$result = $this->resultSet();

// Insert data
$this->query('INSERT INTO table (column) VALUES (:value)');
$this->bind(':value', $value);
$this->execute();
```

### Flash Messages
```php
// Set flash message
$this->setFlash('message_name', 'Your message', 'alert-success');

// Display flash message in view
<?php flash('message_name'); ?>
```

## 🎨 Customization

### Styling
- Edit `public/assets/css/style.css` for custom styles
- CSS variables are used for easy color scheme changes
- Bootstrap 5 classes available throughout

### Configuration
- Modify `app/config/config.php` for application settings
- Add helper functions in the config file
- Update constants for branding

### Database Schema
- Modify `setup.sql` for schema changes
- Update corresponding Model classes
- Ensure foreign key relationships are maintained

## 📱 Browser Support

- **Modern Browsers**: Chrome 60+, Firefox 60+, Safari 12+, Edge 79+
- **Mobile**: iOS Safari 12+, Chrome Mobile 60+
- **Features**: CSS Grid, Flexbox, ES6, Fetch API

## 🚀 Performance

### Optimization Features
- **CSS/JS Compression**: Minified assets
- **Image Optimization**: Lazy loading support
- **Database Optimization**: Indexed queries, optimized joins
- **Caching Headers**: Browser caching for static assets
- **GZIP Compression**: Enabled via .htaccess

### Recommended Enhancements
- Implement Redis/Memcached for session storage
- Add CDN integration for static assets
- Enable PHP OPcache for improved performance
- Implement database query caching

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📞 Support

For support and questions:
- Create an issue in the repository
- Check the documentation in the code comments
- Review the database schema in `setup.sql`

---

**Nekomata Store** - A modern, secure, and scalable e-commerce solution built with PHP MVC architecture.