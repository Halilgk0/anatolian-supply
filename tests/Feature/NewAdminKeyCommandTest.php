<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->environmentPath = storage_path('framework/testing/admin-key-'.uniqid());
    File::ensureDirectoryExists($this->environmentPath);
    app()->useEnvironmentPath($this->environmentPath);
});

afterEach(function () {
    File::deleteDirectory($this->environmentPath);
});

test('command replaces the admin key in the env file and prints the new link', function () {
    File::put($this->environmentPath.'/.env', "APP_NAME=Test\nSTORE_ADMIN_KEY=eski-anahtar\nSTORE_MEDIA_DISK=public\n");

    $this->artisan('yonetim:yeni-anahtar', ['--adres' => 'http://192.168.1.105:8000/'])
        ->expectsOutputToContain('http://192.168.1.105:8000/yonetim/')
        ->assertSuccessful();

    $contents = File::get($this->environmentPath.'/.env');

    expect($contents)
        ->not->toContain('eski-anahtar')
        ->toMatch('/^STORE_ADMIN_KEY=[A-Za-z0-9]{40}$/m')
        ->toContain("APP_NAME=Test\n")
        ->toContain("STORE_MEDIA_DISK=public\n");
});

test('command adds the admin key when the env file has none', function () {
    File::put($this->environmentPath.'/.env', "APP_NAME=Test\n");

    $this->artisan('yonetim:yeni-anahtar')->assertSuccessful();

    expect(File::get($this->environmentPath.'/.env'))->toMatch('/^STORE_ADMIN_KEY=[A-Za-z0-9]{40}$/m');
});

test('command explains what to do when there is no env file', function () {
    $this->artisan('yonetim:yeni-anahtar')
        ->expectsOutputToContain('.env dosyası bulunamadı')
        ->assertFailed();
});
