-- remember. truncate deletes entire tables and resets the numbering to 1.

CREATE DATABASE IF NOT EXISTS bookit;
USE bookit;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(10) NOT NULL DEFAULT 'user'
);

CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  author VARCHAR(100) NOT NULL,
  genre VARCHAR(50) NOT NULL,
  description TEXT,
  price INT NOT NULL,
  stock INT NOT NULL,
  image VARCHAR(255) DEFAULT ''
);

CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  qty INT NOT NULL
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total INT NOT NULL,
  payment_method VARCHAR(30) NOT NULL,
  proof VARCHAR(255) DEFAULT '',
  status VARCHAR(20) NOT NULL DEFAULT 'Processing',
  address VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  book_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  qty INT NOT NULL,
  price INT NOT NULL
);

-- users (admin account: admin@gmail.com / password)
INSERT INTO users (id, name, email, password, role) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'mike', 'mike@gmail.com', '$2y$10$yoxiMIEnq6Na5o3qFMZloujQeYmHV6.IfI5zoyR225QczYOQIlAaC', 'user'),
(3, 'John Book', 'iheartbook@gmail.com', '$2y$10$U2dJWoAHv0kDvH3Hpim6luGeZ17KsJ9OUt513WtpgZlold6fxbG.W', 'user');

-- books
INSERT INTO books (id, title, author, genre, description, price, stock, image) VALUES
(1, 'Made of Stars', 'Hana Krisviana', 'Novel', 'Milo and Bree type shii', 85000, 11, 'book_1793893482.jpg'),
(2, 'Omniscient Reader\'s Viewpoint', 'SingNSong', 'Action', 'kim dokja, stop dyin yo', 110000, 0, 'book_1793893380.jpg'),
(3, 'Atomic Habits', 'James Clear', 'Self-Help', 'Buku tentang larping feminisme dan progressiveness sambil menggunakan keychain labubu dengan matcha dubai latte', 120000, 65, 'book_1793893230.jpg');