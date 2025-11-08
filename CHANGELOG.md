# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### 🎯 MAJOR MILESTONE: Authentication Module Complete

#### Added
- **Complete JWT Authentication System**
  - JWT-based authentication with tymon/jwt-auth package
  - Secure login, logout, refresh, and user info endpoints
  - Role-based access control (super_admin, admin, kepala_sekolah, user)
  - Indonesian language error messages for better UX

- **Enhanced Database Schema**
  - Schools table with NPSN codes, addresses, provinces, districts
  - Enhanced Users table with roles, job titles, school relationships
  - Proper foreign key constraints and database indexes
  - Support for multi-school user management

- **Eloquent Models & Relationships**
  - School model with complete user relationships
  - User model with JWT implementation and role-based methods  
  - Optimized queries with proper model casting and scoping
  - Circular relationship handling (User ↔ School, KepalaSekolah)

- **Comprehensive Test Data**
  - 5 realistic schools from different Indonesian provinces
  - 27 test users distributed across all roles and schools
  - Proper kepala sekolah assignments for each school
  - SchoolSeeder and UserSeeder with realistic data

- **API Infrastructure**
  - Authentication routes with proper middleware
  - JWT configuration and secret key management
  - API route structure ready for user management
  - Error handling and validation framework

- **Testing & Validation**
  - Custom authentication test command (`php artisan auth:test`)
  - Database verification and relationship testing
  - Password hashing and JWT token validation
  - Complete setup verification system

- **Documentation**
  - Complete auth module documentation (docs/auth-module.md)
  - Implementation summary with progress tracking
  - Database schema with ERD diagrams
  - Comprehensive TODO list for next phases

#### API Endpoints (Ready)
- `POST /api/auth/login` - User authentication with JWT
- `POST /api/auth/logout` - Secure user logout
- `POST /api/auth/refresh` - JWT token refresh
- `GET /api/auth/me` - Current authenticated user info

#### Test Credentials
- Super Admin: `superadmin@spksaw.com` / `password123`
- School Admin: `admin1@spksaw.com` / `password123`
- Kepala Sekolah: `kepala.sekolah1@spksaw.com` / `password123`

### Infrastructure Updates
- Docker Compose configuration for PostgreSQL 16 and pgAdmin 4
- Comprehensive documentation for Docker/Podman usage (DOCKER.md)
- Quick start guide for new developers (QUICKSTART.md)
- Docker command aliases helper script (.docker-aliases.sh)
- Updated README.md with complete project setup instructions
- PostgreSQL database configuration with environment variables
- pgAdmin web interface for database management
- Support for both Docker and Podman container runtimes

### Changed
- Migrated from SQLite to PostgreSQL as default database
- Updated .env.example with PostgreSQL configuration
- Enhanced .gitignore with Docker/Podman specific entries
- Improved development workflow documentation
- Updated README.md with authentication system status

### Database
- Database engine: SQLite → PostgreSQL 16
- Added persistent volumes for data storage
- Configured health checks for database container
- Added backup and restore instructions

### Development Environment
- Laravel: 12.36.1
- PHP: 8.4.11
- React: 19.2.0
- Vite: 7.0.7
- Tailwind CSS: 4.0
- PostgreSQL: 16 (Alpine)
- pgAdmin: 4 (latest)

### Infrastructure
- Docker Compose with multi-service setup
- Podman compatibility
- Network isolation for services
- Volume management for data persistence

## [Initial Release]

### Added
- Initial Laravel + React project setup
- Vite build configuration
- Tailwind CSS integration
- Basic authentication scaffolding
- Database migrations for users, cache, and jobs
- Composer scripts for setup and development
- NPM scripts for frontend build
- Queue and log management
