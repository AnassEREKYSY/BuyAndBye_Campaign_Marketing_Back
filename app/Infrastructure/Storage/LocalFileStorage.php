<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Contracts\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalFileStorage implements FileStorageInterface
{
    public function storeUserAvatar(string $userId, UploadedFile $file): string
    {
        $path = $file->store("users/{$userId}/avatar", 'public');
        return Storage::url($path);
    }

    public function storeStoreBanner(string $userId, UploadedFile $file): string
    {
        $path = $file->store("users/{$userId}/store-banner", 'public');
        return Storage::url($path);
    }

    public function deleteByUrl(string $publicUrl): void
    {
        $path = $publicUrl;
        if (Str::startsWith($path, ['http://', 'https://'])) {
            $path = parse_url($path, PHP_URL_PATH) ?: $path;
        }
        $prefix = '/storage/';
        if (Str::startsWith($path, $prefix)) {
            $path = Str::after($path, $prefix);
        }

        if ($path !== '' && $path !== '/' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function storeProductImage(string $userId, string $productId, UploadedFile $file): string
{
    $path = $file->store("users/{$userId}/products/{$productId}", 'public');
    return Storage::url($path);
}
}
