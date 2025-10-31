<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TemplateKit>
 */
class TemplateKitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Business', 'E-commerce', 'Portfolio', 'Blog', 'Landing Page', 'Agency'];

        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement($categories),
            'author' => fake()->name(),
            'version' => fake()->randomElement(['1.0.0', '1.1.0', '2.0.0', '2.1.0']),
            'thumbnail' => fake()->imageUrl(640, 480, 'business', true),
            'tags' => fake()->words(4),
            'files' => ['index.html', 'styles.css', 'script.js'],
            'price' => fake()->randomFloat(2, 9.99, 99.99),
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }
}
