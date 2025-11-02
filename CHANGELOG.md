# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
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
