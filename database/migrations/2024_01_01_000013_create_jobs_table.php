<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('budget', 10, 2);
            $table->enum('status', ['draft', 'open', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->integer('proposal_count')->default(0);
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};