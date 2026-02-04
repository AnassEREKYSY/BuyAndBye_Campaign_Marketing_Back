<?php

declare(strict_types=1);

namespace Src\Domain\Users\Repositories;

use Src\Domain\Users\Entities\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $attributes): User
    {
