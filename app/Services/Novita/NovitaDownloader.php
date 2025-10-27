<?php

namespace App\Services\Novita;

use Spatie\MediaLibrary\Downloaders\Downloader;
use Spatie\MediaLibrary\MediaCollections\Exceptions\UnreachableUrl;

class NovitaDownloader implements Downloader
{
    public function __construct()
    {}

    public function getTempFile(string $url): string
    {
        $accessToken = config('services.novita.key');

        $context = stream_context_create([
            'http' => [
                'header' => [
                    'User-Agent: Spatie MediaLibrary',
//                    "Authorization: Bearer {$accessToken}"
                ],
            ],
        ]);
\Llog::warning($url);
        if (! $stream = @fopen($url, 'r', false, $context)) {
            throw UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'media-library');

        file_put_contents($temporaryFile, $stream);

        fclose($stream);

        return $temporaryFile;
    }
}

