# Laravel Survey Tool (Beta)

A modern, flexible survey creation and management tool built with Laravel, Tailwind CSS, Alpine.js, and SurveyJS.

⚠️ **BETA WARNING**: This package is currently in early beta and is not recommended for production use. Breaking changes may be introduced without notice.

## Features

- Survey creation and management
- Responsive design using Tailwind CSS
- Interactive UI components with Alpine.js
- Professional survey rendering with SurveyJS
- Built on Laravel's robust backend framework

## Requirements

- PHP >= 8.1
- Laravel >= 9.0
- Node.js >= 16.0
- Composer
- NPM or Yarn

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/laravel-survey-tool.git
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
# or
yarn install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Build assets:
```bash
npm run dev
# or
yarn dev
```

## Configuration

1. Update your `.env` file with your database credentials
2. Configure your SurveyJS license key (if applicable)
3. Set up any additional environment variables as needed

## Development Setup

For local development:

```bash
php artisan serve
```

In a separate terminal:
```bash
npm run watch
# or
yarn watch
```

## Current Limitations

- Limited question types available
- No export functionality yet
- Basic reporting capabilities
- Limited customization options
- Potential breaking changes in future updates

## Contributing

As this project is in early beta, we're not currently accepting public contributions. Please feel free to open issues for bug reports or feature requests.

## Security

If you discover any security-related issues, please email security@yourdomain.com instead of using the issue tracker.

## License

This project is currently private and not licensed for public use.

## Roadmap

- [ ] Expanded question types
- [ ] Advanced reporting
- [ ] Export functionality
- [ ] Custom themes
- [ ] API documentation
- [ ] Integration tests
- [ ] Performance optimizations

## Support

For questions and support, please open an issue in the repository.
