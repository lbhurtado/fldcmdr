<?php

use Illuminate\Database\Seeder;
use App\{Category, Question, Answer};

class QuickRankSeeder extends Seeder
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
        		'type' => $category['type'] ?? 'text',
        		'enabled_at' => $category['enabled_at'] ?? now(),
        		'extra_attributes' => $category['options'] ?? [],
        	]);
        	collect($category['questions'])->each(function ($question) use ($createdCategory) {
        		$createdQuestion = Question::create([
        			'category_id' => $createdCategory->id,
        			'question' => $question['question'],
        			'type' => $question['type'] ?? 'radio',
        			'extra_attributes' => $question['options'] ?? [],
        		]);
        	});
        });
    }

    private function getData()
    {
        return collect([
        	[
        		'category' => 'Quick Rank',
        		'options' => [
        			'twosome' => true,
        			'reward' => env('SURVEY_REWARD', 0),
        		],
        		'questions' => [
					[
		                'question' => 'Gender?',
		                'options' => [
		                	'values' => [
			                    'Male',
			                    'Female'
		                	],
		                ],
		            ],
		            [
		                'question' => 'Educational Attainment?',
		                'options' => [
		                	'values' => [
			                    'Some Elementary',
			                    'Elementary Graduate',
			                    'Some High School',
			                    'High School Graduate',
			                    'Some College',
			                    'College Graduate',
		                	],
		                ],
		            ],
		            [
		                'question' => 'Age Group?',
		                'options' => [
		                	'values' => [
			                    '18 to 30',
			                    '31 to 40',
			                    '41 to 50',
			                    '51 and above',
		                	],
		                ],
		            ],
		            [
		                'question' => 'Monthly income of household?',
		                'options' => [
		                	'values' => [
			                    '150k',
			                    '50k',
			                    '16k',
			                    '5k', 
		                	],
		                ],
		            ],
		            [
		                'question' => 'If elections were held tomorrow, who will you vote for?',
		                'options' => [
		                	'values' => [
			                    'Erap Estrada',
			                    'Isko Moreno',
			                    'Lito Atienza',
			                    'Alfredo Lim',
			                    'Undecided',
		                	],
			                'anchored' => true,
		                ],
		            ],
		            [
		                'question' => 'Why did you choose :anchor?',		                
		                'options' => [
		                	'values' => [
			                    'Honest',
			                    'Track Record',
			                    'Popular',
			                    'Rich',
		                	],
		                ],
		            ],
					[
		                'question' => 'Why not <Erap Estrada>?',
		                'options' => [
		                	'values' => [
			                    'Corrupt',
			                    'Not Effective',
			                    'Controversial',
			                    'No Track Record',
		                	],
		                	'anchor_regex' => '<:anchor>',
            				'anchor_action' => 'skip',
		                ],
		            ],
					[
		                'question' => 'Why not <Isko Moreno>?',
		                'options' => [
		                	'values' => [
			                    'Corrupt',
			                    'Not Effective',
			                    'Controversial',
			                    'No Track Record',
		                	],
		                	'anchor_regex' => '<:anchor>',
		                	'anchor_action' => 'skip',
		                ],
		            ],
					[
		                'question' => 'Why not <Lito Atienza>?',
		                'options' => [
		                	'values' => [
			                    'Corrupt',
			                    'Not Effective',
			                    'Controversial',
			                    'No Track Record',
		                	],
		                	'anchor_regex' => '<:anchor>',
		                	'anchor_action' => 'skip',
		                ],
		            ],
					[
		                'question' => 'Why not <Alfredo Lim>?',
		                'options' => [
		                	'values' => [
			                    'Corrupt',
			                    'Not Effective',
			                    'Controversial',
			                    'No Track Record',
		                	],
		                	'anchor_regex' => '<:anchor>',
		                	'anchor_action' => 'skip',
		                ],
		            ],
		            [
		                'question' => 'What is the most important issue for the coming elections?',
		                'options' => [
		                	'values' => [
			                    'Job Opportunities',
								'Peace and Order',
								'Prices',
								'Corruption',
								'Health Services',
								'Schools and Books', 
								'Roads and Bridges',
		                	],
		                ],
		            ],
        		],
        	],
			[
        		'category' => 'D-Day Morning',
        		'options' => [
        			'reward' => 0,
        		],
        		'questions' => [
		            [
		                'question' => 'Is the precinct open?',
		                'options' => [
		                	'required' => true,
		                	'values' => [
			                    'Yes',
			                    'No',
		                	],
		                ],
		            ],
		            [
		                'question' => 'Is the BEI composition valid?',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                    'Can\'t tell',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is the ballot box sealed?',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                    'Can\'t tell',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is there a zero-votes print-out?',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                    'Can\'t tell',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Have you voted?',
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
        		'options' => [
        			'reward' => 0,
        		],
        		'questions' => [
		            [
		                'question' => 'Is the precinct closed?',
		                'options' => [
		                	'required' => true,
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is there a print-out of the election return (ER)?',
		                'options' => [
		                	'values' => [
			                    'Yes',
			                    'No',
			                ],
		                ],
		            ],
		            [
		                'question' => 'Is there a PCOS transmission receipt?',
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
        			'reward' => 0,
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
