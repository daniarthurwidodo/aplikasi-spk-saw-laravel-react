# Aplikasi SPK SAW - Laravel + React

[![Laravel](https://img.shields.io/badge/Laravel-12.36.1-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19.2.0-61DAFB?style=flat&logo=react)](https://react.dev)
[![PHP](https://img.shields.io/badge/PHP-8.4.11-777BB4?style=flat&logo=php)](https://www.php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=flat&logo=postgresql)](https://www.postgresql.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

A Decision Support System (Sistem Pendukung Keputusan) application using the Simple Additive Weighting (SAW) method, built with Laravel and React.

> 📖 **Quick Links**: [Quick Start](QUICKSTART.md) | [Docker Setup](DOCKER.md) | [Contributing](CONTRIBUTING.md) | [Changelog](CHANGELOG.md)

## 🚀 Tech Stack

### Backend
- **Laravel 12.36.1** - PHP Framework
- **PHP 8.4.11** - Programming Language
- **PostgreSQL 16** - Database

### Frontend
- **React 19.2.0** - UI Library
- **React Router DOM 7.9.5** - Client-side Routing
- **Vite 7.0.7** - Build Tool
- **Tailwind CSS 4.0** - CSS Framework

### DevOps
- **Docker/Podman** - Container Management
- **pgAdmin 4** - Database Management Tool

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2 with extensions:
  - PDO
  - pgsql
  - mbstring
  - xml
  - curl
- **Composer** - PHP package manager
- **Node.js** >= 20.x - JavaScript runtime
- **npm** or **yarn** - Package manager
- **Docker Desktop** or **Podman** - Container runtime

## 🛠️ Installation

### Quick Setup

For first-time setup, run the automated setup script:

```bash
composer setup
```

This will:
- Install PHP dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Run database migrations
- Install Node.js dependencies
- Build frontend assets

### Manual Setup

If you prefer manual setup:

1. **Clone the repository**
   ```bash
   git clone https://github.com/daniarthurwidodo/aplikasi-spk-saw-laravel-react.git
   cd aplikasi-spk-saw-laravel-react
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Start PostgreSQL with Docker/Podman**
   ```bash
   # Using Docker
   docker-compose up -d
   
   # Using Podman
   podman compose up -d
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Install Node.js dependencies**
   ```bash
   npm install
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

## 🚀 Running the Application

### Development Mode

The easiest way to run the application in development mode:

```bash
composer dev
```

This starts all necessary services concurrently:
- Laravel development server (http://localhost:8000)
- Queue worker
- Laravel Pail (logs viewer)
- Vite dev server (HMR)

### Manual Development Mode

Alternatively, run services separately:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

**Terminal 3 - Queue Worker (optional):**
```bash
php artisan queue:work
```

### Production Build

```bash
npm run build
php artisan serve
```

## 🗄️ Database Management

### Docker/Podman Commands

**Start containers:**
```bash
# Docker
docker-compose up -d

# Podman
podman compose up -d
```

**Stop containers:**
```bash
# Docker
docker-compose down

# Podman
podman compose down
```

**Access PostgreSQL CLI:**
```bash
# Docker
docker-compose exec postgres psql -U laravel -d laravel

# Podman
podman exec -it laravel_postgres psql -U laravel -d laravel
```

### Database Configuration

Default database credentials (configurable in `.env`):

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### pgAdmin Access

Access the web-based database manager:
- URL: http://localhost:5050
- Email: `admin@admin.com`
- Password: `admin`

See [DOCKER.md](DOCKER.md) for detailed Docker/Podman documentation.

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Or run PHPUnit directly:

```bash
php artisan test
```

## 📁 Project Structure

```
├── app/                    # Application logic
│   ├── Http/              # Controllers, Middleware
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── database/              # Database files
│   ├── migrations/        # Database migrations
│   ├── seeders/          # Database seeders
│   └── factories/        # Model factories
├── public/               # Public assets
├── resources/            # Frontend resources
│   ├── css/             # Stylesheets
│   ├── js/              # React components
│   └── views/           # Blade templates
├── routes/              # Route definitions
├── storage/             # Application storage
├── tests/               # Test files
└── docker-compose.yml   # Container configuration
```

## 🔧 Available Commands

### Quick Commands with Make

For convenience, you can use Make commands (recommended):

```bash
make help           # Show all available commands
make setup          # First-time setup
make dev            # Start development server
make start          # Start containers
make stop           # Stop containers
make db-migrate     # Run migrations
make db-fresh       # Fresh migration + seed
make test           # Run tests
make clean          # Clear all caches
```

Run `make help` to see all available commands.

### Composer Scripts

```bash
composer setup          # First-time setup
composer dev           # Run development server with all services
composer test          # Run tests
```

### NPM Scripts

```bash
npm run dev           # Start Vite dev server
npm run build         # Build for production
```

### Artisan Commands

```bash
php artisan serve             # Start development server
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Fresh migration
php artisan db:seed          # Seed database
php artisan queue:work       # Run queue worker
php artisan pail             # View logs
php artisan db:show          # Show database info
```

## 🌐 Accessing the Application

- **Application**: http://localhost:8000
- **pgAdmin**: http://localhost:5050
- **Vite Dev Server**: http://localhost:5173 (when running `npm run dev`)

## 🐛 Troubleshooting

### Vite Manifest Not Found

If you see "Vite manifest not found" error:

```bash
npm run dev
```

Or build the assets:

```bash
npm run build
```

### Database Connection Issues

1. Check if PostgreSQL container is running:
   ```bash
   # Docker
   docker-compose ps
   
   # Podman
   podman ps
   ```

2. Verify database credentials in `.env` match `docker-compose.yml`

3. Test database connection:
   ```bash
   php artisan db:show
   ```

### Port Already in Use

Change ports in `.env`:

```env
DB_PORT=5433
PGADMIN_PORT=5051
```

Then restart containers.

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev)
- [React Router Documentation](https://reactrouter.com/)
- [Vite Documentation](https://vitejs.dev)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Router Setup Guide](ROUTER.md)
- [Docker Documentation](DOCKER.md)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
