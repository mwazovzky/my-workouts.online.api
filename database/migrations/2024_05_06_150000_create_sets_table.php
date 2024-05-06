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
        Schema::create('sets', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->integer('weight')->nullable();
            $table->integer('repetitions');
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('action_id');
            $table->timestamps();

            $table->foreign('action_id')->references('id')->on('actions')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
