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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('user_id');
                $table->string('employee_no', 32);
                $table->string('user_name', 32);
                $table->string('email', 255)->unique();
                $table->string('password', 255);
                $table->unsignedInteger('role_id');
                $table->foreign('role_id')
                    ->references('role_id')
                    ->on('roles');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
