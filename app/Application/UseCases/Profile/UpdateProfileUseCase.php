<?php

declare(strict_types=1);

namespace App\Application\UseCases\Profile;

use App\Application\Dtos\Profile\UpdateProfileDTO;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Models\User;

class UpdateProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $user, UpdateProfileDTO $dto): void
    {
        $updateData = array_filter([
            'display_name' => $dto->displayName,
            'photo_url' => $dto->photoUrl,
        ], fn ($value) => $value !== null);

        if (! empty($updateData)) {
            $this->userRepository->update($user, $updateData);
        }
    }
}
