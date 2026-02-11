<?php

declare(strict_types=1);

namespace App\Application\Dtos\Profile; 

class UpdateProfileDTO
{
    public function __construct(
        public readonly ?string $displayName,
        public readonly ?string $photoUrl,
    ) {}
}
