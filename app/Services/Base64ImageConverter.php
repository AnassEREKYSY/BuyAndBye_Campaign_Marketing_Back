<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;

class Base64ImageConverter
{
    public function toDataUri(UploadedFile $file): string
    {
        $fileContent = file_get_contents($file->getRealPath());
        $base64 = base64_encode($fileContent);
        $mimeType = $file->getMimeType();

        return "data:{$mimeType};base64,{$base64}";
    }

    public function toDataUriList(array $files): array
    {
        return array_map(fn (UploadedFile $file) => $this->toDataUri($file), $files);
    }
}
