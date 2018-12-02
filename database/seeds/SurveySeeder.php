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
        		'type' => $category['type'],
        		'enabled_at' => $category['enabled_at'],
        		'extra_attributes' => $category['options'] ?? [],
        	]);
        	collect($category['questions'])->each(function ($question) use ($createdCategory) {
        		$createdQuestion = Question::create([
        			'category_id' => $createdCategory->id,
        			'question' => $question['question'],
        			'type' => $question['type'],
        			'extra_attributes' => $question['options'] ?? [],
        			// 'options' => $question['options'] ?? [],
        		]);
        	});
        });
    }

    private function getData()
    {
        return collect([
        	[
        		'category' => 'Demographics',
        		'type' => 'text',
        		'enabled_at' => now(),
        		'options' => [
        			'twosome' => true,
        			'reward' => 25,
        			'pollcount' => false,
        		],
        		'questions' => [
					[
		                'question' => 'Registered Voter?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => true,
		                	'values' => [
			                    'Yes',
			                    'No'
		                	],
		                ],
		            ],
					[
		                'question' => 'Gender?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Male',
			                    'Female'
		                	],
		                ],
		            ],
		            [
		                'question' => 'Social Economic Class?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Class A',
			                    'Class B',
			                    'Class C',
			                    'Class D',
			                    'Class E',
		                	],
		                ],
		            ],
		            [
		                'question' => 'Age Group?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    '18 to 30',
			                    '31 to 40',
			                    '41 to 50',
			                    '51 and above',
		                	],
		                ],
		            ],
		            [
		                'question' => 'District in Manila?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
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
        	],
        	[
        		'category' => 'Popular',
        		'type' => 'text',
        		'enabled_at' => now(),
        		'options' => [
        			'twosome' => false,
        			'reward' => 0,
        			'pollcount' => false,
        		],
        		'questions' => [
		            [
		                'question' => 'Who will you vote for in the 2019 elections?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Erap Estrada',
			                    'Isko Moreno',
			                    'Lito Atienza',
			                    'Alfredo Lim',
		                	],
			                'anchored' => true,
		                ],
		            ],
		            [
		                'question' => 'Why :anchor?',
		                'type' => 'radio',
		                
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Honest',
			                    'Track Record',
			                    'Popular',
			                    'Rich',
		                	],
		                ],
		            ],
					[
		                'question' => 'Why is <Erap Estrada> not your #1?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Corrupt',
			                    'Gay',
			                    'Tamad',
			                    'Killer',
		                	],
		                	'anchor_regex' => '<:anchor>',
            				'anchor_action' => 'skip',
		                ],
		            ],
					[
		                'question' => 'Why is <Isko Moreno> not your #1?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Corrupt',
			                    'Gay',
			                    'Tamad',
			                    'Killer',
		                	],
		                	'anchor_regex' => '<:anchor>',
		                	'anchor_action' => 'skip',
		                ],
		            ],
					[
		                'question' => 'Why is <Lito Atienza> not your #1?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Corrupt',
			                    'Gay',
			                    'Tamad',
			                    'Killer',
		                	],
		                	'anchor_regex' => '<:anchor>',
		                	'anchor_action' => 'skip',
		                ],
		            ],
					[
		                'question' => 'Why is <Alfredo Lim> not your #1?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Corrupt',
			                    'Gay',
			                    'Tamad',
			                    'Killer',
		                	],
		                	'anchor_regex' => '<:anchor>',
		                	'anchor_action' => 'skip',
		                ],
		            ],
		            [
		                'question' => 'What is the most important issue?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Crime',
			                    'Corruption',
			                    'Environment',
		                	],
		                ],
		            ],
		            [
		                'question' => 'What is your problem?',
		                'type' => 'radio',
		                'options' => [
		                	'required' => false,
		                	'values' => [
			                    'Health',
			                    'Labor',
			                    'Education',
		                	],
		                ],
		            ],
        		],
        	],
			[
        		'category' => 'D-Day Morning',
        		'type' => 'text',
        		'enabled_at' => now(),
        		'options' => [
        			'twosome' => false,
        			'reward' => 0,
        			'pollcount' => false,
        		],
        		'questions' => [
		            [
		                'question' => 'Is the precinct open?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
		                	],
		                ],
		            ],
		            [
		                'question' => 'Is the BEI composition valid?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is the ballot box sealed?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is there a zero-votes print-out?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Have you voted?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
        		],
        	],
			[
        		'category' => 'D-Day Afternoon',
        		'type' => 'text',
        		'enabled_at' => now(),
        		'options' => [
        			'twosome' => false,
        			'reward' => 0,
        			'pollcount' => false,
        		],
        		'questions' => [
		            [
		                'question' => 'Is the precinct closed?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is there a print-out of the election return (ER)?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is there a PCOS transmission receipt?',
		                'type' => 'radio',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		        ],
		    ],
		    [
        		'category' => 'D-Day Poll Count',
        		'type' => 'numeric',
        		'enabled_at' => now(),
        		'options' => [
        			'twosome' => false,
        			'reward' => 0,
        			'pollcount' => true,
        		],
        		'questions' => [
		            [
		                'question' => 'How many votes for Erap #Estrada?',
		                'type' => 'string',
		                'options' => [
		                	'values' => [0],
		                ],

		            ],
		            [
		                'question' => 'How many votes for Lito #Atienza?',
		                'type' => 'string',
		                'options' => [
		                	'values' => [0],
		                ],
		            ],
		            [
		                'question' => 'How many votes for Alfredo #Lim?',
		                'type' => 'string',
		                'options' => [
		                	'values' => [0],
		                ],
		            ],
		            [
		                'question' => 'How many votes for Isko #Moreno?',
		                'type' => 'string',
		                'options' => [
		                	'values' => [0],
		                ],
		            ],
        		],
        	],
        ]);
    }
}
