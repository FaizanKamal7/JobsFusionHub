# JobsFusionHub

A modern full-stack job portal application built with Vue 3 and Symfony, designed as a monorepo for seamless frontend and backend development.

## Overview

JobsFusionHub is a full-stack web application that combines a powerful REST API backend with a modern single-page application frontend. The project uses a monorepo structure to maintain both applications in a single Git repository.

## Technology Stack

### Backend
- **Framework**: Symfony 7.3
- **API**: API Platform 4.2 (REST/JSON API)
- **Database**: PostgreSQL 16 (via Docker)
- **ORM**: Doctrine ORM 3.5
- **Security**: Symfony Security Bundle
- **CORS**: Nelmio CORS Bundle
- **Package Manager**: Composer

### Frontend
- **Framework**: Vue 3.5 (Composition API)
- **Build Tool**: Vite 7.1
- **State Management**: Pinia 3.0
- **Routing**: Vue Router 4.6
- **Language**: TypeScript 5.9
- **Testing**: Vitest 3.2 (unit) + Playwright 1.56 (e2e)
- **Code Quality**: ESLint 9.37 + Prettier 3.6
- **Package Manager**: npm

### DevOps
- **Containerization**: Docker Compose
- **Database**: PostgreSQL 16 Alpine

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** >= 9.x
- **Docker** & **Docker Compose**
- **Git**

## Project Structure

```
JobsFusionHub/
├── backend/                 # Symfony REST API
│   ├── bin/                # Console commands
│   ├── config/             # Symfony configuration
│   ├── migrations/         # Database migrations
│   ├── public/             # Web root
│   ├── src/
│   │   ├── Controller/     # API controllers
│   │   ├── Entity/         # Doctrine entities
│   │   └── Repository/     # Database repositories
│   ├── templates/          # Twig templates
│   ├── var/                # Cache and logs
│   └── composer.json       # PHP dependencies
│
├── frontend/               # Vue 3 SPA
│   ├── e2e/               # End-to-end tests
│   ├── src/
│   │   ├── components/    # Vue components
│   │   ├── assets/        # Images, styles
│   │   ├── config/        # API configuration
│   │   ├── router/        # Vue Router
│   │   ├── stores/        # Pinia state management
│   │   └── __tests__/     # Unit tests
│   ├── public/            # Static assets
│   └── package.json       # Node dependencies
│
├── .git/                   # Git repository
├── .gitignore             # Git ignore rules
└── README.md              # This file
```

## Getting Started

### 1. Clone the Repository

```bash
git clone <repository-url>
cd JobsFusionHub
```

### 2. Backend Setup

#### Install Dependencies
```bash
cd backend
composer install
```

#### Configure Environment
```bash
# Copy the environment file
cp .env .env.local

# Update database credentials if needed
# Edit .env.local and set your DATABASE_URL
```

#### Start Database
```bash
# From the backend directory
docker-compose up -d
```

#### Run Migrations
```bash
php bin/console doctrine:migrations:migrate
```

#### Start Development Server
```bash
symfony server:start
# Or use PHP built-in server
php -S localhost:8000 -t public
```

The API will be available at `http://localhost:8000`

### 3. Frontend Setup

#### Install Dependencies
```bash
cd frontend
npm install
```

#### Configure Environment
```bash
# The API URL is configured in src/config/apiClient.ts
# Default: http://localhost:8000/api
```

#### Start Development Server
```bash
npm run dev
```

The frontend will be available at `http://localhost:5173`

## Development Workflow

### Backend Commands

```bash
# Symfony console
php bin/console

# Create a new entity
php bin/console make:entity

# Create a migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Clear cache
php bin/console cache:clear

# Database fixtures (if installed)
php bin/console doctrine:fixtures:load
```

### Frontend Commands

```bash
# Start dev server with hot reload
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Run unit tests
npm run test:unit

# Run unit tests in watch mode
npm run test:unit:watch

# Run E2E tests
npm run test:e2e

# Run E2E tests in UI mode
npm run test:e2e:ui

# Lint and fix code
npm run lint

# Format code
npm run format

# Type check
npm run type-check
```

## API Documentation

Once the backend is running, API documentation is available at:

- **API Platform Interface**: `http://localhost:8000/api`
- **OpenAPI/Swagger**: `http://localhost:8000/api/docs`

## Testing

### Backend Tests
```bash
cd backend
php bin/phpunit
```

### Frontend Tests

**Unit Tests (Vitest)**
```bash
cd frontend
npm run test:unit
```

**E2E Tests (Playwright)**
```bash
cd frontend
npm run test:e2e
```

## Code Quality

### Linting
```bash
cd frontend
npm run lint
```

### Formatting
```bash
cd frontend
npm run format
```

## Environment Variables

### Backend (.env)
```env
APP_ENV=dev
APP_SECRET=your-secret-key
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/jobsfusionhub?serverVersion=16&charset=utf8"
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
```

### Frontend
API configuration is in `frontend/src/config/apiClient.ts`

## Deployment

### Backend
1. Set `APP_ENV=prod` in `.env`
2. Run `composer install --no-dev --optimize-autoloader`
3. Clear and warm up cache: `php bin/console cache:clear --env=prod`
4. Run migrations: `php bin/console doctrine:migrations:migrate --no-interaction`
5. Configure your web server to point to `backend/public/`

### Frontend
1. Build the application: `npm run build`
2. The `dist/` directory contains production-ready files
3. Deploy to your web server or hosting platform

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/my-new-feature`
5. Submit a pull request

## Troubleshooting

### CORS Issues
If you encounter CORS errors, ensure that:
- The backend CORS bundle is properly configured in `config/packages/nelmio_cors.yaml`
- The frontend API client is using the correct base URL
- Both frontend and backend servers are running

### Database Connection Issues
- Verify Docker containers are running: `docker-compose ps`
- Check database credentials in `.env.local`
- Ensure PostgreSQL port (5432) is not in use by another service

### Port Conflicts
- Backend default: 8000
- Frontend default: 5173
- Database default: 5432

If any port is in use, you can change them in the respective configuration files.

## License

[Add your license here]

## Support

For issues and questions, please open an issue in the GitHub repository.
