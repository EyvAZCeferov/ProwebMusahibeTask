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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->uuid('request_id')->unique();

            $table->unsignedBigInteger("user_id")->nullable();
            $table->foreign("user_id")->references("id")->on("users")->onDelete('set null');

            $table->ipAddress('ip_address')->nullable();
            $table->jsonb('user_agent')->nullable();
            $table->string('method', 10); // get, post, put vsvs
            $table->text('url'); // full url
            $table->unsignedSmallInteger('status_code'); // only status code
            $table->unsignedInteger('latency_ms');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
