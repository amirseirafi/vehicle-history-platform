<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('color_id')
                ->nullable()
                ->after('color')
                ->constrained('vehicle_colors')
                ->nullOnDelete();

            $table->string('custom_color', 100)
                ->nullable()
                ->after('color_id');

            $table->index('color_id');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropIndex(['color_id']);
            $table->dropColumn([
                'color_id',
                'custom_color',
            ]);
        });
    }
};