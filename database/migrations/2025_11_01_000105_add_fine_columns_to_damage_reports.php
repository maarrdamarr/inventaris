<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('fine_amount')->nullable()->after('evidence_path');
            $table->string('fine_status')->default('unpaid')->after('fine_amount'); // unpaid, submitted, approved
            $table->string('fine_proof_path')->nullable()->after('fine_status');
            $table->timestamp('fine_paid_at')->nullable()->after('fine_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->dropColumn(['fine_amount','fine_status','fine_proof_path','fine_paid_at']);
        });
    }
};

