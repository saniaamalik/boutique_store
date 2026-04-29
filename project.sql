
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ================= USERS =================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  phone VARCHAR(20),
  role ENUM('admin','customer') DEFAULT 'customer',
  address TEXT
);

INSERT INTO users (id, name, email, password, phone, role, address) VALUES
(4, 'admin', 'admin@gmail.com', '$2y$10$IkeP1lW4p8kfWXmAtvabh.y.S4h/gMsM3yLI6B8kUbjWOp5wH1rxq', '03001234567', 'admin', 'Lahore market 123'),
(5, 'sania', 'sania@gmail.com', '$2y$10$Cc52nGcqYidjvBMeQUr7P.pokdN5ecCnUF5qBCO3J5HPq5alO8m/m', '03101234567', 'customer', NULL);

-- ================= CATEGORIES =================
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (id, name, description) VALUES
(1, 'party wear', NULL),
(2, 'lawn suits', NULL);

-- ================= PRODUCTS =================
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

INSERT INTO products (id, category_id, name, description, price, stock, image) VALUES
(1, 1, 'embriodery party suit', 'full embriodery fancy suits for parties.', 5000.00, 0, '1777192038_7709.jpg'),
(2, 2, 'simple lawn suit', 'summer collection', 3000.00, 0, '1777192103_7842.webp');

-- ================= CART =================
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT DEFAULT 1
);

-- ================= ORDERS =================
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  total_amount DECIMAL(10,2) DEFAULT 0.00,
  status VARCHAR(50) DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  payment_method VARCHAR(50) DEFAULT 'COD',
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO orders (id, user_id, total_amount, status, payment_method) VALUES
(1, 4, 3000.00, 'Processing', 'COD');

-- ================= ORDER ITEMS =================
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  quantity INT DEFAULT 1,
  price DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO order_items (id, order_id, product_id, quantity, price) VALUES
(1, 1, 2, 1, 3000.00);