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
        if (!Schema::hasColumn('users', 'provider_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('provider_id')->nullable();
                $table->string('provider_name')->nullable();
                $table->string('avatar')->nullable();
                $table->string('password')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider_id', 'provider_name', 'avatar']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
