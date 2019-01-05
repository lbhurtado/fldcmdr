<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->schemalessAttributes('extra_attributes');
            $table->nestedSet();
            $table->timestamps();
        });

        Schema::create('model_has_areas', function (Blueprint $table) {
            $table->unsignedInteger('area_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['area_id', 'model_type', 'model_id']);
            $table->index(['model_id', 'model_type']);
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_areas');
        Schema::dropIfExists('areas');
    }
}
