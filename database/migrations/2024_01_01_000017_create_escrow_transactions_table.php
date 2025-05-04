<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->boolean('is_first_collaboration')->default(false);
            $table->enum('status', ['held', 'released', 'refunded', 'disputed'])->default('held');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('escrow_transactions');
    }
};