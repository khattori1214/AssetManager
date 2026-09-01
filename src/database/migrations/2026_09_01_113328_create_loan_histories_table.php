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
        if (!Schema::hasTable('loan_histories')) {
            Schema::create('loan_histories', function (Blueprint $table) {
                $table->increments('loan_history_id');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')
                    ->references('user_id')
                    ->on('users');
                $table->unsignedInteger('asset_id');
                $table->foreign('asset_id')
                    ->references('asset_id')
                    ->on('assets');
                $table->dateTime('loan_date');
                $table->date('due_date');
                $table->dateTime('return_date')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_histories');
    }
};
