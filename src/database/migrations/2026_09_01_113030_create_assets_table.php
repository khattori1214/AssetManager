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
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->increments('asset_id');
                $table->string('asset_name', 100);
                $table->unsignedInteger('category_id')->nullable();
                $table->foreign('category_id')
                    ->references('category_id')
                    ->on('loan_categories');
                $table->enum('asset_type', ['loan', 'consumable']);
                $table->unsignedInteger('stock')->nullable();
                $table->unsignedInteger('min_stock')->nullable();
                $table->string('unit', 20);
                $table->unsignedInteger('max_request_quantity')->nullable();
                $table->unsignedInteger('monthly_request_limit')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
