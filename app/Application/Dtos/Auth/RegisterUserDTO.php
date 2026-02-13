<?php

declare(strict_types=1);

namespace App\Application\Dtos\Auth;

use Illuminate\Http\UploadedFile;

final class RegisterUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $displayName,
        public readonly ?UploadedFile $photo,
    ) {}
}
