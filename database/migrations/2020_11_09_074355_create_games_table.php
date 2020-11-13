<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('playerName')->default('Unnamed player');
            $table->integer('from');
            $table->integer('to');
            $table->integer('attempts');
            $table->integer('done_attempts')->default(0);
            $table->string('status')->nullable();
            $table->json('numbers')->nullable();
            $table->integer('correct_number')->nullable();
            $table->decimal('coefficient')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
