# Nekomata Store - E-commerce Website

A complete e-commerce website built with PHP, MySQL, HTML, CSS (Bootstrap), and JavaScript featuring a modern black and white theme.

## Features

### 🛍️ **Customer Features**
- **User Registration & Login**: Secure user authentication system
- **Product Catalog**: Browse products with category filtering and search
- **Product Details**: Detailed product pages with images, descriptions, and reviews
- **Shopping Cart**: Add/remove items with quantity management
- **Checkout Process**: Secure checkout with order confirmation
- **Order Management**: View order history and track order status
- **Product Reviews**: Rate and review products
- **User Profile**: Manage personal information and preferences

### 🔧 **Admin Features**
- **Admin Dashboard**: Overview of store statistics and recent activity
- **Product Management**: Add, edit, and manage product inventory
- **Order Management**: View and update order statuses
- **User Management**: Manage customer accounts
- **Category Management**: Organize products into categories
- **Low Stock Alerts**: Monitor inventory levels

### 🎨 **Design Features**
- **Modern Black & White Theme**: Clean, professional design
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Bootstrap Integration**: Modern UI components and layouts
- **Font Awesome Icons**: Professional iconography throughout
- **Smooth Animations**: Enhanced user experience with subtle transitions

## Database Schema

The application uses the following database tables:

- **users**: Customer and admin accounts
- **categories**: Product categories
- **products**: Product information and inventory
- **carts**: Shopping cart sessions
- **cart_items**: Items in shopping carts
- **orders**: Order information
- **order_items**: Individual items in orders
- **comments**: Product reviews and ratings

## Installation

### Prerequisites
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Setup Instructions

1. **Clone or Download** the project files to your web server directory

2. **Database Setup**:
   ```sql
   -- Import the setup.sql file to create the database and tables
   mysql -u your_username -p < setup.sql
   ```

3. **Configure Database Connection**:
   Edit `config/database.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'nekomata_db';
   $username = 'your_username';
   $password = 'your_password';
   ```

4. **Set File Permissions**:
   Ensure proper permissions for the web server to read/write files

5. **Access the Website**:
   - Customer site: `http://your-domain.com/`
   - Admin panel: `http://your-domain.com/admin/`

### Default Admin Account
- **Username**: admin
- **Password**: admin123
- **Email**: admin@nekomata.com

*Remember to change the default admin password after installation!*

## File Structure

```
nekomata-store/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── header.php           # Common header with navigation
│   └── footer.php           # Common footer
├── assets/
│   ├── css/
│   │   └── style.css        # Custom styling
│   └── js/
│       └── script.js        # JavaScript functionality
├── admin/
│   └── index.php           # Admin dashboard
├── index.php               # Homepage
├── products.php            # Product catalog
├── product.php             # Product details
├── categories.php          # Category listing
├── cart.php               # Shopping cart
├── checkout.php           # Checkout process
├── orders.php             # Order history
├── profile.php            # User profile
├── login.php              # User login
├── register.php           # User registration
├── logout.php             # Logout functionality
├── add_to_cart.php        # Add to cart handler
├── cancel_order.php       # Order cancellation
└── setup.sql              # Database setup script
```

## Key Features Explained

### 🔐 **Security Features**
- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Session management for user authentication
- Admin role-based access control

### 💳 **Payment Integration**
- Currently supports Cash on Delivery (COD)
- Designed for easy integration with payment gateways
- Order tracking and status management

### 📱 **Responsive Design**
- Mobile-first approach
- Bootstrap 5 for responsive grid system
- Optimized for all screen sizes
- Touch-friendly interface

### 🔍 **Search & Filter**
- Product search by name and description
- Category-based filtering
- Price sorting (low to high, high to low)
- Popularity and newest product sorting

### 📊 **Analytics Ready**
- View count tracking for products
- Order statistics and revenue tracking
- User engagement metrics
- Admin dashboard with key performance indicators

## Customization

### Changing Colors
The black and white theme can be customized by modifying the CSS variables in `assets/css/style.css`:

```css
:root {
    --primary-black: #000000;
    --primary-white: #ffffff;
    --light-gray: #f8f9fa;
    --medium-gray: #6c757d;
    --dark-gray: #343a40;
    --accent-gray: #e9ecef;
}
```

### Adding Payment Methods
To integrate payment gateways:
1. Modify the checkout process in `checkout.php`
2. Add payment method selection
3. Integrate with your preferred payment provider's API
4. Update order status handling

### Extending Admin Features
The admin panel can be extended by:
1. Adding new pages in the `admin/` directory
2. Following the existing navigation structure
3. Implementing proper authentication checks
4. Using the established styling patterns

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Contributing

This is a complete e-commerce solution ready for production use or further development. Feel free to extend and customize according to your needs.

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions:
- Check the code comments for implementation details
- Review the database schema in `setup.sql`
- Examine the included sample data for examples

---

**Nekomata Store** - Built with ❤️ using PHP, MySQL, Bootstrap, and modern web technologies.