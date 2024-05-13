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
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->default(DB::raw('(uuid_generate_v4())'))->primary();
            $table->string('name')->unique();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('roles');
    }
};
