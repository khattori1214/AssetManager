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
        if (!Schema::hasTable('loan_categories')) {
            Schema::create('loan_categories', function (Blueprint $table) {
                $table->increments('category_id');
                $table->string('category_name', 50)->unique();
                $table->timestamps();
                $table->unsignedInteger('max_loan_days');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_categories');
    }
};
