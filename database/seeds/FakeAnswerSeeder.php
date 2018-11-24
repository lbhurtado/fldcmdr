<?php

use Illuminate\Database\Seeder;
use App\{User, Category, Question, Answer};

class FakeAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('answers')->delete();
        DB::table('questions')->delete();
        DB::table('categories')->delete();
    	DB::table('users')->delete();

		$node = App\User::create([
			'name' => 'Lester',
		    'mobile' => '09173011987',
		    'email' => 'lester@chicozen.com',
		    'password' => bcrypt('1234'),
		    'children' => [
				[
					'name' => 'Nicolo',
				  	'mobile' => '09178251991',
				  	'email' => 'nicolo@hurtado.ph',
				  	'password' => bcrypt('1234'),
				  	'children' => [
				      [ 
				      		'name' => 'Retsel',
				        	'mobile' => '09189362340', 
				        	'email' => 'lester@applester.co',
				        	'password' => bcrypt('1234'),
				      ],
				  ],
				],
				[
					'name' => 'Apple',
				  	'mobile' => '09088882786',
				  	'email' => 'apple@hurtado.ph',
				  	'password' => bcrypt('1234'),
					 'children' => [
					    [ 
					    	'name' => 'Ruth',
					    	'mobile' => '09175180722', 
					    	'email' => 'raphurtado@me.com',
					    	'password' => bcrypt('1234'),
					    ],
					],
				],
		    ],
		]);

    	$category = tap(Category::make(['title' => 'Poll Count', 'type' => 'numeric']), function ($category) {
    		$category->extra_attributes = [
    			'twosome' => false,
    			'reward' => 0,
    			'pollcount' => true,
    		];
    		$category->save();
    	});

    	$questions = [
    		'Erap #Estrada',
    		'Lito #Atienza',
    		'Alfredo #Lim',
    		'Isko #Moreno',
    	];

    	foreach ($questions as $question) {
	    	$type = "string";
	    	$extra_attributes = ['values' => [0]];
	    	$question = tap(Question::make(compact('question', 'type', 'extra_attributes')), function ($question) use ($category) {
	    		$question->category()->associate($category);
	    		$question->save();
	    	});
    	}

    	$questions = Question::all();
		$node->each(function ($user) use ($questions) {
			$questions->each(function ($question) use ($user) {
		    	$a = tap(Answer::make(['answer' => [rand(1,250)]]), function ($answer) use ($question, $user) {
		    		$answer->weight = array_sum($answer->answer);
		    		$answer->question()->associate($question);
		    		$answer->user()->associate($user);
		    		$answer->askable()->associate($user);
		    		$answer->save();
		    	});
			});
		});
    }
}
