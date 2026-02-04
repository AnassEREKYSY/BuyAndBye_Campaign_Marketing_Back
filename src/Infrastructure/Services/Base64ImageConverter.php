<?php

declare(strict_types=1);

namespace Src\Infrastructure\Services;

use Illuminate\Http\UploadedFile;

class Base64ImageConverter
{
    public function toDataUri(UploadedFile $file): string
    {
        $mime = $file->getMimeType() ?? 'application/octet-stream';
        $content = file_get_contents($file->getRealPath());
        $base64 = base64_encode($content === false ? '' : $content);

        return "data:{$mime};base64,{$base64}";
    }

    /**
     * @param UploadedFile[] $files
     * @return string[]
     */
    public function toDataUriList(array $files): array
    {
        return array_map(fn (UploadedFile $file): string => $this->toDataUri($file), $files);
    }
}
