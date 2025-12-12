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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_account');     // id of acccounts cash or bank account
            $table->foreign('from_account')->references('id')->on('accounts')->onDelete('cascade');
            $table->unsignedBigInteger('to_account');
            $table->foreign('to_account')->references('id')->on('accounts')->onDelete('cascade');
            $table->decimal('amount',8,2)->default(0);
            $table->string('description');      // rnsb to bob , bob to cash
            $table->date('transfer_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
