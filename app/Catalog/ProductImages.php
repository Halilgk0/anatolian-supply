<?php

namespace App\Catalog;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores product photos on the media disk and tells cut-out PNGs apart from ordinary photos.
 */
class ProductImages
{
    /**
     * @return array{path: string, cutout: bool}
     */
    public function store(UploadedFile $file): array
    {
        return [
            'path' => $file->store('products', config('store.media_disk')),
            'cutout' => $this->hasTransparentEdges($file->getRealPath()),
        ];
    }

    /**
     * @param  array<int, string>  $paths
     */
    public function delete(array $paths): void
    {
        if ($paths !== []) {
            Storage::disk(config('store.media_disk'))->delete(array_values($paths));
        }
    }

    /**
     * An image whose border is see-through is a cut-out product shot; anything else is shown as a photo print.
     */
    public function hasTransparentEdges(string $path): bool
    {
        if (! function_exists('imagecreatefromstring')) {
            return false;
        }

        $image = @imagecreatefromstring((string) file_get_contents($path));

        if ($image === false) {
            return false;
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        $right = imagesx($image) - 2;
        $bottom = imagesy($image) - 2;
        $middleX = intdiv($right, 2);
        $middleY = intdiv($bottom, 2);

        $samples = [[1, 1], [$right, 1], [1, $bottom], [$right, $bottom], [$middleX, 1], [$middleX, $bottom], [1, $middleY], [$right, $middleY]];

        $transparent = array_filter(
            $samples,
            fn (array $point): bool => ((imagecolorat($image, max(0, $point[0]), max(0, $point[1])) >> 24) & 0x7F) > 100,
        );

        return count($transparent) >= 6;
    }
}
