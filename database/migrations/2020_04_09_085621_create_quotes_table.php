<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments("id", true);
            $table->integer('user_id')->unsigned();
            $table->string("from_destination");
            $table->string("from_latlong");
            $table->string("to_destination");
            $table->string("to_latlong");
            $table->date("date");
            $table->string("time");
            $table->string("no_of_people");
            $table->string("distance");
            $table->timestamps();
        });

        Schema::table('quotes', function($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
