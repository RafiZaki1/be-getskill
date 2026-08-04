<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class OptionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();
        $course = Course::create([
            'id' => Uuid::uuid(),
            'user_id' => $user->id,
            'sub_category_id' => 1,
            'title' => 'lorem ipsum second',
            'slug' => Str::slug('lorem ipsum second'),
            'sub_title' => 'lorem ipsum dolor sit amet',
            'description' => 'lorem ipsum dolor sit amet lorem rebum magna diam stet',
            'price' => 100000,
            'photo' => 'course/course_thumb04.jpg',
            'is_premium' => true,
            'is_ready' => false
        ]);
    }
}
