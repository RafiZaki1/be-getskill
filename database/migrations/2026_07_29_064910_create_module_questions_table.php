<?php

use App\Enums\AnswerEnum;
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
        Schema::create('module_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('module_id')->constrained();
            $table->longText('question');
            $table->longText('option_a');
            $table->longText('option_b');
            $table->longText('option_c');
            $table->longText('option_d');
            $table->longText('option_e');
            $table->enum('answer', [AnswerEnum::OPTION_A->value, AnswerEnum::OPTION_B->value, AnswerEnum::OPTION_C->value, AnswerEnum::OPTION_D->value, AnswerEnum::OPTION_E->value]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_qustions');
    }
};
