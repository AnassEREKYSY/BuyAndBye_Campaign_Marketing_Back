<?php

declare(strict_types=1);

namespace Src\Application\Shared\DTOs;

class ApiResponse
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data = null,
        public ?array $errors = null
    ) {
    }
}
