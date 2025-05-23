<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->string('value');
            $table->timestamp('updated_at');
            $table->unique(['user_id', 'key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
};