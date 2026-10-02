<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('yonetim:yeni-anahtar {--adres= : Siteyi açtığın adres, örn. http://192.168.1.105:8000}')]
#[Description('Ürün yönetimi paneli için yeni bir gizli bağlantı oluşturur; eski bağlantı çalışmaz hale gelir.')]
class NewAdminKey extends Command
{
    /**
     * Replace STORE_ADMIN_KEY in the .env file and print the new admin link.
     */
    public function handle(): int
    {
        $environmentFile = $this->laravel->environmentFilePath();

        if (! is_file($environmentFile)) {
            $this->components->error('.env dosyası bulunamadı. Önce .env.example dosyasını .env adıyla kopyala.');

            return self::FAILURE;
        }

        $key = Str::random(40);
        $contents = (string) file_get_contents($environmentFile);

        $contents = preg_match('/^STORE_ADMIN_KEY=.*$/m', $contents)
            ? (string) preg_replace('/^STORE_ADMIN_KEY=.*$/m', "STORE_ADMIN_KEY={$key}", $contents)
            : rtrim($contents)."\n\nSTORE_ADMIN_KEY={$key}\n";

        file_put_contents($environmentFile, $contents);

        $this->callSilently('config:clear');

        $address = rtrim((string) ($this->option('adres') ?: config('app.url')), '/');

        $this->components->info('Yeni panel bağlantısı oluşturuldu. Eski bağlantı artık “sayfa bulunamadı” gösterir.');
        $this->line("  <options=bold>{$address}/yonetim/{$key}</>");
        $this->newLine();

        return self::SUCCESS;
    }
}
