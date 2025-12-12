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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');      //id of account cash(1), bob(2), rnsb(3), uco(4)
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->decimal('amount',8,2)->default(0);
            $table->string('credit_debit');
            $table->string('category');         // travel, food, home
            $table->string('description');      // detail of expense or earn dosa, brts, recharge
            $table->date('transaction_date');
            $table->decimal('remaining_balance',8,2)->default(0);
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
