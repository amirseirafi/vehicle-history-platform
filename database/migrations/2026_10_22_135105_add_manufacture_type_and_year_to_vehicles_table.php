<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('manufacture_type', 20)
                ->nullable()
                ->after('body_style');

            $table->unsignedSmallInteger('manufacture_year')
                ->nullable()
                ->after('manufacture_type');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'manufacture_type',
                'manufacture_year',
            ]);
        });
    }
};