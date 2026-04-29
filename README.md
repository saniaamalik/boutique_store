# Stylish Boutique Web Application

## Overview
Stylish Boutique is a PHP-based fashion storefront web application. It is built with a focus on user management, core CRUD operations, responsive UI, and basic security. The application demonstrates a real-world student project implementation using commonly accepted web development best practices.

### What this project includes
- User registration, login, and session-based authentication
- Admin vs customer role handling
- Product catalog and category management
- Shopping cart workflow and order placement
- Database-driven application with MySQL
- Input validation, password hashing, prepared statements, and CSRF tokens
- Responsive HTML/CSS interface for desktop and mobile

## Requirement Fulfillment
This project fulfills the assignment requirements by providing:
- User management with registration, login, and profile access
- CRUD operations on product and category data
- Search, filter, and sort functionality for products
- Shopping cart and order placement features
- Role-based access control for admin and customer users
- Secure session handling, prepared statements, and CSRF protection
- Deployment-ready code suitable for GitHub and free PHP hosting

## Technology Stack
- PHP 8.x (backend scripting)
- MySQL / MariaDB (relational database)
- HTML5 and CSS3 for frontend layout and responsive design
- Minimal JavaScript for user experience and navigation
- XAMPP for local Apache and MySQL server setup
- `mysqli` extension for secure database queries and prepared statements

## Project Structure and Important Files
- `index.php` � Public homepage with login/register navigation
- `login.php` � Secure user login page with CSRF protection
- `register.php` � User registration and validation page
- `config/db.php` � Database connection and helper utility functions
- `admin/` � Admin panel for dashboards and management pages
- `customers/` � Customer dashboard, cart, orders, and profile features
- `uploads/` � Directory for product image files
- `project.sql` � Complete SQL schema and sample data import file

## Detailed Setup Instructions
Follow these steps to run the project locally using XAMPP.

### 1. Install and Start XAMPP
1. Download XAMPP from https://www.apachefriends.org/index.html
2. Install XAMPP on Windows
3. Open XAMPP Control Panel
4. Start `Apache` and `MySQL`
5. Confirm both services are running successfully

### 2. Place Project Files
1. Copy the full `web_project` folder into `C:\xampp\htdocs\`
2. Confirm the file path is `C:\xampp\htdocs\web_project`
3. Verify these files exist in the root:
   - `index.php`
   - `login.php`
   - `register.php`
   - `project.sql`
   - `config/db.php`

### 3. Create the Database
1. Open `http://localhost/phpmyadmin/`
2. Click on `Databases`
3. Enter `db_boutique` as the database name
4. Click `Create`

### 4. Import SQL Schema and Sample Data
1. In phpMyAdmin, select the new `db_boutique`
2. Click the `Import` tab
3. Choose the file `project.sql` from the project folder
4. Click `Go`
5. Confirm import succeeded and tables were created

### 5. Confirm Required Tables
After import, confirm these tables are present:
- `users`
- `categories`
- `products`
- `cart`
- `orders`
- `order_items`

### 6. Verify Database Settings
1. Open `config/db.php`
2. Confirm connection details:
   - Host: `localhost`
   - Username: `root`
   - Password: (empty)
   - Database: `db_boutique`
3. If MySQL uses a password, update the file accordingly

### 7. Run the Application
1. Open browser and visit:
   - `http://localhost/web_project/index.php`
2. Use `Register` to create a new customer account
3. Use `Login` to access the customer dashboard
4. To access admin pages, log in as an admin user

## Sample Admin and Customer Accounts
The SQL import includes example accounts:
- Admin:
  - Username: `admin`
  - Email: `admin@gmail.com`
  - Password: `admin123`
- Customer:
  - Username: `sania`
  - Email: `sania@gmail.com`
  - Password: `sania123`

> Note: Passwords are stored securely using PHP `password_hash()`.

## How to Use the Application
### Customer Use Case
1. Register a new account on `register.php`
2. Login on `login.php`
3. Filter products by category
4. Search products using the search bar
5. Sort products by price
6. Add products to cart and update quantities
7. Place an order from the cart page

### Admin Use Case
1. Login with admin credentials
2. Access `admin/admin_dashboard.php`
3. View counts for customers, products, categories, orders, and revenue
4. Navigate to admin management pages to view or edit items

## Database and Feature Summary
- `users` table stores user profiles, roles, and contact data
- `categories` table stores product categories
- `products` table handles product data, pricing, stock, and images
- `cart` table stores active user cart items
- `orders` table stores order summaries and statuses
- `order_items` table stores individual products in each order

## Security Features
- CSRF token protection for forms
- Password hashing with `password_hash()`
- Prepared SQL statements to mitigate SQL injection
- Input validation on registration and login fields
- Session checks for protected pages
- Output sanitization using `htmlspecialchars()` in displayed content

## Deployment Instructions
### GitHub Deployment
1. Create a new public repository on GitHub
2. Commit the full `web_project` folder
3. Push the code to GitHub
4. Use the GitHub repo link as the source code submission

##Hostinger Deployment
1.Sign up / login at https://www.hostinger.com/
2.Purchase a hosting plan (Premium or Business recommended) and connect your domain
3.Go to hPanel → Files → File Manager
4.Upload all web_project files into the public_html folder
5.Go to hPanel → Databases → MySQL Databases and create a new database
6.Import project.sql into the database using phpMyAdmin
7.Update config/db.php with Hostinger database credentials (DB name, username, password, host)
8.Open your domain URL and verify that the application is working correctly

## Notes
- This README update does not modify any application source files
- The project is designed to run on local XAMPP and can be deployed to free PHP hosts
- If the site fails to connect, re-check `config/db.php` database credentials

## Troubleshooting
- If `Apache` or `MySQL` will not start, ensure no port conflict exists on `80` or `3306`
- If registration fails, verify the database tables exist and the SQL import completed
- If login fails, check the sample account credentials and session support in the browser

## Recommended Checklist
- [ ] XAMPP installed and running
- [ ] `web_project` folder copied to `C:\xampp\htdocs\`
- [ ] `db_boutique` created in phpMyAdmin
- [ ] `project.sql` imported successfully
- [ ] Database credentials checked in `config/db.php`
- [ ] Customer and admin login tested
- [ ] Cart and order flow verified

---

Thank you for reviewing the Stylish Boutique web application. This README now includes detailed setup, usage, database details, sample accounts, and deployment steps for a complete project submission.
