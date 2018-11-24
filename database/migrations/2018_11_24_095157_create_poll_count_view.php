<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollCountView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->dropView());

        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    private function dropView(): string
    {
        return <<<SQL
DROP VIEW IF EXISTS poll_counts;
SQL;
    }

    private function createView(): string
    {
        return <<<SQL
CREATE VIEW poll_counts AS 
    select q.category_id, q.id as question_id, sum(cast (a.answer as integer)) from answers a
    join questions q on q.id = a.question_id
    join categories c on c.id = q.category_id
    where (c.title = 'D-Day Poll Count')
    group by q.category_id, q.id;
SQL;
    }

    protected function f()
    {

    }
}
