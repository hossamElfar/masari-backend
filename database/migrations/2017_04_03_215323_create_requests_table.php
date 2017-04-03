<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('expert_id')->unsigned();
            $table->foreign('expert_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('timing_id')->unsigned();
            $table->foreign('timing_id')->references('id')->on('timings')->onDelete('cascade');

            $table->boolean('accepted')->default(false);

            $table->boolean('reserved');
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
        //
    }
}
