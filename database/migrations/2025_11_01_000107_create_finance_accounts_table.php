<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_balance')->default(1000000000); // 1 Milyar
            $table->timestamps();
        });

        // seed one row if empty
        DB::table('finance_accounts')->insert(['cash_balance'=>1000000000,'created_at'=>now(),'updated_at'=>now()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_accounts');
    }
};

