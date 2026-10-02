<?php

namespace App\Catalog;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

/**
 * Keeps uploaded product photos in a database table, for hosts like Vercel where
 * the server's own disk is wiped whenever an instance is replaced.
 */
class DatabaseMediaAdapter implements FilesystemAdapter
{
    public function __construct(
        private ConnectionInterface $connection,
        private string $table,
        private string $url,
    ) {}

    public function getUrl(string $path): string
    {
        return rtrim($this->url, '/').'/'.ltrim($path, '/');
    }

    public function fileExists(string $path): bool
    {
        return $this->query()->where('path', $path)->exists();
    }

    public function directoryExists(string $path): bool
    {
        return $this->query()->where('path', 'like', rtrim($path, '/').'/%')->exists();
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $mimeType = (new FinfoMimeTypeDetector)->detectMimeType($path, $contents) ?? 'application/octet-stream';

        $this->query()->updateOrInsert(['path' => $path], [
            'mime_type' => $mimeType,
            'size' => strlen($contents),
            'contents' => base64_encode($contents),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, (string) stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        $encoded = $this->query()->where('path', $path)->value('contents');

        if ($encoded === null) {
            throw UnableToReadFile::fromLocation($path, 'File not found.');
        }

        return base64_decode($encoded);
    }

    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $this->read($path));
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $this->query()->where('path', $path)->delete();
    }

    public function deleteDirectory(string $path): void
    {
        $this->query()->where('path', 'like', rtrim($path, '/').'/%')->delete();
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Directories only exist as path prefixes here, so there is nothing to create.
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Media files are always public.');
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, visibility: 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($path, mimeType: $this->metadata($path)->mime_type);
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path, lastModified: strtotime((string) $this->metadata($path)->updated_at) ?: null);
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path, fileSize: (int) $this->metadata($path)->size);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $prefix = trim($path, '/');

        $rows = $this->query()
            ->when($prefix !== '', fn (Builder $query) => $query->where('path', 'like', $prefix.'/%'))
            ->orderBy('path')
            ->get(['path', 'size', 'mime_type', 'updated_at']);

        foreach ($rows as $row) {
            $relative = $prefix === '' ? $row->path : substr($row->path, strlen($prefix) + 1);

            if (! $deep && str_contains($relative, '/')) {
                continue;
            }

            yield new FileAttributes($row->path, (int) $row->size, 'public', strtotime((string) $row->updated_at) ?: null, $row->mime_type);
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        if (! $this->fileExists($source)) {
            throw UnableToMoveFile::fromLocationTo($source, $destination);
        }

        $this->copy($source, $destination, $config);
        $this->delete($source);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        if (! $this->fileExists($source)) {
            throw UnableToCopyFile::fromLocationTo($source, $destination);
        }

        $this->write($destination, $this->read($source), $config);
    }

    private function metadata(string $path): object
    {
        return $this->query()->where('path', $path)->first(['mime_type', 'size', 'updated_at'])
            ?? throw UnableToRetrieveMetadata::create($path, 'metadata', 'File not found.');
    }

    private function query(): Builder
    {
        return $this->connection->table($this->table);
    }
}
