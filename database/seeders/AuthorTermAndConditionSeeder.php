<?php

namespace Database\Seeders;

use App\Models\AuthorTermAndCondition;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorTermAndConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AuthorTermAndCondition::create([
            'id' => Uuid::uuid(),
            'term_and_condition' => 'Term and Condition',
        ]);
    }
}
