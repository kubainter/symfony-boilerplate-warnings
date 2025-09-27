# symfony-boilerplate-warnings

## Project requirements
- PHP 8.2+
- Symfony 6.4+ or 7.x
- Doctrine ORM
- MySQL database
- Composer
- Docker/Docker Compose (recommended)

## Quick start (Docker)
```sh
docker compose up -d
```

## Quick start (local)
```sh
composer install
cp app/.env app/.env.local # configure your DB connection
symfony server:start
```

## Local run (without Docker)

You can run the project without Docker using your local PHP and MySQL installation.

1. Make sure you have MySQL running locally on port 3306.
2. Create file `app/.env.local` with:
   DATABASE_URL="mysql://symfony:symfony@127.0.0.1:3306/symfony?serverVersion=8.0&charset=utf8mb4"
3. Install dependencies:
   composer install
4. Run Symfony server:
   symfony server:start
5. Run migrations and fixtures:
   php bin/console doctrine:migrations:migrate
   php bin/console doctrine:fixtures:load
```

## Database migration
```sh
docker compose exec php bin/console doctrine:migrations:migrate
```

## Load sample data (fixtures)
```sh
docker compose exec php bin/console doctrine:fixtures:load
```

## Sample data (fixtures)

Sample contractors, budgets, and invoices are provided via Doctrine fixtures for quick start and testing warning generation.

To load sample data:
```sh
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```
Or locally (without Docker):
```sh
php bin/console doctrine:fixtures:load --no-interaction
```

Fixtures include:
- Contractors (with and without overdue invoices)
- Budgets (positive and negative balance)
- Invoices (paid, unpaid, overdue)

After loading fixtures, you can run the warning generation CLI to test business rules:
```sh
docker compose exec php php bin/console app:warnings:generate
```

## Generate warnings (CLI)
```sh
docker compose exec php bin/console app:warnings:generate
```

## Static analysis (PHPStan)

PHPStan is used for basic static code analysis (level 5).

To run PHPStan:
```sh
composer phpstan
```
Or directly:
```sh
vendor/bin/phpstan analyse -c app/phpstan.neon
```

## Architecture overview
- DDD light: separate modules (Core, Finance) and layers (Domain, Application, Infrastructure)
- Repositories as interfaces in domain; implementations in infrastructure
- Entities with domain logic, but keep it reasonable
- Migrations reproduce full schema
- Fixtures for quick start (sample contractors, budgets, invoices)
- CLI command orchestrates warning generators (no domain logic in command)
- PSR-12 code style, basic static analysis
