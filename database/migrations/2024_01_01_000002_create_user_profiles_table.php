<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('company_name')->nullable();
            $table->enum('company_size', ['1', '2-9', '10-100', '100+'])->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamp('updated_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};