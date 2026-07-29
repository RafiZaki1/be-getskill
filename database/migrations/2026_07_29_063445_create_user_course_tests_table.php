<?php

use App\Enums\TestEnum;
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
        Schema::create('user_course_tests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid(column: 'course_test_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('module_question_id');
            $table->string('answer')->nullable();
            $table->integer('score')->nullable();
            $table->enum('test_type', array_map(fn($enum) => $enum->value, TestEnum::cases()));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_course_tests');
    }
};
