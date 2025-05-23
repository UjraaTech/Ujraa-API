<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['client', 'freelancer']);
            $table->timestamp('assigned_at');
            $table->unique(['user_id', 'role']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
};