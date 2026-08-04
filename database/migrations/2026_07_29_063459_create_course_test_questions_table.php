<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('course_test_id')->constrained();
            $table->foreignUuid('module_id');
            $table->integer('question_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_test_questions');
    }
};
