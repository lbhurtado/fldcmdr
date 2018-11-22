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
            ]);
            collect($category['questions'])->each(function ($question) use ($createdCategory) {
                $createdQuestion = SurveyQuestion::create([
                    'category_id' => $createdCategory->id,
                    'question' => $question['question'],
                    'type' => $question['type'],
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

        $this->bot
            ->setUser(['id' => $channel_id])
            ->setDriver(TelegramDriver::class)
            ->receives($this->keyword)
            ->assertReply(trans('survey.introduction'))
            ->assertQuestion(trans('survey.input.category'))
            ->receives(1)
            
            ;
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
