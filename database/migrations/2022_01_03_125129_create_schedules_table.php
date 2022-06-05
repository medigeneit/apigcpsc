<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chamber_id');
            $table->date('date');
            $table->string('time_schedule')->comment("Json encoding decoding");
            $table->string('slot_threshold')->nullable()->comment("Json encoding decoding Max slot, min threshold / mentor type");
            $table->string('mentors')->nullable()->comment("Json encoding decoding multiple mentors / mentor_type");
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('schedules');
    }
}
