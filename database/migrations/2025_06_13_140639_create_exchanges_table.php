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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("from_currency_id")->nullable();
            $table->foreign("from_currency_id")->references("id")->on("currencies")->onDelete('cascade');

            $table->unsignedBigInteger("to_currency_id")->nullable();
            $table->foreign("to_currency_id")->references("id")->on("currencies")->onDelete('cascade');

            $table->unsignedBigInteger("user_id")->nullable();
            $table->foreign("user_id")->references("id")->on("users")->onDelete('cascade');

            $table->decimal('rate', 15, 6);
            $table->timestamps();

            $table->unique(['from_currency_id', 'to_currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
