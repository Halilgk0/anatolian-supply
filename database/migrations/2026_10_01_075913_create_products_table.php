<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('category', 40);
            $table->string('tagline', 160);
            $table->text('description');
            $table->string('illustration', 20)->nullable();
            $table->json('images');
            $table->json('colors');
            $table->json('sizes');
            $table->json('features');
            $table->json('specs');
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
