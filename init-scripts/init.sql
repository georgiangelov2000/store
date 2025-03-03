-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    status INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    special_quantity INT DEFAULT NULL,
    special_price DECIMAL(10,2) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT DEFAULT NULL,
    product_id INT DEFAULT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);


-- Insert initial product data only if it does not exist
INSERT INTO products (sku, name, unit_price, special_quantity, special_price)
SELECT * FROM (SELECT 'A', 'Product A', 50.00, 3, 130.00) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM products WHERE sku = 'A') LIMIT 1;

INSERT INTO products (sku, name, unit_price, special_quantity, special_price)
SELECT * FROM (SELECT 'B', 'Product B', 30.00, 2, 45.00) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM products WHERE sku = 'B') LIMIT 1;

INSERT INTO products (sku, name, unit_price, special_quantity, special_price)
SELECT 'C', 'Product C', 20.00, NULL, NULL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE sku = 'C');

INSERT INTO products (sku, name, unit_price, special_quantity, special_price)
SELECT 'D', 'Product D', 10.00, NULL, NULL
WHERE NOT EXISTS (SELECT 1 FROM products WHERE sku = 'D');