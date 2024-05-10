<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_tokens', function (Blueprint $table) {
            $table->uuid('id')->default(DB::raw('(uuid_generate_v4())'))->primary();
            $table->uuid('user_id');
            $table->string('token')->nullable(false);
            $table->string('type')->enum('type', ['LOGIN', 'FORGOT_PASSWORD', 'CHANGE_PASSWORD'])->default('LOGIN');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->string('version')->nullable();
            $table->string('os')->nullable();
            $table->timestampTz('expired_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestampTz('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by')->nullable();
            $table->timestampTz('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_tokens');
    }
};
