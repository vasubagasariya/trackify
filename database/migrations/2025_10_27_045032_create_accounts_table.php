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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');     // bob, uco, rnsb, cash
            $table->string('type');     // bank, cash
            $table->decimal('opening_balance',8,2)->default(0);
            $table->decimal('current_balance',8,2)->default(0);
            $table->decimal('expence',8,2)->default(0);
            $table->date('opening_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
