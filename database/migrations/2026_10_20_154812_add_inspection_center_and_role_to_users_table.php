<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('inspection_center_id')
                ->nullable()
                ->after('id')
                ->constrained('inspection_centers')
                ->nullOnDelete();

            $table->string('role')
                ->default('inspector')
                ->after('email');

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['inspection_center_id']);
            $table->dropColumn(['inspection_center_id', 'role']);
        });
    }
};