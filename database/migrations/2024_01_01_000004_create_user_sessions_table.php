<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('refresh_token');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->timestamp('created_at');
            $table->timestamp('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_sessions');
    }
};