<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\Category;
use App\{User, Invitee};
use App\Question as SurveyQuestion;
use App\Eloquent\{Phone, Messenger};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class SurveyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/survey';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');

        $survey = $this->getData();

        $survey->each(function ($category) {
            $createdCategory = Category::create([
                'title' => $category['category'],
                'type' => $category['type'],
                'enabled_at' => $category['enabled_at'],
                'extra_attributes' => $category['options'] ?? [],
            ]);
            collect($category['questions'])->each(function ($question) use ($createdCategory) {
                $createdQuestion = SurveyQuestion::create([
                    'category_id' => $createdCategory->id,
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'extra_attributes' => $question['options'] ?? [],
                    // 'options' => $question['options'] ?? [],
                ]);
            });
        });

        // $this->input = collect([
        //     'category' => Category::where('title', 'Demographics')->first(),
        //     'coordinates' => [
        //         'lat' => $this->faker->latitude,
        //         'lon' => $this->faker->longitude,
        //     ],
        //     'mobiles' => [
        //         Phone::number('09178251991'),
        //         Phone::number('09189362340'),
        //     ],
        // ]);
    }

    /** @test */
    public function survey_success_run()
    {
        \Queue::fake();

        $input = $this->getInput('Demographics');

        foreach ($input->get('mobiles') as $mobile) {
            $this->bot
                ->setUser(['id' => $channel_id = $this->faker->randomNumber(8)])
                ->setDriver(TelegramDriver::class)
                ->receives($this->keyword)
                ;

            $user_id = tap(Messenger::hook($this->botman)->getUser(), function ($user) use ($channel_id) {
                $this->assertEquals($user->channel_id, $channel_id);
            })->id;
            
            $this->bot
                ->assertReply(trans('survey.intro'))
                ->assertQuestion(trans('survey.input.category'))
                ->receives($input->get('category')->id)
                ;

            if (config('chatbot.survey.location'))
                $this->bot
                    ->assertReply(trans('survey.input.location'))
                    ->receivesLocation($input->get('coordinates')['lat'], $input->get('coordinates')['lon'])
                    ;

            $this->bot
                ->assertReply(trans('survey.info', [
                    'category' => $input->get('category')->title,
                    'count' => $input->get('category')->questions->count()
                ]))
                ;

            if ($input->get('category')->twosome) {
                $this->bot
                    ->assertQuestion(trans('survey.input.mobile'))
                    ->receives($mobile)
                    ;

                $this->assertDatabaseHas('invitees', compact('mobile'));                
            }

            \Queue::assertPushed(\App\Jobs\SendUserInvitation::class); 

            $askable      = Invitee::withMobile($mobile)->first();
            $askable_id   = $askable->id;
            $askable_type = get_class($askable);

            $count = $input->get('category')->questions->count();
            $qanda = '';
            $input->get('category')->questions->each(function ($question, $key) use ($count, &$qanda) {
                $answer = $question->required 
                            ? $question->values[$affirmative_index = 0] 
                            : array_random($question->values)
                            ;
                $this->bot
                    ->assertQuestion(trans('survey.question', [
                        'current'   => $key+1,
                        'count'     => $count,
                        'text'      => $question->question,
                    ]))
                    ->receivesInteractiveMessage($answer)
                    ->assertReply(trans('survey.answer', compact('answer')))
                    ;

                $qanda .= $question->question . " " . $answer . "\n";
                $this->assertDatabaseHas('answers', compact('user_id', 'answer', 'askable_id', 'askable_type'));
            });
                        
            $this->bot
                ->assertReply(trans('survey.finished'))
                ->assertReply(trans('survey.result', compact('qanda')))
                ;                    

            \Queue::assertPushed(\App\Jobs\SendUserInvitation::class);
            \Queue::assertPushed(\App\Jobs\SendAskableReward::class); 
        }
    }

    /** @test */
    public function survey_required_question_run()
    {                
        \Queue::fake();

        $input = $this->getInput('Demographics');
        config(['chatbot.survey.location' => false]);

        $this->bot
            ->setUser(['id' => $channel_id = $this->faker->randomNumber(8)])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('survey.intro'))
            ->assertQuestion(trans('survey.input.category'))
            ->receives($input->get('category')->id)
            ->assertReply(trans('survey.info', [
                'category' => $input->get('category')->title,
                'count' => $input->get('category')->questions->count()
            ]))
            ->assertQuestion(trans('survey.input.mobile'))
            ->receives($input->get('mobiles')[0])
            ->assertTemplate(Question::class)
            ->receives('No')
            ->assertReply(trans('survey.abort'))
            ;

        \Queue::assertPushed(\App\Jobs\SendUserInvitation::class);
        \Queue::assertNotPushed(\App\Jobs\SendAskableReward::class);
    }

    /** @test */
    public function survey_pollcount()
    {
        \Queue::fake();

        $input = $this->getInput('D-Day Poll Count');
        config(['chatbot.survey.location' => false]);

        $this->bot
            ->setUser(['id' => $channel_id = $this->faker->randomNumber(8)])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('survey.intro'))
            ->assertQuestion(trans('survey.input.category'))
            ->receives($input->get('category')->id)
            ->assertReply(trans('survey.info', [
                'category' => $input->get('category')->title,
                'count' => $input->get('category')->questions->count()
            ]))
            ;

            $count = $input->get('category')->questions->count();
            $qanda = '';
            $input->get('category')->questions->each(function ($question, $key) use ($count, &$qanda) {
                $answer = rand(10,100);
                $this->bot
                    ->assertQuestion(trans('survey.question', [
                        'current'   => $key+1,
                        'count'     => $count,
                        'text'      => $question->question,
                    ]))
                    ->receivesInteractiveMessage($answer)
                    ->assertReply(trans('survey.answer', compact('answer')))
                    ;

                $qanda .= $question->question . " " . $answer . "\n";
                $this->assertDatabaseHas('answers', compact('answer', 'askable_id', 'askable_type'));
            });

            $this->bot
                ->assertReply(trans('survey.finished'))
                ->assertReply(trans('survey.result', compact('qanda')))
                ;  
                
        \Queue::assertNotPushed(\App\Jobs\SendUserInvitation::class); //category twosome is false
        \Queue::assertNotPushed(\App\Jobs\SendAskableReward::class); //category reward is zero
    }

    private function getInput($title = 'Demographics')
    {
        return collect([
            'category' => Category::where(compact('title'))->first(),
            'coordinates' => [
                'lat' => $this->faker->latitude,
                'lon' => $this->faker->longitude,
            ],
            'mobiles' => [
                Phone::number('09178251991'),
                Phone::number('09189362340'),
            ],
        ]);
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
