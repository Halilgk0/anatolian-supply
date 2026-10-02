<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    /**
     * Serve a product photo stored in the database. File names are unique per upload,
     * so browsers and the CDN may keep a copy for a year.
     */
    public function __invoke(string $path): Response
    {
        $file = DB::table(config('filesystems.disks.database.table'))
            ->where('path', $path)
            ->first(['mime_type', 'contents']);

        abort_if($file === null, 404);

        $contents = base64_decode($file->contents);

        return response($contents, 200, [
            'Content-Type' => $file->mime_type,
            'Content-Length' => (string) strlen($contents),
            'Cache-Control' => 'public, max-age=31536000, s-maxage=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
