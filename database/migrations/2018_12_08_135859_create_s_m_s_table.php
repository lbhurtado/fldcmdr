<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSMSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_m_s_s', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from');
            $table->string('to');
            $table->string('message')->nullable();
            // $table->string('from_number');
            // $table->string('to_number');
            // $table->string('message_type');
            // $table->string('direction');
            // $table->string('content')->nullable();
            // $table->boolean('simulated');
            // $table->timestamp('time_created')->nullable();
            // $table->timestamp('time_sent')->nullable();
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
        Schema::dropIfExists('s_m_s_s');
    }
}
