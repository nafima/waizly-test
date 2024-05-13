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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->default(DB::raw('(uuid_generate_v4())'))->primary();
            $table->uuid('role_id')->nullable(false);
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable(false);
            $table->string('fullname')->nullable(false);
            $table->date('birthdate')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('last_ip')->nullable();
            $table->string('language')->enum('language', ['EN', 'ID'])->default('EN');
            $table->string('status')->enum('status', ['INACTIVE', 'ACTIVE', 'BANNED', 'DELETED'])->default('INACTIVE');
            $table->integer('login_attempt')->default(0);
            $table->timestampTz('last_login')->nullable();
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
        Schema::dropIfExists('users');
    }
};
