<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyingUsersCoulmns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
            $table->string('country')->default("")->nullable()->change();
            $table->string('city')->default("")->nullable()->change();
            $table->string('age')->default("")->nullable()->change();
            $table->string('gender')->default("")->nullable()->change();
            $table->string('pp')->default("")->nullable()->change();
            
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
