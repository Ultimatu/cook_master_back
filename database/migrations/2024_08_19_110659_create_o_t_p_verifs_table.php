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
        Schema::create('o_t_p_verifs', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->string('otp');
            $table->timestamp('expires_at');
            $table->boolean('is_verified')->default(false);
            $table->string('type')->default('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_t_p_verifs');
    }
};
