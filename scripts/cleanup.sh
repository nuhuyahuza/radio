#!/bin/bash

# Zaa Radio Cleanup Script
# Cleans up old logs, cache, and temporary files

set -e

APP_DIR="/var/www/zaa-radio"
LOG_DIR="/var/log/zaa-radio"
CACHE_DIR="$APP_DIR/storage/cache"
LOG_RETENTION_DAYS=30

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

log "Starting cleanup process..."

# Clean up old logs
log "Cleaning up old log files..."
find $LOG_DIR -name "*.log" -mtime +$LOG_RETENTION_DAYS -delete

# Clean up cache
log "Cleaning up cache files..."
rm -rf $CACHE_DIR/*

# Clean up temporary files
log "Cleaning up temporary files..."
find $APP_DIR -name "*.tmp" -delete
find $APP_DIR -name "*.temp" -delete

# Clean up old backups
log "Cleaning up old backups..."
find /var/backups/zaa-radio -name "*.tar.gz" -mtime +30 -delete
find /var/backups/zaa-radio -name "*.sql" -mtime +30 -delete

log "Cleanup completed successfully!"

