<?php

namespace Database\Seeders;

use App\Enums\AnswerEnum;
use App\Models\Course;
use App\Models\CourseTask;
use App\Models\CourseTest;
use App\Models\Module;
use App\Models\ModuleQuestion;
use App\Models\ModuleTask;
use App\Models\Quiz;
use App\Models\SubmissionTask;
use App\Models\SubModule;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->firstOrFail();
        $course = Course::create([
            'id' => Uuid::uuid(),
            'user_id' => $user->id,
            'sub_category_id' => 1,
            'title' => 'lorem ipsum',
            'slug' => Str::slug('lorem ipsum'),
            'sub_title' => 'lorem ipsum dolor sit amet',
            'description' => 'lorem ipsum dolor sit amet lorem rebum magna diam stet',
            'price' => 100000,
            'photo' => 'course/course_thumb04.jpg',
            'is_premium' => true,
            'is_ready' => false
        ]);

        $course = Course::create([
            'id' => Uuid::uuid(),
            'user_id' => $user->id,
            'sub_category_id' => 1,
            'title' => 'lorem ipsum dolor',
            'slug' => Str::slug('lorem ipsum dolor'),
            'sub_title' => 'lorem ipsum dolor sit amet',
            'description' => 'lorem ipsum dolor sit amet lorem rebum magna diam stet',
            'price' => 100000,
            'photo' => 'course/course_thumb04.jpg',
            'is_premium' => true,
            'is_ready' => false
        ]);
        $course = Course::create([
            'id' => Uuid::uuid(),
            'user_id' => $user->id,
            'sub_category_id' => 1,
            'title' => 'lorem ipsum dolor sit',
            'slug' => Str::slug('lorem ipsum dolor sit'),
            'sub_title' => 'lorem ipsum dolor sit amet',
            'description' => 'lorem ipsum dolor sit amet lorem rebum magna diam stet',
            'price' => 100000,
            'photo' => 'course/course_thumb04.jpg',
            'is_premium' => true,
            'is_ready' => false
        ]);
        for ($a = 1; $a <= 3; $a++) {
            Module::create([
                'id' => Uuid::uuid(),
                'course_id' => $course->id,
                'step' => $a,
                'title' => 'lorem ipsum ' . $a,
                'slug' => Str::slug('lorem ipsum ' . $a),
                'sub_title' => 'lorem ipsum dolor sit amet'
            ]);
        }
        $modules = Module::limit(3)->get();
        $firstModule = Module::firstOrFail();
        foreach ($modules as $index => $module) {
            SubModule::create([
                'id' => Uuid::uuid(),
                'module_id' => $module->id,
                'step' => $index,
                'title' => 'lorem ipsum ' . $index,
                'slug' => Str::slug('lorem ipsum ' . $index),
                'sub_title' => 'lroem ipsum dolor sit amet',
                'content' => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).",
            ]);
        }
        foreach ($modules as $index => $module) {

            foreach (AnswerEnum::cases() as $index => $option) {
                ModuleQuestion::create([
                    'id' => Uuid::uuid(),
                    'module_id' => $module->id,
                    'question' => 'lorem ipsum dolor sit amet jawabannya adalah:' . $option->value,
                    'option_a' => 'lorem ipsum',
                    'option_b' => 'lorem ipsum',
                    'option_c' => 'lorem ipsum',
                    'option_d' => 'lorem ipsum',
                    'option_e' => 'lorem ipsum',
                    'answer' => $option->value,
                ]);
            }

        }


        $quiz = Quiz::create([
            'id' => Uuid::uuid(),
            'module_id' => $firstModule->id,
            'rules' => 'lorem ipsum dolor sit amet',
            'total_question' => 5,
            'retry_delay' => 60,
            'minimum_score' => 60,
            'duration' => 60,
        ]);
        $courseTest = CourseTest::create([
            'id' => Uuid::uuid(),
            'course_id' => $course->id,
            'total_question' => '5',
            'duration' => 60,
        ]);

        $moduleTask = ModuleTask::create([
            'id' => Uuid::uuid(),
            'module_id' => $firstModule->id,
            'point' => 1,
            'description' => 'lorem ipsum dolor sit amet lorem rebum magna diam stet',
            'question' => 'lorem ipsum dolor sit amet'
        ]);
    }
}
