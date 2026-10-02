<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(3, true));

        return [
            'slug' => Str::slug($name),
            'code' => 'AS-'.fake()->unique()->numberBetween(500, 999),
            'name' => $name,
            'category' => fake()->randomElement(['Dış giyim', 'Alt giyim', 'Ekipman', 'Aksesuar']),
            'tagline' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'illustration' => null,
            'images' => [['path' => 'products/'.Str::random(12).'.jpg', 'cutout' => false]],
            'colors' => [
                ['name' => 'Zeytin', 'hex' => '#5f6744', 'image' => null, 'cutout' => false],
                ['name' => 'Coyote', 'hex' => '#a08259', 'image' => null, 'cutout' => false],
            ],
            'sizes' => ['S', 'M', 'L'],
            'features' => [fake()->sentence(), fake()->sentence()],
            'specs' => [['label' => 'Kumaş', 'value' => '%100 pamuk']],
            'is_published' => true,
            'sort_order' => 100,
        ];
    }

    /**
     * Shown with one of the built-in SVG drawings instead of a photo.
     */
    public function illustrated(string $illustration = 'jacket'): static
    {
        return $this->state(fn (array $attributes) => [
            'illustration' => $illustration,
            'images' => [],
        ]);
    }

    /**
     * Saved in the admin but hidden from the site.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
