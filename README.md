# BINDING HOST TO AN INTERNAL DOCKER CONTAINER APPLICATION WITH NGINX

## Overview
This guide explains how to configure Nginx to bind a host machine to an internal Docker container running a web application.

## Nginx Configuration
The following configuration sets up Nginx as a reverse proxy to route traffic from the host machine to an internal application running on port `8000`.

### Configuration File: `/etc/nginx/nginx.conf`
```nginx
user www-data;
worker_processes auto;

# Events configuration
events {
  worker_connections 1024;
}

# HTTP server configuration
http {
  server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
    
    location / {
      proxy_pass http://127.0.0.1:8000; # Proxy requests to the internal application
      proxy_set_header Host $host;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
      proxy_set_header X-Forwarded-Proto $scheme;
    }
  }
}
```

## Steps to Apply the Configuration
1. **Update the Nginx configuration file**:
   - Save the above configuration in `/etc/nginx/nginx.conf`.

2. **Restart Nginx to apply changes**:
   ```sh
   sudo systemctl restart nginx
   ```

3. **Ensure the Docker container is running**:
   ```sh
   docker ps
   ```
   - The application should be exposed on port `8000` inside the container.

## 4. Docker Containers
The following containers are running as part of this setup:

```
CONTAINER ID   IMAGE          COMMAND                  CREATED          STATUS                    PORTS                    NAMES
e45ad64893e6   nginx:latest   "/docker-entrypoint.…"   21 minutes ago   Up 20 minutes (healthy)   127.0.0.1:8000->80/tcp   nginx_server
2427af4efd50   store-app      "docker-php-entrypoi…"   21 minutes ago   Up 20 minutes (healthy)   9000/tcp                 php
f9bb83a35b90   mysql:latest   "docker-entrypoint.s…"   21 minutes ago   Up 21 minutes (healthy)   3306/tcp, 33060/tcp      mysql_db
```
## 5. Database Diagram

Here is the database structure used in this project:

### `order_items` Table
```plaintext
+------------+---------------+------+-----+-------------------+-------------------+
| Field      | Type          | Null | Key | Default           | Extra             |
+------------+---------------+------+-----+-------------------+-------------------+
| id         | int           | NO   | PRI | NULL              | auto_increment    |
| order_id   | int           | YES  | MUL | NULL              |                   |
| product_id | int           | YES  | MUL | NULL              |                   |
| quantity   | int           | NO   |     | NULL              |                   |
| price      | decimal(10,2) | NO   |     | NULL              |                   |
| created_at | datetime      | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at | datetime      | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+------------+---------------+------+-----+-------------------+-------------------+
```

### `orders` Table
```plaintext
+-------------+---------------+------+-----+-------------------+-------------------+
| Field       | Type          | Null | Key | Default           | Extra             |
+-------------+---------------+------+-----+-------------------+-------------------+
| id          | int           | NO   | PRI | NULL              | auto_increment    |
| status      | int           | NO   |     | 1                 |                   |
| total_price | decimal(10,2) | NO   |     | 0.00              |                   |
| created_at  | datetime      | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at  | datetime      | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+-------------+---------------+------+-----+-------------------+-------------------+
```
**Order Statuses:**
```php
public const STATUS_CREATED = 1;
public const STATUS_COMPLETED = 2;
public const STATUS_CANCELED = 3;
```

### `products` Table
```plaintext
+------------------+---------------+------+-----+-------------------+-------------------+
| Field            | Type          | Null | Key | Default           | Extra             |
+------------------+---------------+------+-----+-------------------+-------------------+
| id               | int           | NO   | PRI | NULL              | auto_increment    |
| sku              | varchar(10)   | NO   | UNI | NULL              |                   |
| name             | varchar(255)  | NO   |     | NULL              |                   |
| unit_price       | decimal(10,2) | NO   |     | NULL              |                   |
| special_quantity | int           | YES  |     | NULL              |                   |
| special_price    | decimal(10,2) | YES  |     | NULL              |                   |
| created_at       | datetime      | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
| updated_at       | datetime      | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED |
+------------------+---------------+------+-----+-------------------+-------------------+
```

## 6. API Routes

The application exposes the following API endpoints:

```yaml
api_order_create:
    path: /api/v1/orders/create
    controller: App\Controller\API\OrderController::createOrder
    methods: [POST]

api_order_get:
    path: /api/v1/orders/{id}
    controller: App\Controller\API\OrderController::getOrder
    methods: [GET]

api_order_list:
    path: /api/v1/orders
    controller: App\Controller\API\OrderController::getOrders
    methods: [GET]

api_order_items_list:
    path: /api/v1/orders/{id}/items
    controller: App\Controller\API\OrderController::getItems
    methods: [GET]

api_order_update_status:
    path: /api/v1/orders/{id}/update-status
    controller: App\Controller\API\OrderController::updateOrderStatus
    methods: [PUT]

api_order_delete:
    path: /api/v1/orders/{id}/delete
    controller: App\Controller\API\OrderController::deleteOrder
    methods: [DELETE]

api_product_list:
    path: /api/v1/products
    controller: App\Controller\API\ProductController::listProducts
    methods: [GET]

home:
    path: /
    controller: App\Controller\HomeController::index

products:
    path: /products
    controller: App\Controller\ProductController::index
```


## 7. Project Structure

### `src/Builders/API/`
Contains builders for constructing API-related data:
- `BaseBuilder.php` – Base builder class.
- `OrderBuilder.php` – Builds order-related data.
- `ProductBuilder.php` – Builds product-related data.

### `src/Controller/`
Handles HTTP requests:
- `HomeController.php` – Manages the home page.
- `ProductController.php` – Handles product-related views.

### `src/Controller/API/`
Manages API endpoints:
- `OrderController.php` – Handles order-related API operations.
- `ProductController.php` – Handles product-related API operations.

### `src/DataFixtures/`
Contains seed data for testing:
- `ProductFixtures.php` – Seeds the database with product data.

### `src/Entity/`
Defines database entities:
- `Order.php` – Represents orders.
- `OrderItem.php` – Represents items within an order.
- `Product.php` – Represents products.

### `src/Repository/`
Handles database queries:
- `OrderRepository.php` – Fetches order data.
- `ProductRepository.php` – Fetches product data.

### `src/Requests/`
Defines request validation rules:
- `CreateOrderRequest.php` – Validates order creation.
- `GetOrderRequest.php` – Validates order retrieval.
- `UpdateOrderStatusRequest.php` – Validates order status updates.
