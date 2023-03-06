<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => '',
            'name' => '',
            'bio' => '',
            'type_id' => '',
            'breed_id' => '',
            'color_id' => '',
            'age_year' => '',
            'age_month' => '',
            'gender' => '',
            'playfullness' => '',
            'active_level' => '',
            'friendliness' => ''
        ];
    }
}
