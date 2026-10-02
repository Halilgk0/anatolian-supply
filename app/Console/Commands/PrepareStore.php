<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('magaza:hazirla')]
#[Description('Veritabanı tablolarını günceller; hiç ürün yoksa örnek ürünleri ekler.')]
class PrepareStore extends Command
{
    /**
     * Bring the schema up to date and seed the demo catalog into an empty store.
     * Safe to run on every start: existing products are never touched.
     */
    public function handle(): int
    {
        $this->call('migrate', ['--force' => true]);

        if (Product::query()->exists()) {
            $this->components->info('Ürünler zaten var; örnek ürün eklenmedi.');

            return self::SUCCESS;
        }

        $this->call('db:seed', ['--force' => true]);
        $this->components->info('Mağaza boştu; örnek ürünler eklendi.');

        return self::SUCCESS;
    }
}
