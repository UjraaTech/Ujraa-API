<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'escalated', 'resolved', 'closed'])->default('open');
            $table->timestamp('last_activity_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
};