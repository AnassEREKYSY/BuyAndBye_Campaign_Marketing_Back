<?php

declare(strict_types=1);

namespace App\Application\UseCases\Auth; 


use App\Application\Dtos\Auth\LoginUserDTO; 
use App\Domain\Contracts\UserRepositoryInterface;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;

class LoginUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(LoginUserDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw InvalidCredentialsException::create();
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [$user, $token];
    }
}
