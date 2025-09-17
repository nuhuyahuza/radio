#!/bin/bash

# Zaa Radio Production Deployment Script
# This script handles the deployment of the Zaa Radio application to production

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="zaa-radio"
APP_DIR="/var/www/zaa-radio"
BACKUP_DIR="/var/backups/zaa-radio"
LOG_FILE="/var/log/zaa-radio/deploy.log"

# Functions
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}" | tee -a $LOG_FILE
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}" | tee -a $LOG_FILE
    exit 1
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}" | tee -a $LOG_FILE
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    error "Please run as root"
fi

# Create necessary directories
log "Creating necessary directories..."
mkdir -p $APP_DIR
mkdir -p $BACKUP_DIR
mkdir -p /var/log/zaa-radio
mkdir -p /etc/nginx/ssl

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    error "Docker is not installed. Please install Docker first."
fi

if ! command -v docker-compose &> /dev/null; then
    error "Docker Compose is not installed. Please install Docker Compose first."
fi

# Check if .env.production exists
if [ ! -f ".env.production" ]; then
    error ".env.production file not found. Please create it first."
fi

# Backup current deployment
if [ -d "$APP_DIR" ] && [ "$(ls -A $APP_DIR)" ]; then
    log "Creating backup of current deployment..."
    BACKUP_NAME="backup-$(date +%Y%m%d-%H%M%S)"
    tar -czf "$BACKUP_DIR/$BACKUP_NAME.tar.gz" -C $APP_DIR .
    log "Backup created: $BACKUP_DIR/$BACKUP_NAME.tar.gz"
fi

# Copy application files
log "Copying application files..."
cp -r . $APP_DIR/
cd $APP_DIR

# Set proper permissions
log "Setting proper permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 777 $APP_DIR/storage
chmod -R 777 $APP_DIR/logs

# Copy environment file
log "Setting up environment configuration..."
cp .env.production .env

# Generate SSL certificates if they don't exist
if [ ! -f "/etc/nginx/ssl/cert.pem" ] || [ ! -f "/etc/nginx/ssl/key.pem" ]; then
    log "Generating SSL certificates..."
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/nginx/ssl/key.pem \
        -out /etc/nginx/ssl/cert.pem \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=yourdomain.com"
fi

# Stop existing containers
log "Stopping existing containers..."
docker-compose -f docker-compose.prod.yml down || true

# Build and start containers
log "Building and starting containers..."
docker-compose -f docker-compose.prod.yml up -d --build

# Wait for database to be ready
log "Waiting for database to be ready..."
sleep 30

# Run database migrations
log "Running database migrations..."
docker-compose -f docker-compose.prod.yml exec app php migrate.php

# Seed database if needed
if [ "$1" = "--seed" ]; then
    log "Seeding database..."
    docker-compose -f docker-compose.prod.yml exec app php seeds/seed.php
fi

# Run tests
log "Running tests..."
docker-compose -f docker-compose.prod.yml exec app php tests/run_tests.php

# Set up log rotation
log "Setting up log rotation..."
cat > /etc/logrotate.d/zaa-radio << EOF
/var/log/zaa-radio/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF

# Set up cron jobs
log "Setting up cron jobs..."
cat > /etc/cron.d/zaa-radio << EOF
# Zaa Radio Cron Jobs
0 2 * * * root $APP_DIR/scripts/backup.sh
0 3 * * * root $APP_DIR/scripts/cleanup.sh
*/5 * * * * root $APP_DIR/scripts/health-check.sh
EOF

# Set up systemd service
log "Setting up systemd service..."
cat > /etc/systemd/system/zaa-radio.service << EOF
[Unit]
Description=Zaa Radio Application
Requires=docker.service
After=docker.service

[Service]
Type=oneshot
RemainAfterExit=yes
WorkingDirectory=$APP_DIR
ExecStart=/usr/bin/docker-compose -f docker-compose.prod.yml up -d
ExecStop=/usr/bin/docker-compose -f docker-compose.prod.yml down
TimeoutStartSec=0

[Install]
WantedBy=multi-user.target
EOF

# Enable and start service
systemctl daemon-reload
systemctl enable zaa-radio
systemctl start zaa-radio

# Health check
log "Performing health check..."
sleep 10
if curl -f http://localhost/health > /dev/null 2>&1; then
    log "Health check passed!"
else
    error "Health check failed!"
fi

# Cleanup old backups
log "Cleaning up old backups..."
find $BACKUP_DIR -name "backup-*.tar.gz" -mtime +30 -delete

log "Deployment completed successfully!"
log "Application is available at: https://yourdomain.com"
log "Admin panel: https://yourdomain.com/admin"
log "Logs: /var/log/zaa-radio/"
log "Backups: $BACKUP_DIR/"

# Display useful information
echo ""
echo "=== Deployment Summary ==="
echo "Application: $APP_NAME"
echo "Directory: $APP_DIR"
echo "Logs: /var/log/zaa-radio/"
echo "Backups: $BACKUP_DIR/"
echo "Service: systemctl status zaa-radio"
echo "Logs: journalctl -u zaa-radio -f"
echo "=========================="

