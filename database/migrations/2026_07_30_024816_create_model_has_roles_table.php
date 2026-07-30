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
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->uuid('role_uuid');
            $table->string('model_type');
            $table->uuid('model_uuid');

            $table->index(['model_uuid', 'model_type'], 'model_has_roles_model_uuid_model_type_index');

            $table->foreign('role_uuid')
                ->references('uuid')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary(
                ['role_uuid', 'model_uuid', 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_roles');
    }
};
