<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\User;
use App\Enums\AuthorStatusEnum;
use Illuminate\Support\Facades\Hash;

class AuthorStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AuthorStatusEnum::cases() as $i => $status) {
                $user = User::create([
                    'name' => 'author_' . $status->value . '_' . $i,
                    'email' => 'author' . $status->value . '' . $i . '@gmail.com',
                    'password' => Hash::make('password'),
                ]);
                Author::factory()->create([
                    'user_id' => $user->id,
                    'status' => $status->value,
                ]);
            }
    }
}
