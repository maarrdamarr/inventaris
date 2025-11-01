<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('damage_report_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('comment')->nullable();
            $table->string('status_change')->nullable(); // optional note of status update
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_report_comments');
    }
};

