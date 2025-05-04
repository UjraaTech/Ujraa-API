<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('issuer');
            $table->date('issue_date');
            $table->date('expire_date')->nullable();
            $table->text('description')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certifications');
    }
};