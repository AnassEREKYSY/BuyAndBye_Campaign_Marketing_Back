<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Contracts\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Stores uploads in a public Supabase Storage bucket through its REST API.
 * Used in production (Vercel), where the local filesystem is read-only.
 */
class SupabaseFileStorage implements FileStorageInterface
{
    private string $baseUrl;
    private string $key;
    private string $bucket;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.supabase.url'), '/');
        $this->key = (string) config('services.supabase.service_key');
        $this->bucket = (string) config('services.supabase.bucket', 'uploads');
    }

    public function storeUserAvatar(string $userId, UploadedFile $file): string
    {
        return $this->put("users/{$userId}/avatar", $file);
    }

    public function storeStoreBanner(string $userId, UploadedFile $file): string
    {
        return $this->put("users/{$userId}/store-banner", $file);
    }

    public function storeProductImage(string $userId, string $productId, UploadedFile $file): string
    {
        return $this->put("users/{$userId}/products/{$productId}", $file);
    }

    public function deleteByUrl(string $publicUrl): void
    {
        $marker = "/storage/v1/object/public/{$this->bucket}/";
        if (! Str::contains($publicUrl, $marker)) {
            return;
        }

        $path = Str::after($publicUrl, $marker);

        Http::withToken($this->key)
            ->withHeaders(['apikey' => $this->key])
            ->delete("{$this->baseUrl}/storage/v1/object/{$this->bucket}", ['prefixes' => [$path]]);
    }

    private function put(string $folder, UploadedFile $file): string
    {
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin';
        $path = trim($folder, '/') . '/' . Str::uuid() . '.' . $extension;

        $response = Http::withToken($this->key)
            ->withHeaders([
                'apikey' => $this->key,
                'x-upsert' => 'true',
                'cache-control' => '3600',
            ])
            ->withBody((string) file_get_contents($file->getRealPath()), $file->getMimeType() ?: 'application/octet-stream')
            ->post("{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$path}");

        if ($response->failed()) {
            throw new RuntimeException('Upload to Supabase Storage failed: ' . $response->body());
        }

        return "{$this->baseUrl}/storage/v1/object/public/{$this->bucket}/{$path}";
    }
}
