# Staging Deployment Guide

This guide covers deploying the Zaa Radio Advertisement Booking System to a staging environment.

## Prerequisites

- Docker and Docker Compose installed
- Access to staging server
- Domain name or IP address for staging
- SSL certificates (optional but recommended)

## Environment Setup

1. **Copy environment file:**
   ```bash
   cp .env.example .env.staging
   ```

2. **Update staging environment variables:**
   ```bash
   # Application
   APP_ENV=staging
   APP_DEBUG=false
   APP_URL=https://staging.zaaradio.com

   # Database
   DB_HOST=db
   DB_PORT=3306
   DB_NAME=zaa_radio_staging
   DB_USER=zaa_radio
   DB_PASSWORD=zaa_radio_password

   # Redis
   REDIS_HOST=redis
   REDIS_PORT=6379
   QUEUE_CONNECTION=redis

   # Mail (configure with your SMTP server)
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-server.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@domain.com
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@zaaradio.com
   MAIL_FROM_NAME="Zaa Radio"

   # Security
   APP_KEY=your-32-character-secret-key
   SESSION_LIFETIME=120
   ```

## Deployment Steps

1. **Deploy the application:**
   ```bash
   docker-compose -f docker-compose.staging.yml up -d --build
   ```

2. **Run database migrations:**
   ```bash
   docker-compose -f docker-compose.staging.yml exec app php migrate.php
   ```

3. **Seed the database:**
   ```bash
   docker-compose -f docker-compose.staging.yml exec app php seeds/seed.php
   ```

4. **Start the queue worker:**
   ```bash
   docker-compose -f docker-compose.staging.yml exec -d worker php app/Workers/QueueWorker.php
   ```

## Verification Checklist

### Basic Functionality
- [ ] Application loads at staging URL
- [ ] All containers are running (`docker-compose -f docker-compose.staging.yml ps`)
- [ ] Database connection works
- [ ] Redis connection works

### User Authentication
- [ ] Admin login works (admin@zaaradio.com / admin123)
- [ ] Station Manager login works (manager@zaaradio.com / manager123)
- [ ] Advertiser login works (advertiser1@example.com / advertiser123)
- [ ] Password reset functionality works

### Core Features
- [ ] Booking calendar displays correctly
- [ ] Advertisers can create bookings
- [ ] Station managers can approve/reject bookings
- [ ] Admin can manage users
- [ ] Reports and analytics work
- [ ] Export functionality works (CSV/PDF/Excel)

### Email Notifications
- [ ] Booking confirmation emails are sent
- [ ] Approval/rejection emails are sent
- [ ] Password reset emails are sent
- [ ] Queue worker processes emails correctly

### Security
- [ ] CSRF protection is active
- [ ] SQL injection protection works
- [ ] XSS protection works
- [ ] Role-based access control works
- [ ] Rate limiting is active

## Monitoring

### Container Health
```bash
# Check container status
docker-compose -f docker-compose.staging.yml ps

# View logs
docker-compose -f docker-compose.staging.yml logs -f

# Check specific service logs
docker-compose -f docker-compose.staging.yml logs -f app
docker-compose -f docker-compose.staging.yml logs -f web
docker-compose -f docker-compose.staging.yml logs -f db
```

### Database Health
```bash
# Connect to database
docker-compose -f docker-compose.staging.yml exec db mysql -u zaa_radio -pzaa_radio_password zaa_radio_staging

# Check table status
SHOW TABLES;
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM bookings;
```

### Redis Health
```bash
# Connect to Redis
docker-compose -f docker-compose.staging.yml exec redis redis-cli

# Check queue status
LLEN zaa_radio_queue
```

## Troubleshooting

### Common Issues

1. **Containers won't start:**
   - Check Docker daemon is running
   - Verify port conflicts (8081, 3307)
   - Check disk space

2. **Database connection fails:**
   - Verify database credentials in .env.staging
   - Check if MySQL container is running
   - Wait for database to fully initialize

3. **Email not sending:**
   - Verify SMTP credentials
   - Check if queue worker is running
   - Test with a simple email first

4. **Permission issues:**
   - Check file permissions in the project directory
   - Ensure Docker has access to the project files

### Logs to Check

- Application logs: `docker-compose -f docker-compose.staging.yml logs app`
- Web server logs: `docker-compose -f docker-compose.staging.yml logs web`
- Database logs: `docker-compose -f docker-compose.staging.yml logs db`
- Redis logs: `docker-compose -f docker-compose.staging.yml logs redis`

## Rollback

If issues occur, you can rollback:

1. **Stop the staging environment:**
   ```bash
   docker-compose -f docker-compose.staging.yml down
   ```

2. **Restore from backup (if available):**
   ```bash
   # Restore database backup
   docker-compose -f docker-compose.staging.yml exec db mysql -u zaa_radio -pzaa_radio_password zaa_radio_staging < backup.sql
   ```

3. **Redeploy previous version:**
   ```bash
   git checkout previous-stable-tag
   docker-compose -f docker-compose.staging.yml up -d --build
   ```

## Maintenance

### Regular Tasks
- Monitor disk space usage
- Check container resource usage
- Review application logs for errors
- Test critical user workflows
- Update dependencies as needed

### Backup Strategy
- Database backups: Daily automated backups
- Application code: Git repository
- Configuration files: Version controlled
- SSL certificates: Secure backup location

## Support

For staging environment issues:
1. Check the troubleshooting section above
2. Review application logs
3. Contact the development team with specific error messages
4. Include relevant log snippets and environment details
