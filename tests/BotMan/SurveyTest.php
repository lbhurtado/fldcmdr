<?php

namespace Tests\BotMan;

use Tests\TestCase;

use App\User;
use App\Category;
use App\Question as SurveyQuestion;
use App\Eloquent\Phone;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/survey';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
        // $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
        $survey = $this->getData();

        $survey->each(function ($category) {
            $createdCategory = Category::create([
                'title' => $category['category'],
                'extra_attributes' => $category['options'] ?? [],
            ]);
            collect($category['questions'])->each(function ($question) use ($createdCategory) {
                $createdQuestion = SurveyQuestion::create([
                    'category_id' => $createdCategory->id,
                    'question' => $question['question'],
                    'type' => $question['type'],
                    'extra_attributes' => $question['options'] ?? [],
                    'options' => $question['options'] ?? [],
                ]);
            });
        });

    }

    /** @test */
    public function survey_success_run()
    {
        $channel_id = $this->faker->randomNumber(8);
        $mobile = Phone::number('09178251991');
        $category = 'Demographics';
        $count = 3;

        \Queue::fake();

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('survey.intro'))
            ->assertQuestion(trans('survey.input.category'))
            ->receives(1)
            ->assertReply(trans('survey.info', compact('category', 'count')))
            ->assertQuestion(trans('survey.input.mobile'))
            ->receives($mobile)
            ->assertTemplate(Question::class)
            ->receives('Male')
            ->assertReply(trans('survey.answer', ['answer' => 'Male']))
            ->assertTemplate(Question::class)
            ->receives('18 to 30')
            ->assertReply(trans('survey.answer', ['answer' => '18 to 30']))
            ->assertTemplate(Question::class)
            ->receives('Tondo')
            ->assertReply(trans('survey.answer', ['answer' => 'Tondo']))
            ->assertReply(trans('survey.finished'))
            ;

        // $qanda = "Gender? Male\nAge Group? 18 to 30\nDistrict? Tondo\n";

        // $this->bot
        //     ->assertReply(trans('survey.result', compact('qanda')))
        //     ;

        \Queue::assertPushed(\App\Jobs\SendAskableReward::class);
    }

    public function survey_pollcount()
    {
        $channel_id = $this->faker->randomNumber(8);
        $category = 'Poll Count';
        $count = 4;

        $this->dbseed();

        \Queue::fake();

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('survey.intro'))
            ->assertQuestion(trans('survey.input.category'))
            ->receives(2)
            ->assertReply(trans('survey.info', compact('category', 'count')))
            ->assertQuestion(trans('survey.input.mobile'))
            ->receives($mobile)
            ->assertTemplate(Question::class)
            ->receives('Male')
            ->assertReply(trans('survey.answer', ['answer' => 'Male']))
            ->assertTemplate(Question::class)
            ->receives('18 to 30')
            ->assertReply(trans('survey.answer', ['answer' => '18 to 30']))
            ->assertTemplate(Question::class)
            ->receives('Tondo')
            ->assertReply(trans('survey.answer', ['answer' => 'Tondo']))
            ->assertReply(trans('survey.finished'))
            ;

        $qanda = "Gender? Male\nAge Group? 18 to 30\nDistrict? Tondo\n";

        $this->bot
            ->assertReply(trans('survey.result', compact('qanda')))
            ;

        \Queue::assertPushed(\App\Jobs\SendAskableReward::class);
    }

    private function getData()
    {
        return collect([
            [
                'category' => 'Demographics',
                'options' => [
                    'twosome' => true,
                    'reward' => 25,
                    'pollcount' => false,
                ],
                'questions' => [
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
                        'question' => 'District?',
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
        ]);
    }

    public function dbseed()
    {
        $node = User::create([
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

        $category = tap(Category::make(['title' => 'Poll Count']), function ($category) {
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

        $answers = [
            'Erap #Estrada' => 4,
            'Lito #Atienza' => 3,
            'Alfredo #Lim' => 2,
            'Isko #Moreno' => 1,
        ];


        foreach ($questions as $question) {
            $type = "string";
            $extra_attributes = ['values' => [0]];
            $question = tap(SurveyQuestion::make(compact('question', 'type', 'extra_attributes')), function ($question) use ($category) {
                $question->category()->associate($category);
                $question->save();
            });
        }

        $questions = SurveyQuestion::all();
        $node->each(function ($user) use ($questions, $answers) {
            $questions->each(function ($question) use ($user, $answers) {
                $votes = $answers[$question->question];
                $a = tap(Answer::make(['answer' => [$votes]]), function ($answer) use ($question, $user) {
                    $answer->question()->associate($question);
                    $answer->user()->associate($user);
                    $answer->askable()->associate($user);
                    $answer->save();
                });
            });
        });
    }
}
