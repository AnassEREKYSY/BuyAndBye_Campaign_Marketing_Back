<?php

declare(strict_types=1);

namespace Src\Infrastructure\Services;

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
}