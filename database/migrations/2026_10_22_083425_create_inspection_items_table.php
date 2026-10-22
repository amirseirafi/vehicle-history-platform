<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inspection_report_id')
                ->constrained('inspection_reports')
                ->cascadeOnDelete();

            $table->string('part_key', 100);
            $table->string('part_name', 150);

            $table->string('side', 20)
                ->nullable();

            $table->string('status', 30)
                ->default('not_checked');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique([
                'inspection_report_id',
                'part_key',
            ]);

            $table->index([
                'part_key',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};