-- =========================================================
-- DATABASE INITIALIZATION LAYER
-- =========================================================
CREATE DATABASE IF NOT EXISTS kinetik_db;
USE kinetik_db;

-- Disable foreign key checks temporarily to ensure clean drop and regeneration
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS test_drives;
DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS vehicle_images;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. UNIFIED USERS TABLE (Segregates Customers and Admins via role columns)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('customer', 'admin') DEFAULT 'customer',
    verification_tier VARCHAR(50) DEFAULT 'Standard Buyer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. VEHICLES TABLE (Houses primary technical specifications and core display images)
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    category ENUM('sports', 'Super', 'muscle') NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    description TEXT,
    horsepower INT,
    top_speed_kmh INT,
    main_image VARCHAR(255) NOT NULL, -- e.g., assets/images/cars/aero_main.jpg
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. VEHICLE IMAGES TABLE (Enables carousel sliders for viewing different sides of the car)
CREATE TABLE vehicle_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL, -- e.g., assets/images/cars/aero_side.jpg
    caption VARCHAR(100),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. ORDERS TABLE (Tracks customer checkouts, amounts, status, and payment methods)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(12, 2) NOT NULL,
    status ENUM('Pending', 'Approved', 'In Transit', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    payment_method ENUM('Bank Transfer', 'Credit Card', 'Mobile Money', 'Crypto') DEFAULT 'Bank Transfer',
    shipping_address TEXT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. ORDER DETAILS / CART ITEMS TABLE (Junction mapping product IDs, order IDs, quantities, and locked prices)
CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    vehicle_id INT,
    quantity INT NOT NULL DEFAULT 1,
    price_at_purchase DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. TEST DRIVE BOOKINGS TABLE (Activates whenever a person schedules an on-track vehicle trial)
CREATE TABLE test_drives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status ENUM('Requested', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Requested',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================
-- SEED DATA CONFIGURATION (Conflict-Free Initial Values)
-- =========================================================

-- Populate User Ecosystem 
INSERT INTO users (full_name, email, password_hash, phone, role, verification_tier) VALUES 
('Malaz Ibrahim', 'malaz@kinetik.rw', '$2y$10$KinetikSecureHashExampleForCustomerPass', '+250 791 591 773', 'customer', 'Certified Luxury Buyer'),
('System Administrator', 'admin@kinetik.rw', '$2y$10$KinetikSecureHashExampleForAdminDashboard', '+250 790 000 000', 'admin', 'Fleet Commander');

-- Populate High-Performance Fleet Inventory
INSERT INTO vehicles (brand, model_name, category, price, description, horsepower, top_speed_kmh, main_image) VALUES 
('Lamborghini', 'Huracán', 'Super', 260000.00, 'A mid-engine masterwork blending an aggressive, sharp luxury design aesthetic with a high-revving, naturally aspirated 5.2-liter V10 engine.', 640, 325, 'assets/images/cars/huracan.png'),
('Ferrari', '488 GTB', 'sports', 280000.00, 'An Italian icon combining an aerodynamic, low-profile stance with a ferocious twin-turbocharged V8 engine engineered for track-level responsiveness.', 670, 330, 'assets/images/cars/ferrari-488.png'),
('McLaren', '720S', 'Super', 310000.00, 'A futuristic performance hypercar engineered around a revolutionary carbon fiber Monocage chassis and an advanced proactive chassis control suspension.', 720, 341, 'assets/images/cars/mclaren-720s.png'),
('Porsche', '911 Turbo S', 'sports', 230000.00, 'The definitive high-performance everyday supercar, delivering unparalleled dual-clutch acceleration, precision all-wheel drive, and twin-turbo tuning.', 650, 330, 'assets/images/cars/911-turbo-s.png');

-- Populate Extended Angle Images for Detailed Lookups
INSERT INTO vehicle_images (vehicle_id, image_path, caption) VALUES 
(1, 'assets/images/cars/aero_side.jpg', 'Aerodynamic Side Profile Elevation'),
(1, 'assets/images/cars/aero_rear.jpg', 'Rear Active Diffuser Assembly View'),
(2, 'assets/images/cars/gt_interior.jpg', 'Handcrafted Italian Leather Cockpit Matrix'),
(3, 'assets/images/cars/nurburg_track.jpg', 'Apex Stability Testing at the Nordschleife');

-- Populate Baseline Initial Order
INSERT INTO orders (customer_id, total_amount, status, payment_method, shipping_address) VALUES 
(1, 185000.00, 'In Transit', 'Bank Transfer', 'Nyarutarama Estate, Villa 14, Kigali, Rwanda');

-- Map Order Elements Junction Link
INSERT INTO order_details (order_id, vehicle_id, quantity, price_at_purchase) VALUES 
(1, 2, 1, 185000.00);

-- Populate Sample Test Drive Entry
INSERT INTO test_drives (customer_id, vehicle_id, booking_date, booking_time, status) VALUES 
(1, 1, '2026-06-15', '14:00:00', 'Confirmed');

-- =========================================================
-- LIGHTWEIGHT SINGLE VEHICLE TESTING SEED (Lamborghini Only)
-- =========================================================


