.PHONY: help setup dev start stop restart clean build test db-fresh db-migrate db-seed logs

# Default target
help:
	@echo "🚀 Aplikasi SPK SAW - Available Commands"
	@echo ""
	@echo "📦 Setup & Installation:"
	@echo "  make setup       - First time setup (install dependencies, migrate DB)"
	@echo "  make install     - Install PHP and Node dependencies"
	@echo ""
	@echo "🏃 Development:"
	@echo "  make dev         - Start development server (Laravel + Vite + Queue)"
	@echo "  make start       - Start Docker/Podman containers"
	@echo "  make stop        - Stop Docker/Podman containers"
	@echo "  make restart     - Restart Docker/Podman containers"
	@echo ""
	@echo "🗄️  Database:"
	@echo "  make db-migrate  - Run database migrations"
	@echo "  make db-fresh    - Fresh migration with seeding"
	@echo "  make db-seed     - Seed the database"
	@echo "  make db-show     - Show database information"
	@echo "  make db-cli      - Access PostgreSQL CLI"
	@echo ""
	@echo "🏗️  Build:"
	@echo "  make build       - Build frontend assets for production"
	@echo "  make watch       - Build and watch for changes"
	@echo ""
	@echo "🧪 Testing:"
	@echo "  make test        - Run all tests"
	@echo "  make test-unit   - Run unit tests only"
	@echo "  make test-feature- Run feature tests only"
	@echo ""
	@echo "🧹 Maintenance:"
	@echo "  make clean       - Clear all caches"
	@echo "  make logs        - View logs"
	@echo "  make format      - Format PHP code with Pint"
	@echo ""
	@echo "🐳 Docker/Podman:"
	@echo "  make dc-up       - Start containers"
	@echo "  make dc-down     - Stop containers"
	@echo "  make dc-logs     - View container logs"
	@echo "  make dc-ps       - List running containers"

# Setup & Installation
setup:
	@echo "🔧 Running first-time setup..."
	composer setup

install:
	@echo "📦 Installing dependencies..."
	composer install
	npm install

# Development
dev:
	@echo "🚀 Starting development server..."
	composer dev

start: dc-up

stop: dc-down

restart:
	@echo "🔄 Restarting containers..."
	@command -v podman >/dev/null 2>&1 && podman restart laravel_postgres laravel_pgadmin || docker-compose restart

# Database
db-migrate:
	@echo "🗄️  Running migrations..."
	php artisan migrate

db-fresh:
	@echo "🔄 Fresh migration with seeding..."
	php artisan migrate:fresh --seed

db-seed:
	@echo "🌱 Seeding database..."
	php artisan db:seed

db-show:
	@echo "📊 Database information:"
	php artisan db:show

db-cli:
	@echo "💻 Accessing PostgreSQL CLI..."
	@command -v podman >/dev/null 2>&1 && podman exec -it laravel_postgres psql -U laravel -d laravel || docker-compose exec postgres psql -U laravel -d laravel

# Build
build:
	@echo "🏗️  Building frontend assets..."
	npm run build

watch:
	@echo "👀 Watching for changes..."
	npm run dev

# Testing
test:
	@echo "🧪 Running tests..."
	composer test

test-unit:
	@echo "🧪 Running unit tests..."
	php artisan test --testsuite=Unit

test-feature:
	@echo "🧪 Running feature tests..."
	php artisan test --testsuite=Feature

# Maintenance
clean:
	@echo "🧹 Clearing caches..."
	php artisan config:clear
	php artisan cache:clear
	php artisan view:clear
	php artisan route:clear
	@echo "✅ Caches cleared!"

logs:
	@echo "📋 Viewing logs..."
	php artisan pail

format:
	@echo "✨ Formatting code..."
	./vendor/bin/pint
	@echo "✅ Code formatted!"

# Docker/Podman
dc-up:
	@echo "🐳 Starting containers..."
	@command -v podman-compose >/dev/null 2>&1 && podman-compose up -d || \
	 command -v podman >/dev/null 2>&1 && podman compose up -d || \
	 docker-compose up -d
	@echo "✅ Containers started!"

dc-down:
	@echo "🛑 Stopping containers..."
	@command -v podman-compose >/dev/null 2>&1 && podman-compose down || \
	 command -v podman >/dev/null 2>&1 && podman compose down || \
	 docker-compose down
	@echo "✅ Containers stopped!"

dc-logs:
	@echo "📋 Container logs:"
	@command -v podman >/dev/null 2>&1 && podman logs -f laravel_postgres || docker-compose logs -f

dc-ps:
	@echo "📦 Running containers:"
	@command -v podman >/dev/null 2>&1 && podman ps || docker-compose ps
