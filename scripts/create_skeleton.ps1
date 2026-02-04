$root = 'D:\My Data\Buy&Bye\B&B_API\BuyAndByeApi'
$contexts = @(
  @{ Name = 'Auth'; Entity = 'Auth'; Controller = 'AuthsController' },
  @{ Name = 'Users'; Entity = 'User'; Controller = 'UsersController' },
  @{ Name = 'SellerProfiles'; Entity = 'SellerProfile'; Controller = 'SellerProfilesController' },
  @{ Name = 'Products'; Entity = 'Product'; Controller = 'ProductsController' },
  @{ Name = 'Cart'; Entity = 'Cart'; Controller = 'CartsController' },
  @{ Name = 'Orders'; Entity = 'Order'; Controller = 'OrdersController' },
  @{ Name = 'Payments'; Entity = 'Payment'; Controller = 'PaymentsController' },
  @{ Name = 'Payouts'; Entity = 'Payout'; Controller = 'PayoutsController' },
  @{ Name = 'Shipping'; Entity = 'Shipping'; Controller = 'ShippingsController' },
  @{ Name = 'Reviews'; Entity = 'Review'; Controller = 'ReviewsController' },
  @{ Name = 'Reports'; Entity = 'Report'; Controller = 'ReportsController' },
  @{ Name = 'LiveStreams'; Entity = 'LiveStream'; Controller = 'LiveStreamsController' },
  @{ Name = 'Chat'; Entity = 'Chat'; Controller = 'ChatsController' },
  @{ Name = 'Notifications'; Entity = 'Notification'; Controller = 'NotificationsController' },
  @{ Name = 'Coupons'; Entity = 'Coupon'; Controller = 'CouponsController' },
  @{ Name = 'Categories'; Entity = 'Category'; Controller = 'CategoriesController' },
  @{ Name = 'Audit'; Entity = 'Audit'; Controller = 'AuditsController' },
  @{ Name = 'Health'; Entity = 'Health'; Controller = 'HealthsController' }
)

$dirs = @(
  'app/Http/Controllers/Api/V1',
  'app/Http/Requests',
  'app/Http/Resources',
  'app/Http/Middleware',
  'app/Policies',
  'routes',
  'config',
  'database/migrations',
  'src/Domain',
  'src/Application',
  'src/Infrastructure/Persistence/Eloquent/Models',
  'src/Infrastructure/Persistence/Repositories',
  'src/Infrastructure/Services',
  'src/Infrastructure/Jobs',
  'src/Infrastructure/Events',
  'tests/Unit/Domain',
  'tests/Feature/Api/V1'
)

foreach ($dir in $dirs) {
  New-Item -ItemType Directory -Path (Join-Path $root $dir) -Force | Out-Null
}

foreach ($ctx in $contexts) {
  $name = $ctx.Name
  $entity = $ctx.Entity
  $controller = $ctx.Controller

  $domainBase = Join-Path $root ("src/Domain/$name")
  New-Item -ItemType Directory -Path (Join-Path $domainBase 'Entities') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $domainBase 'ValueObjects') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $domainBase 'Events') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $domainBase 'Exceptions') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $domainBase 'Repositories') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $domainBase 'Services') -Force | Out-Null

  $content = @'
<?php

namespace Src\Domain\{0}\Entities;

class {1}
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $domainBase "Entities/$entity.php") -Value $content

  $content = @'
<?php

namespace Src\Domain\{0}\ValueObjects;

class {1}Id
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $domainBase "ValueObjects/${entity}Id.php") -Value $content

  $content = @'
<?php

namespace Src\Domain\{0}\Events;

class {1}Created
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $domainBase "Events/${entity}Created.php") -Value $content

  $content = @'
<?php

namespace Src\Domain\{0}\Exceptions;

class {1}Exception extends \Exception
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $domainBase "Exceptions/${entity}Exception.php") -Value $content

  $content = @'
<?php

namespace Src\Domain\{0}\Repositories;

interface {1}Repository
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $domainBase "Repositories/${entity}Repository.php") -Value $content

  $content = @'
<?php

namespace Src\Domain\{0}\Services;

interface {0}Service
{{
}}
'@ -f $name
  Set-Content -Path (Join-Path $domainBase "Services/${name}Service.php") -Value $content

  $appBase = Join-Path $root ("src/Application/$name")
  New-Item -ItemType Directory -Path (Join-Path $appBase 'UseCases') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $appBase 'DTOs') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $appBase 'Services') -Force | Out-Null
  New-Item -ItemType Directory -Path (Join-Path $appBase 'Mappers') -Force | Out-Null

  $content = @'
<?php

namespace Src\Application\{0}\UseCases;

class Create{1}UseCase
{{
    public function __invoke(): void
    {{
    }}
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $appBase "UseCases/Create${entity}UseCase.php") -Value $content

  $content = @'
<?php

namespace Src\Application\{0}\DTOs;

class {1}DTO
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $appBase "DTOs/${entity}DTO.php") -Value $content

  $content = @'
<?php

namespace Src\Application\{0}\Services;

class {0}Service
{{
}}
'@ -f $name
  Set-Content -Path (Join-Path $appBase "Services/${name}Service.php") -Value $content

  $content = @'
<?php

namespace Src\Application\{0}\Mappers;

class {1}Mapper
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $appBase "Mappers/${entity}Mapper.php") -Value $content

  $content = @'
<?php

namespace Src\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class {0} extends Model
{{
}}
'@ -f $entity
  Set-Content -Path (Join-Path $root "src/Infrastructure/Persistence/Eloquent/Models/${entity}.php") -Value $content

  $content = @'
<?php

namespace Src\Infrastructure\Persistence\Repositories;

use Src\Domain\{0}\Repositories\{1}Repository as {1}RepositoryInterface;

class {1}Repository implements {1}RepositoryInterface
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $root "src/Infrastructure/Persistence/Repositories/${entity}Repository.php") -Value $content

  $content = @'
<?php

namespace Src\Infrastructure\Services;

class {0}Service
{{
}}
'@ -f $name
  Set-Content -Path (Join-Path $root "src/Infrastructure/Services/${name}Service.php") -Value $content

  $content = @'
<?php

namespace Src\Infrastructure\Jobs;

class {0}Job
{{
}}
'@ -f $name
  Set-Content -Path (Join-Path $root "src/Infrastructure/Jobs/${name}Job.php") -Value $content

  $content = @'
<?php

namespace Src\Infrastructure\Events;

class {0}EventListener
{{
}}
'@ -f $name
  Set-Content -Path (Join-Path $root "src/Infrastructure/Events/${name}EventListener.php") -Value $content

  $controllerDir = Join-Path $root ("app/Http/Controllers/Api/V1/$name")
  New-Item -ItemType Directory -Path $controllerDir -Force | Out-Null
  $content = @'
<?php

namespace App\Http\Controllers\Api\V1\{0};

use App\Http\Controllers\Controller;

class {1} extends Controller
{{
    public function index(): void
    {{
    }}

    public function store(): void
    {{
    }}

    public function show(): void
    {{
    }}

    public function update(): void
    {{
    }}

    public function destroy(): void
    {{
    }}
}}
'@ -f $name, $controller
  Set-Content -Path (Join-Path $controllerDir "$controller.php") -Value $content

  $requestDir = Join-Path $root ("app/Http/Requests/$name")
  New-Item -ItemType Directory -Path $requestDir -Force | Out-Null
  $content = @'
<?php

namespace App\Http\Requests\{0};

use Illuminate\Foundation\Http\FormRequest;

class {0}Request extends FormRequest
{{
}}
'@ -f $name
  Set-Content -Path (Join-Path $requestDir "${name}Request.php") -Value $content

  $resourceDir = Join-Path $root ("app/Http/Resources/$name")
  New-Item -ItemType Directory -Path $resourceDir -Force | Out-Null
  $content = @'
<?php

namespace App\Http\Resources\{0};

use Illuminate\Http\Resources\Json\JsonResource;

class {0}Resource extends JsonResource
{{
}}
'@ -f $name
  Set-Content -Path (Join-Path $resourceDir "${name}Resource.php") -Value $content

  $content = @'
<?php

namespace App\Policies;

class {0}Policy
{{
}}
'@ -f $entity
  Set-Content -Path (Join-Path $root "app/Policies/${entity}Policy.php") -Value $content

  $unitDir = Join-Path $root ("tests/Unit/Domain/$name")
  New-Item -ItemType Directory -Path $unitDir -Force | Out-Null
  $content = @'
<?php

namespace Tests\Unit\Domain\{0};

use PHPUnit\Framework\TestCase;

class {1}Test extends TestCase
{{
}}
'@ -f $name, $entity
  Set-Content -Path (Join-Path $unitDir "${entity}Test.php") -Value $content

  $featureDir = Join-Path $root ("tests/Feature/Api/V1/$name")
  New-Item -ItemType Directory -Path $featureDir -Force | Out-Null
  $content = @'
<?php

namespace Tests\Feature\Api\V1\{0};

use Tests\TestCase;

class {1}Test extends TestCase
{{
}}
'@ -f $name, $controller
  Set-Content -Path (Join-Path $featureDir "${controller}Test.php") -Value $content
}

Set-Content -Path (Join-Path $root 'app/Http/Middleware/ApiVersionMiddleware.php') -Value @'
<?php

namespace App\Http\Middleware;

class ApiVersionMiddleware
{
}
'@

Set-Content -Path (Join-Path $root 'routes/api.php') -Value @'
<?php

// V1 Routes
'@

Set-Content -Path (Join-Path $root 'config/sanctum.php') -Value @'
<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:8000,localhost:8080,localhost:4200,localhost:5173',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    'guard' => ['web'],

    'expiration' => null,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ],
];
'@

Set-Content -Path (Join-Path $root 'database/migrations/README.md') -Value @'
# Migrations

// Placeholder for future migrations.
'@

Set-Content -Path (Join-Path $root '.env.example') -Value @'
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
'@

Set-Content -Path (Join-Path $root 'composer.json') -Value @'
{
  "name": "buyandbye/api",
  "type": "project",
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0",
    "doctrine/dbal": "^4.0"
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Src\\": "src/"
    }
  }
}
'@

Set-Content -Path (Join-Path $root 'README.md') -Value @'
# Buy & Bye API

Laravel-based REST API for the Buy & Bye live-commerce marketplace platform.

## Architecture

- Clean Architecture + DDD layering
- Domain/Application/Infrastructure layers under "src/"
- Interface layer via Laravel HTTP controllers/resources/requests
'@
