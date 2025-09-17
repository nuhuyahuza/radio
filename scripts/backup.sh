#!/bin/bash

# Zaa Radio Backup Script
# Creates backups of the database and application files

set -e

# Configuration
APP_DIR="/var/www/zaa-radio"
BACKUP_DIR="/var/backups/zaa-radio"
DB_NAME="zaa_radio_prod"
DB_USER="zaa_radio_user"
DB_PASS="your_secure_password_here"
S3_BUCKET="your-backup-bucket"
S3_REGION="us-east-1"
RETENTION_DAYS=30

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

# Create backup directory
mkdir -p $BACKUP_DIR

# Generate backup filename
BACKUP_NAME="zaa-radio-backup-$(date +%Y%m%d-%H%M%S)"
BACKUP_FILE="$BACKUP_DIR/$BACKUP_NAME.tar.gz"

log "Starting backup process..."

# Database backup
log "Creating database backup..."
DB_BACKUP_FILE="$BACKUP_DIR/$BACKUP_NAME.sql"
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $DB_BACKUP_FILE

if [ $? -eq 0 ]; then
    log "Database backup created successfully"
else
    error "Database backup failed"
fi

# Application files backup
log "Creating application files backup..."
cd $APP_DIR
tar -czf $BACKUP_FILE \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs' \
    --exclude='storage/cache' \
    .

if [ $? -eq 0 ]; then
    log "Application files backup created successfully"
else
    error "Application files backup failed"
fi

# Upload to S3 if configured
if [ ! -z "$S3_BUCKET" ] && command -v aws &> /dev/null; then
    log "Uploading backup to S3..."
    aws s3 cp $BACKUP_FILE s3://$S3_BUCKET/backups/ --region $S3_REGION
    aws s3 cp $DB_BACKUP_FILE s3://$S3_BUCKET/backups/ --region $S3_REGION
    
    if [ $? -eq 0 ]; then
        log "Backup uploaded to S3 successfully"
    else
        error "S3 upload failed"
    fi
fi

# Cleanup old backups
log "Cleaning up old backups..."
find $BACKUP_DIR -name "zaa-radio-backup-*.tar.gz" -mtime +$RETENTION_DAYS -delete
find $BACKUP_DIR -name "zaa-radio-backup-*.sql" -mtime +$RETENTION_DAYS -delete

log "Backup process completed successfully!"
log "Backup file: $BACKUP_FILE"
log "Database backup: $DB_BACKUP_FILE"

