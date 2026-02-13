<?php

namespace App\Domain\Contracts;

use Illuminate\Http\UploadedFile;

interface FileStorageInterface
{
    public function storeUserAvatar(string $userId, UploadedFile $file): string;

    public function storeStoreBanner(string $userId, UploadedFile $file): string;

    public function storeProductImage(string $userId, string $productId, UploadedFile $file): string;

    public function deleteByUrl(string $publicUrl): void;
}