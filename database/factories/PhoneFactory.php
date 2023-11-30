<?php

namespace Database\Factories;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PhoneFactory extends Factory
{
    protected $model = Phone::class;

    public function definition(): array
    {
        $usersIds = User::pluck('id');

        return [
            'user_id' => $this->faker->randomElement($usersIds),
            'number' => $this->faker->phoneNumber,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
