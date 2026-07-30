<?php

namespace Database\Seeders;

use App\Models\Blog;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Blog::create([
            'id' => Uuid::uuid(),
            'title' => 'lorem ipsum',
            'slug' => 'lorem-ipsum',
            'thumbnail' => '',
            'description' => 'lorem ipsum dolor sit amet',
            'blog_sub_category_id' => '1',
        ]);
    }
}
