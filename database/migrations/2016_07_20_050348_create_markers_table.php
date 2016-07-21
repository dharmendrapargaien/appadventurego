<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markers', function (Blueprint $table) {

            $table->increments('id');
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('marker_type_id')->unsigned();
            $table->foreign('marker_type_id')->references('id')->on('marker_types');

            $table->string('name', 100);
            $table->string('description', 200);
            $table->decimal('lat', 12 ,8);
            $table->decimal('long', 12 ,8);
            
            $table->integer('marker_points')->default(0);
            $table->integer('marker_stars')->default(0);

            $table->date('marker_date')->nullable();
            $table->time('marker_time')->nullable();

            $table->boolean('status')->default(1);

            $table->softDeletes();
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
        Schema::drop('markers');
    }
}
