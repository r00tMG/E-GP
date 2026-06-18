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
        Schema::table('annonces', function (Blueprint $table) {
            $table->text('instructions')->nullable();
            $table->text('nutrition')->nullable();
            $table->text('video')->nullable();
            $table->text('prepTime')->nullable();
            $table->text('cookTime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('annonces', function (Blueprint $table) {
            $table->dropColumn('instructions');
            $table->dropColumn('nutrition');
            $table->dropColumn('video');
            $table->dropColumn('prepTime');
            $table->dropColumn('cookTime');
        });
    }
};
