#!/bin/bash

# Zaa Radio Health Check Script
# Monitors application health and sends alerts if needed

set -e

APP_URL="https://yourdomain.com"
HEALTH_ENDPOINT="$APP_URL/health"
LOG_FILE="/var/log/zaa-radio/health-check.log"
ALERT_EMAIL="admin@yourdomain.com"

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1" >> $LOG_FILE
}

check_health() {
    if curl -f -s $HEALTH_ENDPOINT > /dev/null; then
        log "Health check passed"
        return 0
    else
        log "Health check failed"
        return 1
    fi
}

if ! check_health; then
    log "Sending alert email..."
    echo "Zaa Radio health check failed at $(date)" | mail -s "Zaa Radio Alert" $ALERT_EMAIL
fi

