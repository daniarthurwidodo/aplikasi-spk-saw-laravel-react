# Quick Start Guide

## 🚀 Get Up and Running in 5 Minutes

### Step 1: Prerequisites Check

Make sure you have these installed:
- [ ] PHP 8.2 or higher (`php -v`)
- [ ] Composer (`composer -V`)
- [ ] Node.js 20+ (`node -v`)
- [ ] Docker Desktop or Podman (`docker -v` or `podman -v`)

### Step 2: Clone & Setup

```bash
# Clone the repository
git clone https://github.com/daniarthurwidodo/aplikasi-spk-saw-laravel-react.git
cd aplikasi-spk-saw-laravel-react

# Run automated setup
composer setup
```

### Step 3: Start Database

```bash
# Quick way (uses Make)
make start

# Or manually:
# Using Docker
docker-compose up -d

# Using Podman
podman-compose up -d
```

Wait ~5 seconds for PostgreSQL to start.

### Step 4: Start Development Server

```bash
# Quick way (uses Make)
make dev

# Or manually:
composer dev
```

This starts:
- ✅ Laravel server at http://localhost:8000
- ✅ Vite dev server with HMR
- ✅ Queue worker
- ✅ Log viewer

### Step 5: Open Application

Open your browser and visit:
- **App**: http://localhost:8000
- **pgAdmin**: http://localhost:5050

## 🎯 Common Tasks

### View All Commands
```bash
make help
```

### Run Migrations
```bash
make db-migrate
# or
php artisan migrate
```

### Seed Database
```bash
make db-seed
# or
php artisan db:seed
```

### Fresh Migration + Seed
```bash
make db-fresh
# or
php artisan migrate:fresh --seed
```

### View Database
```bash
make db-show
# or
php artisan db:show
```

### Access PostgreSQL CLI
```bash
make db-cli
# or manually:
# Docker: docker-compose exec postgres psql -U laravel -d laravel
# Podman: podman exec -it laravel_postgres psql -U laravel -d laravel
```

### Stop Everything
```bash
# Stop dev server: Ctrl+C

# Stop database
make stop
# or manually:
# Docker: docker-compose down
# Podman: podman-compose down
```

## 🐛 Quick Fixes

### "Vite manifest not found"
```bash
npm run dev
```

### "Connection refused" database error
```bash
# Check containers
docker-compose ps       # Docker
podman ps              # Podman

# Restart containers
docker-compose restart  # Docker
podman restart laravel_postgres  # Podman
```

### Port 8000 already in use
```bash
php artisan serve --port=8001
```

### Clear Laravel cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📚 Next Steps

- Read the full [README.md](README.md)
- Check [DOCKER.md](DOCKER.md) for Docker/Podman details
- Browse the [Laravel Documentation](https://laravel.com/docs)
- Explore the [React Documentation](https://react.dev)

## 🆘 Need Help?

- Check the [Troubleshooting section](README.md#-troubleshooting) in README.md
- Review container logs: `docker-compose logs -f` or `podman logs -f laravel_postgres`
- Verify `.env` configuration matches `docker-compose.yml`

Happy coding! 🎉
