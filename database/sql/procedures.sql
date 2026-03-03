-- Stored Procedures for Orders App
-- Database: orders_db
-- Purpose: All database operations must go through stored procedures

USE orders_db;

DROP PROCEDURE IF EXISTS sp_get_products;
DROP PROCEDURE IF EXISTS sp_get_product_by_id;
DROP PROCEDURE IF EXISTS sp_create_product;
DROP PROCEDURE IF EXISTS sp_update_product;
DROP PROCEDURE IF EXISTS sp_delete_product;
DROP PROCEDURE IF EXISTS sp_search_products;
DROP PROCEDURE IF EXISTS sp_create_order;
DROP PROCEDURE IF EXISTS sp_get_orders;
DROP PROCEDURE IF EXISTS sp_get_order_by_id;
DROP PROCEDURE IF EXISTS sp_check_product_stock;
DROP PROCEDURE IF EXISTS sp_update_product_stock;

-- ==========================================
-- PRODUCTS CATALOG PROCEDURES
-- ==========================================

DELIMITER //
CREATE PROCEDURE sp_get_products(
    IN p_search VARCHAR(255),
    IN p_sort_by VARCHAR(50),
    IN p_sort_order VARCHAR(4),
    IN p_page INT,
    IN p_per_page INT
)
BEGIN
    DECLARE v_offset INT;
    SET v_offset = (p_page - 1) * p_per_page;

    -- Set default sort
    SET p_sort_by = IFNULL(p_sort_by, 'id');
    SET p_sort_order = IFNULL(p_sort_order, 'ASC');
    SET p_search = IFNULL(p_search, '');

    -- Get total count
SELECT COUNT(*) as total
FROM products_catalog
WHERE (p_search = '' OR name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR sku COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci);

-- Get products
IF p_sort_by = 'name' THEN
        IF p_sort_order = 'DESC' THEN
SELECT id, sku, name, price, stock, created_at, updated_at
FROM products_catalog
WHERE (p_search = '' OR name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR sku COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci)
ORDER BY name DESC
    LIMIT p_per_page OFFSET v_offset;
ELSE
SELECT id, sku, name, price, stock, created_at, updated_at
FROM products_catalog
WHERE (p_search = '' OR name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR sku COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci)
ORDER BY name ASC
    LIMIT p_per_page OFFSET v_offset;
END IF;
    ELSEIF p_sort_by = 'price' THEN
        IF p_sort_order = 'DESC' THEN
SELECT id, sku, name, price, stock, created_at, updated_at
FROM products_catalog
WHERE (p_search = '' OR name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR sku COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci)
ORDER BY price DESC
    LIMIT p_per_page OFFSET v_offset;
ELSE
SELECT id, sku, name, price, stock, created_at, updated_at
FROM products_catalog
WHERE (p_search = '' OR name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR sku COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci)
ORDER BY price ASC
    LIMIT p_per_page OFFSET v_offset;
END IF;
ELSE
        IF p_sort_order = 'DESC' THEN
SELECT id, sku, name, price, stock, created_at, updated_at
FROM products_catalog
WHERE (p_search = '' OR name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR sku COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci)
ORDER BY id DESC
    LIMIT p_per_page OFFSET v_offset;
ELSE
SELECT id, sku, name, price, stock, created_at, updated_at
FROM products_catalog
WHERE (p_search = '' OR name COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci OR sku COLLATE utf8mb4_unicode_ci LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci)
ORDER BY id ASC
    LIMIT p_per_page OFFSET v_offset;
END IF;
END IF;
END//
DELIMITER ;

-- Get product by ID
DELIMITER //
CREATE PROCEDURE sp_get_product_by_id(IN p_id BIGINT)
BEGIN
SELECT id, sku, name, price, stock, created_at, updated_at
FROM products_catalog
WHERE id = p_id;
END//
DELIMITER ;

-- Create new product
DELIMITER //
CREATE PROCEDURE sp_create_product(
    IN p_sku VARCHAR(255),
    IN p_name VARCHAR(255),
    IN p_price DECIMAL(10,2),
    IN p_stock INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
ROLLBACK;
RESIGNAL;
END;

START TRANSACTION;

INSERT INTO products_catalog (sku, name, price, stock, created_at, updated_at)
VALUES (p_sku, p_name, p_price, p_stock, NOW(), NOW());

SELECT LAST_INSERT_ID() as id;

COMMIT;
END//
DELIMITER ;

-- Update product
DELIMITER //
CREATE PROCEDURE sp_update_product(
    IN p_id BIGINT,
    IN p_sku VARCHAR(255),
    IN p_name VARCHAR(255),
    IN p_price DECIMAL(10,2),
    IN p_stock INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
ROLLBACK;
RESIGNAL;
END;

START TRANSACTION;

UPDATE products_catalog
SET sku = p_sku,
    name = p_name,
    price = p_price,
    stock = p_stock,
    updated_at = NOW()
WHERE id = p_id;

SELECT ROW_COUNT() as affected_rows;

COMMIT;
END//
DELIMITER ;

-- Delete product
DELIMITER //
CREATE PROCEDURE sp_delete_product(IN p_id BIGINT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
ROLLBACK;
RESIGNAL;
END;

START TRANSACTION;

DELETE FROM products_catalog WHERE id = p_id;

SELECT ROW_COUNT() as affected_rows;

COMMIT;
END//
DELIMITER ;

-- ==========================================
-- ORDERS PROCEDURES
-- ==========================================

-- Check product stock availability
DELIMITER //
CREATE PROCEDURE sp_check_product_stock(
    IN p_product_id BIGINT,
    IN p_quantity INT
)
BEGIN
SELECT
    id,
    sku,
    name,
    price,
    stock,
    CASE WHEN stock >= p_quantity THEN 1 ELSE 0 END as available
FROM products_catalog
WHERE id = p_product_id;
END//
DELIMITER ;

-- Update product stock (decrease)
DELIMITER //
CREATE PROCEDURE sp_update_product_stock(
    IN p_product_id BIGINT,
    IN p_quantity INT
)
BEGIN
UPDATE products_catalog
SET stock = stock - p_quantity,
    updated_at = NOW()
WHERE id = p_product_id AND stock >= p_quantity;

SELECT ROW_COUNT() as affected_rows;
END//
DELIMITER ;

-- Create order with items (transactional)
DELIMITER //
CREATE PROCEDURE sp_create_order(
    IN p_user_id BIGINT,
    IN p_items JSON
)
BEGIN
    DECLARE v_order_id BIGINT;
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_discount DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_tax DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_total DECIMAL(10,2) DEFAULT 0.00;
    DECLARE v_item_count INT;
    DECLARE v_counter INT DEFAULT 0;
    DECLARE v_product_id BIGINT;
    DECLARE v_quantity INT;
    DECLARE v_unit_price DECIMAL(10,2);
    DECLARE v_item_subtotal DECIMAL(10,2);
    DECLARE v_stock INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
ROLLBACK;
RESIGNAL;
END;

START TRANSACTION;

-- Get number of items
SET v_item_count = JSON_LENGTH(p_items);

    -- Calculate subtotal and validate stock
    WHILE v_counter < v_item_count DO
        SET v_product_id = JSON_EXTRACT(p_items, CONCAT('$[', v_counter, '].product_id'));
        SET v_quantity = JSON_EXTRACT(p_items, CONCAT('$[', v_counter, '].quantity'));

        -- Get product price and stock
SELECT price, stock INTO v_unit_price, v_stock
FROM products_catalog
WHERE id = v_product_id
    FOR UPDATE; -- Lock row

-- Check stock availability
IF v_stock < v_quantity THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Insufficient stock for product';
END IF;

        -- Calculate item subtotal
        SET v_item_subtotal = v_unit_price * v_quantity;
        SET v_subtotal = v_subtotal + v_item_subtotal;

        SET v_counter = v_counter + 1;
END WHILE;

    -- Apply discount if subtotal > 100
    IF v_subtotal > 100 THEN
        SET v_discount = v_subtotal * 0.10;
END IF;

    -- Calculate tax (12% IVA on subtotal after discount)
    SET v_tax = (v_subtotal - v_discount) * 0.12;

    -- Calculate total
    SET v_total = v_subtotal - v_discount + v_tax;

    -- Create order
INSERT INTO orders (user_id, subtotal, discount, tax, total, status, created_at, updated_at)
VALUES (p_user_id, v_subtotal, v_discount, v_tax, v_total, 'pending', NOW(), NOW());

SET v_order_id = LAST_INSERT_ID();

    -- Insert order items and update stock
    SET v_counter = 0;
    WHILE v_counter < v_item_count DO
        SET v_product_id = JSON_EXTRACT(p_items, CONCAT('$[', v_counter, '].product_id'));
        SET v_quantity = JSON_EXTRACT(p_items, CONCAT('$[', v_counter, '].quantity'));

        -- Get product price again
SELECT price INTO v_unit_price
FROM products_catalog
WHERE id = v_product_id;

SET v_item_subtotal = v_unit_price * v_quantity;

        -- Insert order item
INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
VALUES (v_order_id, v_product_id, v_quantity, v_unit_price, v_item_subtotal, NOW(), NOW());

-- Update product stock
UPDATE products_catalog
SET stock = stock - v_quantity,
    updated_at = NOW()
WHERE id = v_product_id;

SET v_counter = v_counter + 1;
END WHILE;

COMMIT;

-- Return created order
SELECT v_order_id as order_id;
END//
DELIMITER ;

-- Get orders with filters
DELIMITER //
CREATE PROCEDURE sp_get_orders(
    IN p_date_from DATE,
    IN p_date_to DATE,
    IN p_min_total DECIMAL(10,2)
)
BEGIN
SELECT
    o.id,
    o.user_id,
    o.subtotal,
    o.discount,
    o.tax,
    o.total,
    o.status,
    o.created_at,
    o.updated_at,
    u.name as user_name,
    u.email as user_email
FROM orders o
         INNER JOIN users u ON o.user_id = u.id
WHERE
    (p_date_from IS NULL OR DATE(o.created_at) >= p_date_from)
        AND (p_date_to IS NULL OR DATE(o.created_at) <= p_date_to)
        AND (p_min_total IS NULL OR o.total >= p_min_total)
ORDER BY o.created_at DESC;
END//
DELIMITER ;

-- Get order by ID with items
DELIMITER //
CREATE PROCEDURE sp_get_order_by_id(IN p_order_id BIGINT)
BEGIN
    -- Get order header
SELECT
    o.id,
    o.user_id,
    o.subtotal,
    o.discount,
    o.tax,
    o.total,
    o.status,
    o.created_at,
    o.updated_at,
    u.name as user_name,
    u.email as user_email
FROM orders o
         INNER JOIN users u ON o.user_id = u.id
WHERE o.id = p_order_id;

-- Get order items
SELECT
    oi.id,
    oi.order_id,
    oi.product_id,
    oi.quantity,
    oi.unit_price,
    oi.subtotal,
    p.sku,
    p.name as product_name
FROM order_items oi
         INNER JOIN products_catalog p ON oi.product_id = p.id
WHERE oi.order_id = p_order_id;
END//
DELIMITER ;
