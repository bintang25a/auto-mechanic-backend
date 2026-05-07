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
        Schema::create('complaints', function (Blueprint $table) {
            $table->string('complaint_number')->primary();
            $table->string('customer_id');
            $table->foreign('customer_id')->references('uid')->on('users')->onDelete('cascade');
            $table->string('queue_id');
            $table->foreign('queue_id')->references('id')->on('queues')->onDelete('cascade');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
