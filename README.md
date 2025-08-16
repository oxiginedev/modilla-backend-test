# Mondilla Backend Test (Track A - Laravel)

[![Tests](https://github.com/oxiginedev/modilla-backend-test/actions/workflows/tests.yml/badge.svg)](https://github.com/oxiginedev/modilla-backend-test/actions/workflows/tests.yml) [![Code Style](https://github.com/oxiginedev/modilla-backend-test/actions/workflows/lint.yml/badge.svg)](https://github.com/oxiginedev/modilla-backend-test/actions/workflows/lint.yml)

Here's my submission project for the Backend developer interview test at Modilla Designs

## Setup Guide

Follow these steps to run the project locally

1. Clone the repository
```bash
git clone git@github.com:oxiginedev/modilla-backend-test.git

cd mondilla-backend-test
```

2. Install Dependencies
```bash
composer install
```

3. Copy Environment File
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```

5. Configure Environment Variables
Update the `.env` file with your database credentials
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

6. Run Migrations & Seeders
```bash
php artisan migrate --seed --force
```

7. Start the Development Server
```bash
php artisan serve
```

The application should now be available at [http://localhost:8000](http://localhost:8000)

## Documentation

Endpoints have been documented with Postman. Find the collection within the `docs` folder

## API Responses

Success responses (without pagination) are returned like so:
```json
{
    "message": "Be, and it is",
    "data": {}
}
```

Success responses(with pagination)
```json
{
    "data": [],
    "links": {},
    "meta": {}
}
```

## API Design & Database Schema Decisions

- [x] I avoided the use of enums directly on the database. Instead i store statuses as strings, but are casted using defined PHP enum classes. This allows for extensibility in schema. Database enums are tightly coupled to be easily extended.
- [x] The Projects list endpoint does not return `draft` projects, since it's a public facing endpoint, ideally only clients should be able to see their draft projects.

## Code Architecture

I used the `Actions Pattern` for organizing business logic. Asides from the simplicity it provides, It ensures each distinct operation is contained within its own `Action` class.

### Why Actions Pattern?

Four major reasons stand out for me:
- Single Responsibility - Each action handles exactly one task
- Readability - Clear separation between controllers and business logic.
- Reusability - The classes can be reused across controllers, commands, jobs, and event listeners.
- Testability - Since each business logic is self contained within its own class, this makes ite easy to unit test.

## Tests

Both unit and integration tests have been implemented in the app. Run using the command below (All Tests Passing)
```bash
composer test
```

## Lint/Code Formatting

The codebase uses [Laravel Pint](https://laravel.com/docs/12.x/pint) for code formatting. Pint is a fluent wrapper of PHP CS Fixer. Run the command below to apply lint
```bash
composer lint
```
