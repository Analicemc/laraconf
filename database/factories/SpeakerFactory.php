<?php

namespace Database\Factories;

use App\Models\Talk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Speaker;

class SpeakerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Speaker::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $qtdeQualifications = $this->faker->numberBetween(0, 10);
        $randomQualifications = $this->faker->randomElements(array_keys     (Speaker::QUALIFICATIONS), $qtdeQualifications);

        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'bio' => fake()->text(),
            'qualifications' => $randomQualifications,
            'twitter_handle' => fake()->word(),
        ];
    }

    public function withTalks(int $count = 1)
    {
        return $this->has(Talk::factory()->count($count), 'talks');
    }
}
