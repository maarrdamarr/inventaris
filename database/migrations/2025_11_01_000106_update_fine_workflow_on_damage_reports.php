<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('fine_paid_at'); // qris,tunai,transfer_bca,dll
            $table->string('payment_type')->nullable()->after('payment_method'); // cash|installment
            $table->unsignedInteger('installment_total')->nullable()->after('payment_type');
            $table->unsignedInteger('installment_paid')->default(0)->after('installment_total');
            $table->timestamp('cs_checked_at')->nullable()->after('installment_paid');
            $table->timestamp('admin_approved_at')->nullable()->after('cs_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->dropColumn(['payment_method','payment_type','installment_total','installment_paid','cs_checked_at','admin_approved_at']);
        });
    }
};

