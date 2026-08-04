<?php

use App\Enums\InvoiceStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('course_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->uuid('event_id')->nullable();
            $table->string('invoice_id')->unique();
            $table->integer('fee_amount');
            $table->integer('amount');
            $table->string('invoice_url');
            $table->timestamp('expiry_date');
            $table->integer('paid_amount');
            $table->string('payment_channel');
            $table->string('payment_method');
            $table->enum('invoice_status', [InvoiceStatusEnum::PENDING->value, InvoiceStatusEnum::PAID->value, InvoiceStatusEnum::FAILED->value, InvoiceStatusEnum::EXPIRED->value, InvoiceStatusEnum::UNPAID->value])->default(InvoiceStatusEnum::PENDING->value);
            $table->foreignUuid('course_voucher_id')->nullable()->constrained()->restrictOnDelete()->restrictOnDelete()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
