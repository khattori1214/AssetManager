<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('csv_files')) {
            Schema::create('csv_files', function (Blueprint $table) {
                $table->increments('csv_file_id');
                $table->string('file_name', 255);
                $table->date('target_period_start');
                $table->unsignedInteger('record_count');
                $table->dateTime('generated_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_files');
    }
};
