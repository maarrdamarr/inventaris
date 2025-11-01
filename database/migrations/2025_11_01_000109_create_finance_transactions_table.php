<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // fine, purchase, other
            $table->enum('direction', ['in','out']);
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('reference_id')->nullable(); // e.g. damage_report_id
            $table->string('reference_type')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};

