<?php

namespace Tests\BotMan;

use Tests\TestCase;
use App\{User, Category, Question, Answer};
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Foundation\Testing\WithFaker;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PollTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $keyword = '/poll';

    function setUp()
    {
        parent::setUp();
        $this->withoutEvents();
        $this->faker = $this->makeFaker('en_PH');
    }

    /** @test */
    public function poll_success_run()
    {
        $this->dbseed();

        $channel_id = $this->faker->randomNumber(8);

        $text  = "Estrada=20\n";
        $text .= "Atienza=15\n";
        $text .= "Lim=10\n";
        $text .= "Moreno=5\n";
        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply($text)
            ;

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
            $question = tap(Question::make(compact('question', 'type', 'extra_attributes')), function ($question) use ($category) {
                $question->category()->associate($category);
                $question->save();
            });
        }

        $questions = Question::all();
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
