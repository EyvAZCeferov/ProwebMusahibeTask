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
        Schema::create('atm_banknotes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("currency_id")->nullable();
            $table->foreign("currency_id")->references("id")->on("currencies")->onDelete('cascade');

            $table->integer("name")->default(100);
            $table->integer("quantity")->default(0);

            $table->boolean("status")->default(true);

            $table->unsignedBigInteger("user_id")->nullable();
            $table->foreign("user_id")->references("id")->on("users")->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atm_banknotes');
    }
};
