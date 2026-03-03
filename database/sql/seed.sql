-- Seed data for Orders App
-- Database: orders_db
-- Author: Orders App Team
-- Date: 2026-03-02

USE orders_db;

-- Seed users
INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at)
VALUES ('Admin User', 'admin@ordersapp.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        NOW(), NOW()),
       ('John Doe', 'john@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(),
        NOW()),
       ('Jane Smith', 'jane@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(),
        NOW());

-- Seed products
INSERT INTO products_catalog (sku, name, price, stock, created_at, updated_at)
VALUES ('LAPTOP-001', 'Laptop HP Pavilion 15', 899.99, 50, NOW(), NOW()),
       ('LAPTOP-002', 'MacBook Air M2', 1299.99, 30, NOW(), NOW()),
       ('MOUSE-001', 'Logitech MX Master 3', 99.99, 100, NOW(), NOW()),
       ('KEYBOARD-001', 'Mechanical Keyboard RGB', 149.99, 75, NOW(), NOW()),
       ('MONITOR-001', 'Dell UltraSharp 27"', 449.99, 40, NOW(), NOW()),
       ('HEADSET-001', 'Sony WH-1000XM5', 349.99, 60, NOW(), NOW()),
       ('WEBCAM-001', 'Logitech C920', 79.99, 120, NOW(), NOW()),
       ('TABLET-001', 'iPad Air 5th Gen', 599.99, 25, NOW(), NOW()),
       ('PHONE-001', 'iPhone 14 Pro', 1099.99, 20, NOW(), NOW()),
       ('DOCK-001', 'USB-C Docking Station', 199.99, 45, NOW(), NOW()),
       ('CABLE-001', 'USB-C Cable 2m', 19.99, 200, NOW(), NOW()),
       ('CHARGER-001', '65W USB-C Charger', 49.99, 150, NOW(), NOW()),
       ('BAG-001', 'Laptop Backpack', 79.99, 80, NOW(), NOW()),
       ('MOUSE-002', 'Wireless Mouse Basic', 29.99, 180, NOW(), NOW()),
       ('KEYBOARD-002', 'Wireless Keyboard Slim', 59.99, 140, NOW(), NOW());

-- Seed sample orders (for testing queries)
INSERT INTO orders (user_id, subtotal, discount, tax, total, status, created_at, updated_at)
VALUES (2, 899.99, 0.00, 107.99, 1007.98, 'completed', '2026-02-15 10:30:00', '2026-02-15 10:30:00'),
       (2, 249.98, 24.99, 27.00, 251.99, 'completed', '2026-02-20 14:45:00', '2026-02-20 14:45:00'),
       (3, 1299.99, 0.00, 155.99, 1455.98, 'pending', '2026-03-01 09:15:00', '2026-03-01 09:15:00'),
       (3, 129.98, 12.99, 14.04, 131.03, 'completed', '2026-02-28 16:20:00', '2026-02-28 16:20:00');

-- Seed order items
INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
VALUES
-- Order 1
(1, 1, 1, 899.99, 899.99, '2026-02-15 10:30:00', '2026-02-15 10:30:00'),
-- Order 2
(2, 3, 1, 99.99, 99.99, '2026-02-20 14:45:00', '2026-02-20 14:45:00'),
(2, 4, 1, 149.99, 149.99, '2026-02-20 14:45:00', '2026-02-20 14:45:00'),
-- Order 3
(3, 2, 1, 1299.99, 1299.99, '2026-03-01 09:15:00', '2026-03-01 09:15:00'),
-- Order 4
(4, 14, 2, 29.99, 59.98, '2026-02-28 16:20:00', '2026-02-28 16:20:00'),
(4, 15, 1, 59.99, 59.99, '2026-02-28 16:20:00', '2026-02-28 16:20:00'),
(4, 11, 1, 19.99, 19.99, '2026-02-28 16:20:00', '2026-02-28 16:20:00');
