# Contributing Guide

Thank you for considering contributing to this project! This guide will help you get started.

## 🤝 How to Contribute

### Reporting Bugs

If you find a bug, please create an issue with:
- A clear, descriptive title
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- Screenshots (if applicable)
- Your environment (OS, PHP version, Node version)

### Suggesting Features

Feature suggestions are welcome! Please:
- Check if the feature has already been requested
- Clearly describe the feature and its benefits
- Provide examples of how it would be used

### Pull Requests

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes**
   - Follow the coding standards
   - Write tests for new features
   - Update documentation as needed

4. **Test your changes**
   ```bash
   composer test
   ```

5. **Commit your changes**
   ```bash
   git commit -m "Add: description of your changes"
   ```
   
   Use conventional commit messages:
   - `Add:` for new features
   - `Fix:` for bug fixes
   - `Update:` for updates to existing features
   - `Refactor:` for code refactoring
   - `Docs:` for documentation changes
   - `Test:` for test updates

6. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**
   - Provide a clear description of changes
   - Reference any related issues
   - Include screenshots for UI changes

## 📝 Coding Standards

### PHP (Laravel)

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use Laravel's built-in features and conventions
- Write expressive, readable code with clear variable names
- Add type hints to method parameters and return types

Run Laravel Pint to format your code:
```bash
./vendor/bin/pint
```

### JavaScript/React

- Use functional components with hooks
- Follow React best practices
- Use meaningful component and variable names
- Add JSDoc comments for complex functions

### Database

- Always create migrations for schema changes
- Never modify existing migrations that have been deployed
- Add indexes for frequently queried columns
- Write both `up()` and `down()` methods

### Testing

- Write tests for new features
- Maintain or improve test coverage
- Test both happy paths and edge cases

Run tests:
```bash
composer test
```

## 🔄 Development Workflow

1. **Start the development environment**
   ```bash
   composer dev
   ```

2. **Make your changes**
   - Edit code
   - Hot reload will update the frontend
   - Check logs in the terminal

3. **Test manually**
   - Test in different browsers
   - Test edge cases
   - Verify database changes

4. **Run automated tests**
   ```bash
   composer test
   ```

5. **Check code style**
   ```bash
   ./vendor/bin/pint --test
   ```

## 📚 Project Structure

```
├── app/                  # Application logic
│   ├── Http/            # Controllers, Middleware, Requests
│   ├── Models/          # Eloquent models
│   └── Services/        # Business logic services
├── database/
│   ├── migrations/      # Database migrations
│   ├── seeders/        # Data seeders
│   └── factories/      # Model factories
├── resources/
│   ├── js/             # React components
│   ├── css/            # Stylesheets
│   └── views/          # Blade templates
├── routes/             # Route definitions
├── tests/              # Test files
│   ├── Feature/       # Feature tests
│   └── Unit/          # Unit tests
└── public/            # Public assets
```

## 🎨 Frontend Guidelines

- Use Tailwind CSS for styling
- Keep components small and focused
- Extract reusable logic into custom hooks
- Use prop-types or TypeScript for type checking

## 🗄️ Database Guidelines

- Use migrations for all schema changes
- Write seeders for test data
- Use factories for generating fake data
- Add proper indexes for performance

## ✅ Pull Request Checklist

Before submitting a PR, ensure:

- [ ] Code follows project coding standards
- [ ] All tests pass (`composer test`)
- [ ] New features have tests
- [ ] Documentation is updated
- [ ] Commit messages are clear and descriptive
- [ ] No merge conflicts with main branch
- [ ] Code is properly formatted (`./vendor/bin/pint`)
- [ ] No console errors or warnings

## 🆘 Getting Help

- Check the [README.md](README.md) for setup instructions
- Review [QUICKSTART.md](QUICKSTART.md) for common tasks
- Look at existing code for examples
- Ask questions in issues or discussions

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

## 🙏 Thank You!

Your contributions make this project better for everyone. Thank you for taking the time to contribute!
