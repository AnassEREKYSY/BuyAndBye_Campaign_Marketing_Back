# Buy & Bye API

Buy & Bye is a live‑commerce marketplace platform (similar to Whatnot and Vinted).
This repository contains the REST API backend.

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- PostgreSQL (local) or Docker

### Clone

```bash
git clone <your-repo-url>
cd BuyAndByeApi
```

### Environment

Create a local environment file and set PostgreSQL values:

```bash
cp .env.example .env
```

### Docker (API + PostgreSQL)

```bash
docker compose up --build
```

### Local PHP (without Docker)

```bash
composer install
```

Then configure your database credentials in `.env`.

## Technology Stack

- Laravel LTS (PHP 8.2)
- PostgreSQL
- Laravel Sanctum (JWT-based auth)
- Docker (optional local development)

## Architecture

The project uses Clean Architecture and DDD principles with clear layer
separation. The domain is organized by bounded context.

### Layers

- **Domain** (`src/Domain/{BoundedContext}`): entities, value objects, domain
  events, exceptions, repository interfaces, domain service interfaces.
- **Application** (`src/Application/{BoundedContext}`): use cases, DTOs,
  application services, and mappers.
- **Infrastructure** (`src/Infrastructure`): Eloquent models, repository
  implementations, external services, jobs, and event listeners.
- **Interface** (Laravel `app/`): HTTP controllers, requests, resources,
  middleware, policies, and routes.

### Request Flow

1. A controller receives a request.
2. The controller calls a use case.
3. The use case coordinates domain entities and repository interfaces.
4. Infrastructure provides repository implementations and external services.
5. A resource formats the response.
