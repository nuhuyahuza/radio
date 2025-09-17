# Zaa Radio Advertisement Booking System

A comprehensive radio advertisement booking system built with PHP 7.4, MySQL, and Docker.

## Features

- **Multi-role Dashboard**: Admin, Station Manager, and Advertiser dashboards with collapsible navigation
- **Booking System**: Online calendar for advertisers to book radio slots
- **Approval Workflow**: Station managers can approve/reject bookings
- **Reports & Analytics**: Revenue reports, booking statistics, and export capabilities
- **Email Notifications**: Automated emails for bookings, approvals, and rejections
- **Security**: CSRF protection, prepared statements, and role-based access control

## Tech Stack

- **Backend**: PHP 7.4, MySQL 5.7+
- **Frontend**: Bootstrap 5, Vanilla JavaScript (ES6)
- **Containerization**: Docker & Docker Compose
- **Additional**: Redis, PHPMailer, DomPDF, PhpSpreadsheet

## Quick Start

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd radio
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Update `.env` with your configuration (database credentials, mail settings, etc.)

4. Start the development environment:
```bash
docker-compose up -d --build
```

5. Run migrations:
```bash
docker-compose exec app php migrate.php
```

6. Seed the database:
```bash
docker-compose exec app php seeds/seed.php
```

7. Access the application:
- **Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **Redis Commander**: http://localhost:8082

### Default Credentials

- **Admin**: admin@zaaradio.com / admin123
- **Station Manager**: manager@zaaradio.com / manager123

## Project Structure

```
radio/
├── app/                    # Application code
│   ├── Controllers/        # HTTP controllers
│   ├── Models/            # Database models
│   ├── Middleware/        # Authentication & authorization
│   └── Utils/             # Helper classes
├── public/                # Web root
│   ├── index.php          # Entry point
│   ├── assets/            # CSS, JS, images
│   └── views/             # HTML templates
├── config/                # Configuration files
├── migrations/            # Database migrations
├── seeds/                 # Database seeders
├── tests/                 # Unit tests
├── docker/                # Docker configuration
└── docker-compose.yml     # Docker services
```

## API Endpoints

### Public
- `GET /` - Landing page
- `GET /book` - Booking calendar
- `POST /api/bookings` - Create booking

### Admin
- `GET /admin` - Admin dashboard
- `GET /admin/users` - User management
- `GET /admin/reports` - Reports & analytics

### Station Manager
- `GET /manager` - Manager dashboard
- `GET /manager/slots` - Slot management
- `POST /manager/bookings/{id}/approve` - Approve booking
- `POST /manager/bookings/{id}/reject` - Reject booking

### Advertiser
- `GET /advertiser` - Advertiser dashboard
- `GET /advertiser/bookings` - My bookings

## Database Schema

### Users Table
- `id`, `name`, `email`, `password`, `role`, `phone`, `company`, `created_at`, `updated_at`

### Slots Table
- `id`, `station_id`, `date`, `start_time`, `end_time`, `price`, `status`, `created_at`, `updated_at`

### Bookings Table
- `id`, `advertiser_id`, `slot_id`, `status`, `message`, `created_at`, `updated_at`

## Development

### Running Tests
```bash
docker-compose exec app composer test
```

### Code Standards
This project follows PSR-12 PHP coding standards.

### Adding New Features
1. Create migration for database changes
2. Update models if needed
3. Add controller methods
4. Create/update views
5. Add tests
6. Update documentation

## Production Deployment

1. Use production Docker Compose file:
```bash
docker-compose -f docker-compose.prod.yml up -d
```

2. Set up SSL certificates
3. Configure reverse proxy (nginx)
4. Set up monitoring and logging

## Support

For issues and questions, please contact the development team.

## License

This project is proprietary software for Zaa Radio.
