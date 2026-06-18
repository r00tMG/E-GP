<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("recipe_id");
            $table->enum("meal_type", ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->date("date");
            $table->foreign("user_id")->references('id')->on("users")->onDelete('cascade');
            $table->foreign("recipe_id")->references('id')->on("annonces")->onDelete('cascade');
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
        Schema::dropIfExists('meals');
    }
};
