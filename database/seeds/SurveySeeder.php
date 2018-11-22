<?php

use Illuminate\Database\Seeder;
use App\{Category, Question, Answer};

class SurveySeeder extends Seeder
{
 public function run()
    {
        DB::table('answers')->delete();
        DB::table('questions')->delete();
        DB::table('categories')->delete();
        $survey = $this->getData();

        $survey->each(function ($category) {
        	$createdCategory = Category::create([
        		'title' => $category['category'],
        	]);
        	collect($category['questions'])->each(function ($question) use ($createdCategory) {
        		$createdQuestion = Question::create([
        			'category_id' => $createdCategory->id,
        			'question' => $question['question'],
        			'type' => $question['type'],
        			'options' => $question['options'] ?? [],
        		]);
        	});
        });
    }

    private function getData()
    {
        return collect([
        	[
        		'category' => 'Demographics',
        		'questions' => [
					[
		                'question' => 'Gender',
		                'type' => 'radio',
		                'options' => [
		                    'Male',
		                    'Female'
		                ],
		            ],
		            [
		                'question' => 'Age Group',
		                'type' => 'radio',
		                'options' => [
		                    '18 to 30',
		                    '31 to 40',
		                    '41 to 50',
		                    '51 and above',
		                ],
		            ],
		            [
		                'question' => 'District',
		                'type' => 'radio',
		                'options' => [
		                    'Intramuros',
		                    'Tondo',
		                    'Paco',
		                    'Sampaloc',
		                    'Sta. Ana',
		                    'San Nicolas',
		                    'Santa Cruz',
		                    'Binondo',
		                    'Port Area',
		                    'Malate',
		                    'Ermita',
		                    'San Miguel',
		                    'Pandacan',
		                    'San Andres',
		                    'Santa Mesa',
		                ],
		            ],
        		],
        	],
        	[
        		'category' => 'Popular',
        		'questions' => [
		            [
		                'question' => 'Who will you vote for in the 2019 elections?',
		                'type' => 'radio',
		                'options' => [
		                    'Erap Estrada',
		                    'Isko Moreno',
		                    'Lito Atienza',
		                    'Alfredo Lim',
		                ],
		            ],
		            [
		                'question' => 'What is the most important issue?',
		                'type' => 'radio',
		                'options' => [
		                    'Crime',
		                    'Corruption',
		                    'Environment',
		                ],
		            ],
		            [
		                'question' => 'What is your problem?',
		                'type' => 'radio',
		                'options' => [
		                    'Health',
		                    'Labor',
		                    'Education',
		                ],
		            ],

        		],
        	],
			[
        		'category' => 'D-Day Morning',
        		'questions' => [
		            [
		                'question' => 'Is the precinct open?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
		            [
		                'question' => 'Is the BEI composition valid?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
		            [
		                'question' => 'Is the ballot box sealed?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
		            [
		                'question' => 'Is there a zero-votes print-out?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
		            [
		                'question' => 'Have you voted?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
        		],
        	],
			[
        		'category' => 'D-Day Afternoon',
        		'questions' => [
		            [
		                'question' => 'Is the precinct closed?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
		            [
		                'question' => 'Is there a print-out of the election return (ER)?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
		            [
		                'question' => 'Is there a PCOS transmission receipt?',
		                'type' => 'radio',
		                'options' => [
		                    'Yes',
		                    'No',
		                ],
		            ],
		            [
		                'question' => 'How many votes for Erap Estrada?',
		                'type' => 'string',
		            ],
		            [
		                'question' => 'How many votes for Lito Atienza?',
		                'type' => 'string',
		            ],
		            [
		                'question' => 'How many votes for Alfredo Lim?',
		                'type' => 'string',
		            ],
		            [
		                'question' => 'How many votes for Isko Moreno?',
		                'type' => 'string',
		            ],
        		],
        	],
        ]);
    }
}
