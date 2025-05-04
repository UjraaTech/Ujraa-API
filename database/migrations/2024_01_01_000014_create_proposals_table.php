<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->integer('delivery_days');
            $table->text('cover_letter');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->integer('credits_used');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposals');
    }
};