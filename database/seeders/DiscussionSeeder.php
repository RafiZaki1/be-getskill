<?php

namespace Database\Seeders;

use App\Enums\AnswerEnum;
use App\Models\Discussion;
use App\Models\DiscussionTag;
use App\Models\Module;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscussionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $module = Module::query()->first();
        $user = User::query()->where('email', 'admin@gmail.com')->first();
        foreach (AnswerEnum::cases() as $index => $option) {
            $tag = Tag::create([
                'id' => $index + 1,
                'name' => $option->value
            ]);
        }
        $discussion = Discussion::create([
            'id' => 1,
            'course_id' => $module->course_id,
            'discussion_title' => 'lorem ipsum dolor sit amet',
            'discussion_question' => 'magna rebum?',
            'user_id' => $user->id,
            'module_id' => $module->id
        ]);
        $tags = Tag::query()->get();
        foreach ($tags as $index => $tag) {
            DiscussionTag::create([
                'id' => $index + 1,
                'tag_id' => $tag->id,
                'discussion_id' => $discussion->id
            ]);
        }
    }
}
