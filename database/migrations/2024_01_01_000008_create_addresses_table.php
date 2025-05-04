<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('country');
            $table->string('city');
            $table->string('street');
            $table->string('postal_code')->nullable();
            $table->enum('type', ['billing', 'shipping', 'other']);
            $table->timestamp('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};