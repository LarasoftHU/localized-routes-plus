# Contributing to Localized Routes Plus

Thank you for considering contributing to the Localized Routes Plus package! This document provides guidelines and instructions for contributing to this Laravel package.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Making Changes](#making-changes)
- [Testing](#testing)
- [Code Quality](#code-quality)
- [Submitting Changes](#submitting-changes)
- [Reporting Issues](#reporting-issues)

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code. Please be respectful and constructive in all interactions.

## Getting Started

Before you begin:
- Make sure you have a [GitHub account](https://github.com/signup/free)
- Check existing [issues](https://github.com/LarasoftHU/localized-routes-plus/issues) to see if your bug or feature request already exists
- For major changes, please create an issue first to discuss what you would like to change

## Development Setup

### Prerequisites

- PHP 8.0 or higher
- Composer
- Laravel 9.0 or higher (for testing)

### Installation

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/YOUR-USERNAME/localized-routes-plus.git
   cd localized-routes-plus
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Prepare the development environment:
   ```bash
   composer run prepare
   ```

## Making Changes

### Coding Standards

This project follows PSR-12 coding standards and uses Laravel Pint for code formatting.

#### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add appropriate comments for complex logic
- Keep methods small and focused on a single responsibility

#### Formatting

Before submitting your changes, ensure your code is properly formatted:

```bash
composer run format
```

This will run Laravel Pint to automatically fix code style issues.

## Testing

### Running Tests

All contributions must include appropriate tests. This project uses Pest for testing.

Run the full test suite:
```bash
composer run test
```

Run tests with coverage:
```bash
composer run test-coverage
```

### Test Requirements

- All new features must include corresponding tests
- Bug fixes should include regression tests
- Aim for high test coverage
- Tests should be clear, concise, and well-documented
- Use descriptive test names that explain what is being tested

### Test Structure

- **Feature Tests**: Located in `tests/Feature/` - Test the integration of multiple components
- **Unit Tests**: Test individual classes and methods in isolation

### Writing Tests

When writing tests:
- Use Pest's natural language syntax
- Group related tests using `describe()` blocks
- Use meaningful assertions
- Test both happy paths and edge cases
- Mock external dependencies when appropriate

Example test structure:
```php
describe('LocalizedRoute', function () {
    it('can generate localized routes', function () {
        // Test implementation
    });

    it('handles missing translations gracefully', function () {
        // Test implementation
    });
});
```

## Code Quality

### Static Analysis

This project uses PHPStan for static analysis. Ensure your code passes all checks:

```bash
composer run analyse
```

### Quality Standards

- Code must pass PHPStan level 5 analysis
- No new PHP errors or warnings
- Follow SOLID principles
- Maintain backward compatibility where possible
- Document public methods and classes with PHPDoc

### Pre-submission Checklist

Before submitting your changes, run the following commands to ensure quality:

```bash
# Format code
composer run format

# Run static analysis
composer run analyse

# Run tests
composer run test

# Run tests with coverage
composer run test-coverage
```

All commands should pass without errors.

## Submitting Changes

### Pull Request Process

1. **Update Documentation**: Ensure any new features are documented
2. **Update CHANGELOG**: Add an entry describing your changes
3. **Commit Messages**: Use clear, descriptive commit messages
4. **Pull Request Description**: 
   - Describe what changes you made and why
   - Reference any related issues
   - Include screenshots for UI changes (if applicable)

### Commit Message Format

Use clear and descriptive commit messages:

```
feat: add support for custom locale detection

- Add new middleware for custom locale detection
- Update tests to cover new functionality
- Update documentation

Fixes #123
```

Common prefixes:
- `feat:` - New features
- `fix:` - Bug fixes
- `docs:` - Documentation changes
- `test:` - Adding or updating tests
- `refactor:` - Code refactoring
- `style:` - Code style changes
- `chore:` - Maintenance tasks

### Pull Request Template

When creating a pull request, please include:

- **Description**: What changes were made and why
- **Type of Change**: Bug fix, new feature, breaking change, etc.
- **Testing**: How the changes were tested
- **Checklist**: Confirm all quality checks pass

## Reporting Issues

### Bug Reports

When reporting bugs, please include:

- Clear description of the issue
- Steps to reproduce
- Expected vs actual behavior
- Laravel version
- PHP version
- Package version
- Any relevant error messages or stack traces

### Feature Requests

For feature requests, please:

- Clearly describe the feature and its benefits
- Explain the use case
- Consider implementation approaches
- Check if the feature aligns with the package's goals

### Security Issues

For security-related issues, please email the maintainer directly at fulopkapasi@gmail.com instead of creating a public issue.

## Recognition

Contributors will be recognized in the project's README and release notes. Thank you for helping to improve this package!

## Questions?

If you have questions about contributing, feel free to:
- Create an issue with the "question" label
- Reach out to the maintainers
- Check the existing documentation

---

By contributing to this project, you agree that your contributions will be licensed under the same MIT license that covers the project. 