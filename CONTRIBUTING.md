# Contributing to IONOS DNS API Client

Thank you for your interest in contributing to the IONOS DNS API Client! We welcome contributions from the community.

## Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/davidnsai/ionos-api.git
   cd ionos-dns-api-client
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Run tests**
   ```bash
   composer test
   ```

## Code Standards

This project follows PSR-12 coding standards. Please ensure your code complies with these standards.

### Check code style
```bash
composer cs-check
```

### Fix code style automatically
```bash
composer cs-fix
```

### Run static analysis
```bash
composer analyse
```

## Testing

- Write tests for new features and bug fixes
- Ensure all tests pass before submitting a pull request
- Aim for good test coverage

### Run tests with coverage
```bash
composer test-coverage
```

## Pull Request Process

1. **Fork the repository** and create your branch from `main`
2. **Make your changes** ensuring they follow the coding standards
3. **Add tests** for your changes
4. **Update documentation** if necessary
5. **Ensure all tests pass** and code style is correct
6. **Submit a pull request** with a clear description of your changes

## Pull Request Guidelines

- **Title**: Use a clear and descriptive title
- **Description**: Explain what your changes do and why
- **Testing**: Describe how you tested your changes
- **Breaking Changes**: Clearly document any breaking changes

## Reporting Issues

When reporting issues, please include:

- **PHP version**
- **Library version**
- **Detailed description** of the issue
- **Steps to reproduce** the issue
- **Expected behavior**
- **Actual behavior**
- **Code examples** (if applicable)

## Feature Requests

We welcome feature requests! Please:

- **Check existing issues** to avoid duplicates
- **Describe the feature** in detail
- **Explain the use case** and why it would be beneficial
- **Consider implementation** if you're willing to contribute

## Code Review Process

All submissions require review. We use GitHub pull requests for this purpose. The maintainers will:

- Review your code for quality and style
- Test your changes
- Provide feedback if changes are needed
- Merge your contribution once approved

## License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.

## Questions?

If you have questions about contributing, feel free to:

- Open an issue for discussion
- Contact the maintainers

Thank you for contributing!
