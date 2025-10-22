<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'thumbnail' => $this->faker->imageUrl(800, 600, 'posts'),
            'content' => $this->faker->paragraphs(3, true),
            'category' => $this->faker->randomElement(['Technology', 'Lifestyle', 'Education', 'News']),
            'status' => $this->faker->boolean(90),
        ];
    }
}
