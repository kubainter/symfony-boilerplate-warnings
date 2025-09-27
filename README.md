# symfo-base-skeleton

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

## Database migration
```sh
docker compose exec php bin/console doctrine:migrations:migrate
```

## Load sample data (fixtures)
```sh
docker compose exec php bin/console doctrine:fixtures:load
```

## Generate warnings (CLI)
```sh
docker compose exec php bin/console app:warnings:generate
```

## Architecture overview
- DDD light: separate modules (Core, Finance) and layers (Domain, Application, Infrastructure)
- Repositories as interfaces in domain; implementations in infrastructure
- Entities with domain logic, but keep it reasonable
- Migrations reproduce full schema
- Fixtures for quick start (sample contractors, budgets, invoices)
- CLI command orchestrates warning generators (no domain logic in command)
- PSR-12 code style, basic static analysis
