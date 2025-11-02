#!/bin/bash

# Container Management Aliases for Podman/Docker
# Add these to your ~/.zshrc or ~/.bashrc for easier management

# Detect if using Podman or Docker
if command -v podman &> /dev/null && podman machine list &> /dev/null; then
    alias dc='podman-compose'
    alias dcu='podman-compose up -d'
    alias dcd='podman-compose down'
    alias dcl='podman logs'
    alias dce='podman exec -it'
    alias dcp='podman ps'
    echo "🐳 Using Podman"
else
    alias dc='docker-compose'
    alias dcu='docker-compose up -d'
    alias dcd='docker-compose down'
    alias dcl='docker-compose logs'
    alias dce='docker-compose exec'
    alias dcp='docker-compose ps'
    echo "🐳 Using Docker"
fi

# Laravel-specific shortcuts
alias dc-migrate='php artisan migrate'
alias dc-fresh='php artisan migrate:fresh --seed'
alias dc-db='dce laravel_postgres psql -U laravel -d laravel'
alias dc-psql='dce laravel_postgres psql -U laravel -d laravel'

echo "✅ Container aliases loaded!"
echo "   dcu    - Start containers"
echo "   dcd    - Stop containers"
echo "   dcp    - List containers"
echo "   dcl    - View logs"
echo "   dc-db  - Access PostgreSQL CLI"
