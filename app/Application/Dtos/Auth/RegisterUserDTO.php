<?php

declare(strict_types=1);

namespace App\Application\Dtos\Auth;

use App\Enums\UserRole;
use Illuminate\Http\UploadedFile;

final class RegisterUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $displayName,
        public readonly UserRole $role,
        public readonly ?UploadedFile $photo,
    ) {}
}