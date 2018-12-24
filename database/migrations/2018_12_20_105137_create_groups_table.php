<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->schemalessAttributes('extra_attributes');
            $table->nestedSet();
            $table->timestamps();
        });

        Schema::create('model_has_groups', function (Blueprint $table) {
            $table->unsignedInteger('group_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['group_id', 'model_type', 'model_id']);
            $table->index(['model_id', 'model_type']);
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_groups');
        Schema::dropIfExists('groups');
    }
}
