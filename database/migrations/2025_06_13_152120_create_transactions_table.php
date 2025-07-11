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

            $table->string("code",36)->nullable();

            $table->unsignedBigInteger("user_id")->nullable();
            $table->foreign("user_id")->references("id")->on("users")->onDelete('cascade');

            $table->unsignedBigInteger("account_id")->nullable();
            $table->foreign("account_id")->references("id")->on("accounts")->onDelete('cascade');

            $table->decimal("amount", 15, 2)->default(0.0);

            $table->unsignedBigInteger("transaction_status_id")->nullable();
            $table->foreign("transaction_status_id")->references("id")->on("transaction_statuses")->onDelete('cascade');

            $table->text("notes")->nullable();
            $table->jsonb("additional_data")->nullable(); // moreinformation

            $table->timestamps();
            $table->softDeletes();
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
