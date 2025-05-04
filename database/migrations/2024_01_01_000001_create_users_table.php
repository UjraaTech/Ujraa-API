<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->boolean('phone_verified')->default(false);
            $table->string('password');
            $table->enum('role', ['client', 'freelancer'])->default('client');
            $table->enum('language', ['ar', 'en'])->default('ar');
            $table->boolean('is_verified')->default(false);
            $table->boolean('identity_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};