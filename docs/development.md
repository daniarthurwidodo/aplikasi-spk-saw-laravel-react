# 🛠️ Development Guide

**Project:** SPK-SAWh Development Guidelines  
**Target Audience:** Developers, Contributors  
**Last Updated:** November 8, 2025  

## 🚀 Getting Started

### Prerequisites
- **PHP** >= 8.2 with required extensions
- **Node.js** >= 20.x
- **Composer** >= 2.0
- **Docker/Podman** for local database
- **Git** for version control

### Quick Setup
```bash
# Clone repository
git clone https://github.com/daniarthurwidodo/aplikasi-spk-saw-laravel-react.git
cd aplikasi-spk-saw-laravel-react

# Run automated setup
composer setup

# Start development servers
composer dev
```

## 📁 Project Structure

### Backend (Laravel)
```
app/
├── Http/
│   ├── Controllers/     # API controllers
│   ├── Middleware/      # Custom middleware
│   └── Requests/        # Form request validation
├── Models/              # Eloquent models
├── Services/            # Business logic services
└── Providers/           # Service providers

database/
├── migrations/          # Database schema
├── seeders/            # Sample data
└── factories/          # Model factories

tests/
├── Feature/            # Integration tests
└── Unit/               # Unit tests
```

### Frontend (React)
```
resources/js/
├── components/         # Reusable UI components
├── layouts/           # Layout components
├── pages/             # Page components
├── hooks/             # Custom React hooks
├── utils/             # Utility functions
└── services/          # API service calls
```

## 🔧 Development Workflow

### 1. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Configure database settings
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

# Configure JWT settings
JWT_SECRET=your-secret-key-here
JWT_TTL=60
JWT_REFRESH_TTL=10080
```

### 2. Database Development
```bash
# Create new migration
php artisan make:migration create_new_table

# Create model with migration
php artisan make:model NewModel -m

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### 3. Backend Development
```bash
# Create controller
php artisan make:controller Api/NewController --api

# Create request validation
php artisan make:request NewRequest

# Create service class
php artisan make:service NewService

# Create middleware
php artisan make:middleware NewMiddleware
```

### 4. Frontend Development
```bash
# Start Vite dev server with hot reload
npm run dev

# Build for production
npm run build

# Watch for changes
npm run watch
```

## 🧪 Testing

### Backend Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage

# Create new test
php artisan make:test NewFeatureTest
```

### Frontend Testing (Future)
```bash
# Run Jest tests
npm test

# Run with coverage
npm run test:coverage

# Watch mode
npm run test:watch
```

## 📝 Coding Standards

### PHP/Laravel Standards
- Follow **PSR-12** coding standard
- Use **Laravel conventions** for naming
- Implement **type hints** where possible
- Write **PHPDoc comments** for all methods

#### Example Controller
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    }
}
```

### React/JavaScript Standards
- Use **functional components** with hooks
- Implement **TypeScript** for type safety (planned)
- Follow **React best practices**
- Use **ESLint** and **Prettier** for formatting

#### Example Component
```jsx
import React, { useState, useEffect } from 'react';
import { useApi } from '../hooks/useApi';

export default function UserList() {
    const [users, setUsers] = useState([]);
    const { fetchUsers } = useApi();

    useEffect(() => {
        const loadUsers = async () => {
            try {
                const data = await fetchUsers();
                setUsers(data);
            } catch (error) {
                console.error('Failed to load users:', error);
            }
        };

        loadUsers();
    }, []);

    return (
        <div className="user-list">
            {users.map(user => (
                <div key={user.id} className="user-card">
                    {user.name}
                </div>
            ))}
        </div>
    );
}
```

## 🔄 Git Workflow

### Branch Strategy
```
main                    # Production-ready code
├── develop             # Development integration
├── feature/auth-api    # New features
├── bugfix/login-issue  # Bug fixes
└── hotfix/security     # Critical fixes
```

### Commit Messages
```bash
# Feature
feat(auth): implement JWT authentication

# Bug fix
fix(users): resolve duplicate email validation

# Documentation
docs(api): update authentication endpoints

# Refactor
refactor(services): extract common validation logic

# Test
test(auth): add login endpoint tests
```

### Pull Request Process
1. **Create feature branch** from `develop`
2. **Implement changes** with tests
3. **Update documentation** if needed
4. **Create pull request** with description
5. **Code review** by team members
6. **Merge** after approval

## 📦 Package Management

### Composer (PHP)
```bash
# Add new package
composer require vendor/package

# Add development package
composer require --dev vendor/package

# Update packages
composer update

# Optimize autoloader
composer dump-autoload -o
```

### NPM (JavaScript)
```bash
# Add new package
npm install package-name

# Add development package
npm install --save-dev package-name

# Update packages
npm update

# Audit for vulnerabilities
npm audit
```

## 🐛 Debugging

### Laravel Debugging
```bash
# Enable debug mode
APP_DEBUG=true

# View logs
php artisan pail

# Clear various caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Debugging
```bash
# Show database info
php artisan db:show

# Test database connection
php artisan db:monitor

# Database queries logging
DB_LOG_QUERIES=true
```

### React Debugging
- Use **React Developer Tools** browser extension
- Enable **Vite debug mode** in development
- Use **console.log** for debugging (remove before commit)

## 🔐 Security Practices

### General Guidelines
- **Never commit** sensitive data (passwords, tokens)
- **Validate all inputs** on both client and server
- **Use HTTPS** in production
- **Implement rate limiting** for API endpoints
- **Regular security audits** with tools

### Environment Variables
```bash
# Use .env for configuration
APP_KEY=base64:generated-key
DB_PASSWORD=secure-password
JWT_SECRET=random-secret-key

# Different settings per environment
APP_ENV=local|staging|production
APP_DEBUG=false  # Never true in production
```

## 📊 Performance Guidelines

### Backend Optimization
- **Use database indexes** for frequently queried fields
- **Implement caching** for expensive operations
- **Optimize N+1 queries** with eager loading
- **Use queue jobs** for heavy processing

```php
// Good: Eager loading
$users = User::with('school')->get();

// Bad: N+1 problem
$users = User::all();
foreach ($users as $user) {
    echo $user->school->name; // N+1 query
}
```

### Frontend Optimization
- **Lazy load components** for better performance
- **Optimize images** and use appropriate formats
- **Minimize bundle size** with code splitting
- **Use React.memo** for expensive components

## 🔧 Development Tools

### Recommended VS Code Extensions
- **PHP Intelephense** - PHP language server
- **Laravel Extension Pack** - Laravel development tools
- **ES7+ React/Redux/React-Native snippets** - React snippets
- **Prettier** - Code formatting
- **ESLint** - JavaScript linting
- **GitLens** - Git integration

### Useful Laravel Commands
```bash
# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Show all routes
php artisan route:list

# Show all events
php artisan event:list

# Laravel Telescope (debugging)
composer require laravel/telescope --dev
php artisan telescope:install
```

## 📚 Learning Resources

### Laravel
- [Laravel Documentation](https://laravel.com/docs)
- [Laracasts](https://laracasts.com) - Video tutorials
- [Laravel News](https://laravel-news.com) - Latest updates

### React
- [React Documentation](https://react.dev)
- [React Router](https://reactrouter.com) - Client-side routing
- [React Hook Form](https://react-hook-form.com) - Form handling

### General
- [Clean Code Principles](https://blog.cleancoder.com)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Design Patterns](https://refactoring.guru/design-patterns)

## 🚀 Deployment Preparation

### Pre-deployment Checklist
- [ ] All tests passing
- [ ] Code reviewed and approved
- [ ] Database migrations tested
- [ ] Environment variables configured
- [ ] Security scan completed
- [ ] Performance testing done
- [ ] Documentation updated

### Build Commands
```bash
# Production build
npm run build
composer install --no-dev --optimize-autoloader

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

**Next:** [Deployment Guide](deployment.md)  
**Previous:** [API Reference](api-reference.md)