<?php

use Illuminate\Database\Seeder;
use App\{User, Invitee, Category, Question, Answer};

class SurveySimulatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	// factory(User::class, 1)->create();
        factory(Invitee::class, 100)->create();

        $users = User::all();
        $invitees = Invitee::all();
        $category = Category::where('title', 'Quick Rank')->first();

        $users->each(function ($user) use ($invitees, $category) {
	        $invitees->each(function ($invitee) use ($user, $category) {
	        	$category->questions->each(function ($question) use ($invitee, $user, $category) {
	        		$answer = $question->answers()->make([
	        			'answer' => array_random($question->values),
	        		]);
	        		$answer->user()->associate($user);
	        		$answer->askable()->associate($invitee);
	        		$answer->save();
	        	});
	        });
        });

        

    }
}
