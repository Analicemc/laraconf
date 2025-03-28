<?php

namespace Database\Factories;

use App\Enums\Region;
use App\Enums\ConferenceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Conference;
use App\Models\Venue;

class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Conference::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = now()->addMonths(random_int(1, 6));
        $endDate = $startDate->copy()->addDays(3);
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'website' => fake()->url(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(ConferenceStatus::class),
            'region' => $this->faker->randomElement(Region::class),
            'venue_id' => Venue::factory(),
        ];
    }
}
