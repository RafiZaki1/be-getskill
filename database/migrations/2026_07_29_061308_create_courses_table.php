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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('sub_category_id')->constrained()->restrictOnUpdate()->restrictOnDelete();
            $table->foreignUuid('user_id')->constrained();
            $table->string('title')->unique();
            $table->string('sub_title');
            $table->string('photo')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->boolean('is_premium');
            $table->integer('price')->default(0)->nullable();
            $table->boolean('is_ready')->default(0);
            $table->timestamps();
            $table->integer('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
