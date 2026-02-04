<?php

declare(strict_types=1);

namespace Src\Application\Users\DTOs;

class GetUserProfileResponse
{
    public function __construct(
        public UserResponse $user,
        public ?array $preferences,
        public ?array $businessInfo,
        public ?array $paymentMethods
    ) {
    }
}
