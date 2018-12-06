<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuickRanksView extends Migration
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
DROP VIEW IF EXISTS quick_ranks;
SQL;
    }

    private function createView(): string
    {
        return <<<SQL
CREATE VIEW quick_ranks AS 
select c.title as category, q.question, a.answer, u.name as interviewer, i.mobile as respondent from answers a
inner join questions q on q.id = a.question_id
inner join categories c on c.id = q.category_id
inner join users u on u.id = a.user_id
inner join invitees i on i.id = a.askable_id
where
    c.title='Quick Rank'
order by
    c.id, a.id
SQL;
    }

    protected function f()
    {

    }
}

