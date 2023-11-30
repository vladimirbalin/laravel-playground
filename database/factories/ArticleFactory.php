<?php

namespace Database\Factories;

use App\Models\Lecture\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    public function definition()
    {
        $categoryIds = Category::pluck('id');

        return [
            'title' => fake()->word(),
            'category_id' => fake()->randomElement($categoryIds),
            'user_id' => fake()->randomElement(User::pluck('id')),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
