<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {

            $table->string('id')->primary();

            $table->string('queue_number')->unique();

            $table->string('mechanic_id');
            $table->foreign('mechanic_id')->references('uid')->on('users')->onDelete('cascade');

            $table->enum('status', [
                'waiting',
                'process',
                'done',
                'cancel',
            ])->default('waiting');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
