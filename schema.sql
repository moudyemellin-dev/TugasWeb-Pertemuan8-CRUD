DROP DATABASE IF EXISTS inventaris_db;

CREATE DATABASE inventaris_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE inventaris_db;


-- =========================================
-- 1. TABEL CATEGORIES
-- =========================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;


-- =========================================
-- 2. TABEL SUPPLIERS
-- =========================================
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL
) ENGINE=InnoDB;


-- =========================================
-- 3. TABEL PRODUCTS
-- =========================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    supplier_id INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,

    CONSTRAINT fk_product_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_product_supplier
        FOREIGN KEY (supplier_id)
        REFERENCES suppliers(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;


-- =========================================
-- 4. TABEL ACTIVITY LOGS
-- Bonus: log aktivitas saat delete
-- =========================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NULL,
    action VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_log_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- =========================================
-- SEED CATEGORIES
-- Minimal 5 data
-- =========================================
INSERT INTO categories (name) VALUES
('Elektronik'),
('Aksesoris'),
('Peralatan Kantor'),
('Jaringan'),
('Penyimpanan');


-- =========================================
-- SEED SUPPLIERS
-- Minimal 5 data
-- =========================================
INSERT INTO suppliers (name, phone) VALUES
('PT Teknologi Nusantara', '081234567801'),
('CV Digital Mandiri', '081234567802'),
('PT Sumber Komputer', '081234567803'),
('CV Jaringan Indonesia', '081234567804'),
('PT Data Sejahtera', '081234567805');


-- =========================================
-- SEED PRODUCTS
-- Minimal 5 data
-- =========================================
INSERT INTO products
(name, category_id, supplier_id, price, stock)
VALUES
('Laptop ASUS', 1, 1, 8500000, 10),
('Mouse Wireless', 2, 2, 150000, 25),
('Keyboard Mechanical', 2, 3, 450000, 15),
('Router WiFi', 4, 4, 650000, 12),
('SSD 1TB', 5, 5, 1200000, 20),
('Monitor 24 Inch', 1, 1, 1800000, 8),
('Printer Inkjet', 3, 3, 2100000, 6),
('Flashdisk 64GB', 5, 5, 120000, 30),
('LAN Cable 10 Meter', 4, 4, 85000, 40),
('Webcam Full HD', 2, 2, 350000, 18);


-- =========================================
-- SEED ACTIVITY LOGS
-- =========================================
INSERT INTO activity_logs
(product_id, action, description)
VALUES
(1, 'CREATE', 'Data awal Laptop ASUS ditambahkan'),
(2, 'CREATE', 'Data awal Mouse Wireless ditambahkan'),
(3, 'CREATE', 'Data awal Keyboard Mechanical ditambahkan'),
(4, 'CREATE', 'Data awal Router WiFi ditambahkan'),
(5, 'CREATE', 'Data awal SSD 1TB ditambahkan');