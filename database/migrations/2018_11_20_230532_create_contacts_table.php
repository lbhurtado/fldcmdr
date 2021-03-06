<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile');
            $table->string('name')->nullable();
            // $table->string('role');
            // $table->integer('upline_id')->unsigned();
            $table->nullableMorphs('upline');
            // $table->text('message')->nullable();
            // $table->string('telerivet_id')->unique()->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->schemalessAttributes('extra_attributes');
            $table->unique(['mobile', 'upline_id']);
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
