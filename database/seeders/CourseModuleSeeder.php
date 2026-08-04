<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\ModuleQuestion;
use App\Models\SubModule;
use App\Models\ModuleTask;

class CourseModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat user seeder
        $user = User::firstOrCreate(
            ['email' => 'seeder_user@example.com'],
            [
                'name' => 'Seeder User',
                'gender' => 'perempuan', // SESUAI GENDER ENUM
                'password' => Hash::make('password'),
            ]
        );

        $userId = $user->id;

        // 2. Buat 5 course
        $courses = Course::factory()
            ->count(5)
            ->create([
                'user_id' => $userId,
            ]);

        foreach ($courses as $course) {

            // 10 module
            $modules = Module::factory()
                ->count(10)
                ->create([
                    'course_id' => $course->id
                ]);

            foreach ($modules as $module) {

                // 1 quiz
                Quiz::factory()->create([
                    'module_id' => $module->id
                ]);

                // 10 question
                ModuleQuestion::factory()
                    ->count(10)
                    ->create([
                        'module_id' => $module->id
                    ]);

                // 5 submodule
                SubModule::factory()
                    ->count(5)
                    ->create([
                        'module_id' => $module->id
                    ]);

                // 3 tasks
                ModuleTask::factory()
                    ->count(3)
                    ->create([
                        'module_id' => $module->id
                    ]);
            }
        }
    }
}
