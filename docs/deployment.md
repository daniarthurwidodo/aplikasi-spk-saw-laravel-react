# 🚀 Deployment Guide

**Project:** SPK-SAWh Production Deployment  
**Target Audience:** DevOps, System Administrators  
**Last Updated:** November 8, 2025  

## 🎯 Deployment Overview

This guide covers deploying SPK-SAWh to production environments using modern containerized infrastructure with Docker, PostgreSQL, MinIO, and Redis.

## 🏗️ Infrastructure Requirements

### Server Specifications

#### Minimum Requirements
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 50GB SSD
- **Network**: 100 Mbps

#### Recommended (Production)
- **CPU**: 4 cores
- **RAM**: 8GB
- **Storage**: 100GB SSD + 500GB for file storage
- **Network**: 1 Gbps

### Software Stack
- **OS**: Ubuntu 22.04 LTS / CentOS 8 / Debian 11
- **Container**: Docker 24+ / Podman 4+
- **Web Server**: Nginx 1.22+
- **Database**: PostgreSQL 16
- **Cache**: Redis 7+
- **Storage**: MinIO (S3-compatible)

## 🐳 Docker Deployment

### Production Docker Compose
```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile.prod
    container_name: spksaw-app
    restart: unless-stopped
    env_file:
      - .env.production
    volumes:
      - ./storage:/var/www/storage
      - ./bootstrap/cache:/var/www/bootstrap/cache
    depends_on:
      - database
      - redis
      - minio
    networks:
      - spksaw-network

  nginx:
    image: nginx:alpine
    container_name: spksaw-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./nginx/sites:/etc/nginx/sites-available
      - ./ssl:/etc/nginx/ssl
      - ./public:/var/www/public
    depends_on:
      - app
    networks:
      - spksaw-network

  database:
    image: postgres:16-alpine
    container_name: spksaw-postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: ${DB_DATABASE}
      POSTGRES_USER: ${DB_USERNAME}
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - postgres_data:/var/lib/postgresql/data
      - ./backup:/backup
    networks:
      - spksaw-network

  redis:
    image: redis:7-alpine
    container_name: spksaw-redis
    restart: unless-stopped
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}
    volumes:
      - redis_data:/data
    networks:
      - spksaw-network

  minio:
    image: minio/minio:latest
    container_name: spksaw-minio
    restart: unless-stopped
    environment:
      MINIO_ROOT_USER: ${MINIO_ACCESS_KEY}
      MINIO_ROOT_PASSWORD: ${MINIO_SECRET_KEY}
    command: server /data --console-address ":9001"
    volumes:
      - minio_data:/data
    ports:
      - "9000:9000"
      - "9001:9001"
    networks:
      - spksaw-network

  queue:
    build:
      context: .
      dockerfile: Dockerfile.prod
    container_name: spksaw-queue
    restart: unless-stopped
    command: php artisan queue:work --sleep=3 --tries=3
    env_file:
      - .env.production
    volumes:
      - ./storage:/var/www/storage
    depends_on:
      - database
      - redis
    networks:
      - spksaw-network

  scheduler:
    build:
      context: .
      dockerfile: Dockerfile.prod
    container_name: spksaw-scheduler
    restart: unless-stopped
    command: sh -c "while true; do php artisan schedule:run; sleep 60; done"
    env_file:
      - .env.production
    depends_on:
      - database
    networks:
      - spksaw-network

volumes:
  postgres_data:
  redis_data:
  minio_data:

networks:
  spksaw-network:
    driver: bridge
```

### Production Dockerfile
```dockerfile
# Dockerfile.prod
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    redis \
    oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring xml gd

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js and build assets
RUN apk add --no-cache nodejs npm
RUN npm ci --only=production
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
```

## 🔧 Environment Configuration

### Production Environment (.env.production)
```env
# Application
APP_NAME="SPK-SAWh"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://spksaw.app

# Database
DB_CONNECTION=pgsql
DB_HOST=database
DB_PORT=5432
DB_DATABASE=spksaw_prod
DB_USERNAME=spksaw_user
DB_PASSWORD=secure_database_password

# JWT Configuration
JWT_SECRET=your-jwt-secret-key
JWT_TTL=60
JWT_REFRESH_TTL=10080

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=secure_redis_password
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# MinIO/S3 Storage
MINIO_ENDPOINT=http://minio:9000
MINIO_ACCESS_KEY=your_access_key
MINIO_SECRET_KEY=your_secret_key
MINIO_BUCKET=spksaw-uploads

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
```

## 🌐 Nginx Configuration

### Main Nginx Config
```nginx
# nginx/nginx.conf
user nginx;
worker_processes auto;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
    use epoll;
    multi_accept on;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';

    access_log /var/log/nginx/access.log main;
    error_log /var/log/nginx/error.log warn;

    # Performance
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    include /etc/nginx/sites-available/*;
}
```

### Site Configuration
```nginx
# nginx/sites/spksaw.conf
server {
    listen 80;
    server_name spksaw.app www.spksaw.app;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name spksaw.app www.spksaw.app;
    root /var/www/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/nginx/ssl/spksaw.app.crt;
    ssl_certificate_key /etc/nginx/ssl/spksaw.app.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security
    client_max_body_size 50M;
    
    # Logging
    access_log /var/log/nginx/spksaw_access.log;
    error_log /var/log/nginx/spksaw_error.log;

    # Static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Laravel application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_read_timeout 300;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ /(vendor|storage|bootstrap|tests|node_modules) {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```

## 🚀 Deployment Steps

### 1. Server Preparation
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Create application directory
sudo mkdir -p /opt/spksaw
sudo chown $USER:$USER /opt/spksaw
```

### 2. Code Deployment
```bash
# Clone repository
cd /opt/spksaw
git clone https://github.com/daniarthurwidodo/aplikasi-spk-saw-laravel-react.git .

# Set up environment
cp .env.example .env.production
# Edit .env.production with production values

# Generate application key
docker run --rm -v $(pwd):/app composer:latest composer install --no-dev
docker run --rm -v $(pwd):/app composer:latest php artisan key:generate
```

### 3. SSL Certificate Setup
```bash
# Using Let's Encrypt with Certbot
sudo apt install certbot python3-certbot-nginx -y

# Generate certificate
sudo certbot certonly --nginx -d spksaw.app -d www.spksaw.app

# Copy certificates to nginx directory
sudo mkdir -p ./ssl
sudo cp /etc/letsencrypt/live/spksaw.app/fullchain.pem ./ssl/spksaw.app.crt
sudo cp /etc/letsencrypt/live/spksaw.app/privkey.pem ./ssl/spksaw.app.key
```

### 4. Container Deployment
```bash
# Build and start containers
docker-compose -f docker-compose.prod.yml up -d

# Run database migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Seed initial data
docker-compose -f docker-compose.prod.yml exec app php artisan db:seed --force

# Clear and cache configurations
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
```

### 5. MinIO Setup
```bash
# Access MinIO console
# URL: https://your-domain:9001
# Login with MINIO_ROOT_USER and MINIO_ROOT_PASSWORD

# Create buckets via CLI
docker-compose -f docker-compose.prod.yml exec minio mc alias set local http://localhost:9000 $MINIO_ROOT_USER $MINIO_ROOT_PASSWORD
docker-compose -f docker-compose.prod.yml exec minio mc mb local/spksaw-uploads
docker-compose -f docker-compose.prod.yml exec minio mc mb local/spksaw-thumbnails
```

## 📊 Monitoring & Logging

### Log Management
```bash
# View application logs
docker-compose -f docker-compose.prod.yml logs -f app

# View specific service logs
docker-compose -f docker-compose.prod.yml logs -f nginx
docker-compose -f docker-compose.prod.yml logs -f database

# Laravel logs
docker-compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log
```

### Health Checks
```bash
# Create health check script
#!/bin/bash
# scripts/health-check.sh

# Check application health
curl -f http://localhost/api/health || exit 1

# Check database connection
docker-compose -f docker-compose.prod.yml exec database pg_isready -U $DB_USERNAME || exit 1

# Check Redis
docker-compose -f docker-compose.prod.yml exec redis redis-cli ping || exit 1
```

### Monitoring Stack (Optional)
```yaml
# monitoring/docker-compose.monitoring.yml
version: '3.8'

services:
  prometheus:
    image: prom/prometheus
    ports:
      - "9090:9090"
    volumes:
      - ./prometheus.yml:/etc/prometheus/prometheus.yml

  grafana:
    image: grafana/grafana
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
    volumes:
      - grafana_data:/var/lib/grafana

volumes:
  grafana_data:
```

## 🔄 Backup & Recovery

### Database Backup
```bash
# Automated backup script
#!/bin/bash
# scripts/backup-database.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/opt/spksaw/backup"
BACKUP_FILE="spksaw_backup_$DATE.sql"

# Create backup
docker-compose -f docker-compose.prod.yml exec -T database pg_dump -U $DB_USERNAME $DB_DATABASE > $BACKUP_DIR/$BACKUP_FILE

# Compress backup
gzip $BACKUP_DIR/$BACKUP_FILE

# Upload to cloud storage (optional)
# aws s3 cp $BACKUP_DIR/$BACKUP_FILE.gz s3://your-backup-bucket/

# Cleanup old backups (keep 30 days)
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete
```

### File Storage Backup
```bash
# Backup MinIO data
#!/bin/bash
# scripts/backup-storage.sh

DATE=$(date +%Y%m%d_%H%M%S)
tar -czf /opt/spksaw/backup/storage_$DATE.tar.gz -C /opt/spksaw minio_data/
```

## 🔧 Maintenance & Updates

### Application Updates
```bash
# Update deployment script
#!/bin/bash
# scripts/deploy-update.sh

# Pull latest code
git pull origin main

# Build new images
docker-compose -f docker-compose.prod.yml build

# Stop containers
docker-compose -f docker-compose.prod.yml down

# Start containers
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Clear caches
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
```

### System Maintenance
```bash
# Crontab for automated tasks
# crontab -e

# Daily database backup at 2 AM
0 2 * * * /opt/spksaw/scripts/backup-database.sh

# Weekly storage backup at 3 AM Sunday
0 3 * * 0 /opt/spksaw/scripts/backup-storage.sh

# Certificate renewal check monthly
0 0 1 * * certbot renew --quiet

# Clean Docker system monthly
0 4 1 * * docker system prune -f
```

## 🚨 Security Hardening

### Firewall Configuration
```bash
# UFW firewall setup
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Security Updates
```bash
# Enable automatic security updates
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure -plow unattended-upgrades
```

## 📈 Performance Tuning

### PostgreSQL Optimization
```sql
-- postgresql.conf optimizations
shared_buffers = 256MB
effective_cache_size = 1GB
maintenance_work_mem = 64MB
checkpoint_completion_target = 0.9
wal_buffers = 16MB
default_statistics_target = 100
random_page_cost = 1.1
effective_io_concurrency = 200
```

### Redis Optimization
```conf
# redis.conf optimizations
maxmemory 512mb
maxmemory-policy allkeys-lru
tcp-keepalive 300
timeout 0
```

---

**Production Checklist:**
- [ ] SSL certificates configured
- [ ] Database backups automated
- [ ] Monitoring setup
- [ ] Security hardening applied
- [ ] Performance optimization done
- [ ] Documentation updated

---

**Previous:** [Development Guide](development.md)  
**Back to:** [Documentation Home](../README.md)