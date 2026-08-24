<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ParsePutPatchFormData
{
    /**
     * PHP does not populate $_POST for PUT/PATCH multipart requests.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getMethod(), ['PUT', 'PATCH'], true)) {
            return $next($request);
        }

        $contentType = (string) $request->header('Content-Type');
        if (! str_contains($contentType, 'multipart/form-data')) {
            return $next($request);
        }

        $boundary = $this->extractBoundary($contentType);
        if ($boundary === null) {
            return $next($request);
        }

        $content = $request->getContent();
        if ($content === '' || $content === false) {
            return $next($request);
        }

        [$fields, $files] = $this->parseMultipart($content, $boundary);

        if ($fields !== []) {
            $request->merge($fields);
        }

        if ($files !== []) {
            $request->files->replace(array_merge($request->files->all(), $files));
        }

        return $next($request);
    }

    private function extractBoundary(string $contentType): ?string
    {
        if (preg_match('/boundary=(.*)$/is', $contentType, $matches)) {
            return trim($matches[1], " \"'");
        }

        return null;
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, UploadedFile>}
     */
    private function parseMultipart(string $content, string $boundary): array
    {
        $fields = [];
        $files = [];
        $blocks = preg_split('/-+.'.preg_quote($boundary, '/').'/', $content);
        $blocks = array_slice($blocks, 1, -1);

        foreach ($blocks as $block) {
            if ($block === '' || $block === "--\r\n") {
                continue;
            }

            if (! preg_match('/name="([^"]+)"/', $block, $nameMatch)) {
                continue;
            }

            $name = $nameMatch[1];
            $parts = preg_split("/\r\n\r\n/", $block, 2);
            if (count($parts) < 2) {
                continue;
            }

            $value = rtrim($parts[1], "\r\n");

            if (preg_match('/filename="([^"]*)"/', $block, $fileMatch)) {
                $filename = $fileMatch[1];
                if ($filename === '') {
                    continue;
                }

                $tmpPath = tempnam(sys_get_temp_dir(), 'laravel');
                if ($tmpPath === false) {
                    continue;
                }

                file_put_contents($tmpPath, $value);

                $symfonyFile = new SymfonyUploadedFile(
                    $tmpPath,
                    $filename,
                    mime_content_type($tmpPath) ?: null,
                    null,
                    true
                );

                $files[$name] = UploadedFile::createFromBase($symfonyFile);

                continue;
            }

            $fields[$name] = $value;
        }

        return [$fields, $files];
    }
}
