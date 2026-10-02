<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storefront Contact Channels
    |--------------------------------------------------------------------------
    |
    | The site does not sell directly. Product pages send visitors to the
    | Instagram account, and information requests are emailed to the
    | inquiry address below.
    |
    */

    'instagram_url' => env('STORE_INSTAGRAM_URL', 'https://www.instagram.com/anatoliansupplyco/?utm_source=ig_web_button_share_sheet'),

    'instagram_handle' => env('STORE_INSTAGRAM_HANDLE', 'anatoliansupplyco'),

    'inquiry_email' => env('STORE_INQUIRY_EMAIL', 'anatoliansupplyco@gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | Product Management
    |--------------------------------------------------------------------------
    |
    | The product admin lives behind a secret link: /yonetim/{admin_key}.
    | Leave the key empty to switch the admin off. Product photos are
    | stored on the media disk: "public" on a normal server, "database"
    | where the server's own disk is not durable (Vercel).
    |
    */

    'admin_key' => env('STORE_ADMIN_KEY'),

    'media_disk' => env('STORE_MEDIA_DISK', 'public'),

    /*
    | Run pending migrations on the first request of each server instance.
    | Meant for Vercel, where there is no place to run "php artisan migrate".
    */

    'auto_migrate' => (bool) env('STORE_AUTO_MIGRATE', false),

];
