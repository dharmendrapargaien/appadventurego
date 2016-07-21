<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marker_types', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('name', 100);
            $table->string('description', 200);
            $table->integer('marker_points')->default(0);
            $table->integer('marker_stars')->default(0);
            $table->boolean('marker_for')->default(1); //1 = other, 0 = admin
            $table->boolean('status')->default(1);
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
        Schema::drop('marker_types');
    }
}
