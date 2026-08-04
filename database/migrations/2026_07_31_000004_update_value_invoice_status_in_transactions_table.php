<?php

use App\Enums\InvoiceStatusEnum;
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
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'invoice_status')) {
                $table->enum('invoice_status', [InvoiceStatusEnum::PENDING->value, InvoiceStatusEnum::PAID->value, InvoiceStatusEnum::FAILED->value, InvoiceStatusEnum::EXPIRED->value, InvoiceStatusEnum::UNPAID->value, InvoiceStatusEnum::REFUND->value, InvoiceStatusEnum::CANCELED->value])->default(InvoiceStatusEnum::PENDING->value)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'invoice_status')) {
                $table->enum('invoice_status', [InvoiceStatusEnum::PENDING->value, InvoiceStatusEnum::PAID->value, InvoiceStatusEnum::FAILED->value, InvoiceStatusEnum::EXPIRED->value, InvoiceStatusEnum::UNPAID->value])->default(InvoiceStatusEnum::PENDING->value)->change();
            }
        });
    }
};
