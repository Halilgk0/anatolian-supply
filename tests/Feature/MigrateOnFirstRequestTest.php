<?php

use App\Http\Middleware\MigrateOnFirstRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

const PURCHASE_URL_MIGRATION = '2026_10_02_190250_add_purchase_url_to_products_table';

beforeEach(function () {
    $this->migrateOnFirstRequest = app(MigrateOnFirstRequest::class);
    @unlink($this->migrateOnFirstRequest->markerPath());
});

afterEach(function () {
    @unlink($this->migrateOnFirstRequest->markerPath());
});

/**
 * Put the database back in the state of a site deployed before the purchase link existed.
 */
function forgetPurchaseUrlMigration(): void
{
    Schema::table('products', fn ($table) => $table->dropColumn('purchase_url'));
    DB::table('migrations')->where('migration', PURCHASE_URL_MIGRATION)->delete();
}

test('a pending migration runs on the first request when enabled', function () {
    config(['store.auto_migrate' => true]);
    forgetPurchaseUrlMigration();

    $this->get(route('home'))->assertOk();

    expect(Schema::hasColumn('products', 'purchase_url'))->toBeTrue()
        ->and(is_file($this->migrateOnFirstRequest->markerPath()))->toBeTrue();
});

test('nothing is migrated when the schema is already current', function () {
    config(['store.auto_migrate' => true]);

    expect($this->migrateOnFirstRequest->migrateIfNeeded())->toBeFalse()
        ->and(is_file($this->migrateOnFirstRequest->markerPath()))->toBeTrue();
});

test('requests leave the schema alone when automatic migration is off', function () {
    config(['store.auto_migrate' => false]);
    forgetPurchaseUrlMigration();

    $this->get(route('home'));

    expect(Schema::hasColumn('products', 'purchase_url'))->toBeFalse();
});
