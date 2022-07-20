<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingRatiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_ratios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fq_id');
            $table->unsignedSmallInteger('question_key');
            $table->unsignedSmallInteger('1')->default(0);
            $table->unsignedSmallInteger('2')->default(0);
            $table->unsignedSmallInteger('3')->default(0);
            $table->unsignedSmallInteger('4')->default(0);
            $table->unsignedSmallInteger('5')->default(0);
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
        Schema::dropIfExists('rating_ratios');
    }
}
