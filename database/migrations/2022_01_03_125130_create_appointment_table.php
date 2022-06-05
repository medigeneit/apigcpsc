<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('doctor_id');
            $table->integer('schedule_id');
            $table->integer('serial');
            $table->tinyInteger('type');
            $table->json('questions')->nullable();
            $table->float('payable')->nullable();
            $table->unsignedBigInteger('requested_mentor_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment');
    }
}
