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
