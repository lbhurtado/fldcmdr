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
        $mobiles = [
            Phone::number('09178251991'),
            Phone::number('09189362340'),
        ];
        // $mobile = Phone::number('09178251991');
        $category = 'Demographics';
        $count = 4;
        $coordinate = ['longitude' => 121.030962, 'latitude' => 14.644346];
        $categories = Category::all();

        \Queue::fake();

        foreach ($mobiles as $mobile) {
            $this->bot
                ->setUser(['id' => $channel_id])
                ->setDriver(TelegramDriver::class)
                ->receives($this->keyword)
                ->assertReply(trans('survey.intro'))
                ;

            if ($categories->count() == 0) {
                $this->bot->assertReply(trans('survey.abort'));
            }
            else {
                if ($categories->count() > 1) {
                    $this->bot
                        ->assertQuestion(trans('survey.input.category'))
                        ->receives(1)
                        ;
                }  

                if (config('chatbot.survey.location'))
                $this->bot
                    ->assertTemplate(OutgoingMessage::class)
                    ->receivesLocation($coordinate['latitude'], $coordinate['longitude'])
                    ;

                $this->bot
                    ->assertReply(trans('survey.info', compact('category', 'count')))
                    ->assertQuestion(trans('survey.input.mobile'))
                    ->receives($mobile)
                    ->assertTemplate(Question::class)
                    ->receives('Yes')
                    ->assertReply(trans('survey.answer', ['answer' => 'Yes']))
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

                \Queue::assertPushed(\App\Jobs\SendAskableReward::class);
            } 
        }
        
    }

/** @test */
    public function survey_required_question_run()
    {
        $channel_id = $this->faker->randomNumber(8);
        $mobiles = [
            Phone::number('09178251991'),
            Phone::number('09189362340'),
        ];
        $category = 'Demographics';
        $count = 4;
        $coordinate = ['longitude' => 121.030962, 'latitude' => 14.644346];
        $categories = Category::all();

        \Queue::fake();
        foreach ($mobiles as $mobile) {
            $this->bot
                ->setUser(['id' => $channel_id])
                ->setDriver(TelegramDriver::class)
                ->receives($this->keyword)
                ->assertReply(trans('survey.intro'))
                ;

            if ($categories->count() > 1) {
                $this->bot
                    ->assertQuestion(trans('survey.input.category'))
                    ->receives(1)
                    ;
            }  

            if (config('chatbot.survey.location'))
            $this->bot
                ->assertTemplate(OutgoingMessage::class)
                ->receivesLocation($coordinate['latitude'], $coordinate['longitude'])
                ;

            $this->bot
                ->assertReply(trans('survey.info', compact('category', 'count')))
                ->assertQuestion(trans('survey.input.mobile'))
                ->receives($mobile)
                ->assertTemplate(Question::class)
                ->receives('No')
                ->assertReply(trans('survey.abort'))
                ;

            $this->assertDatabaseHas('answers', ['answer' => 'No']);
            \Queue::assertNotPushed(\App\Jobs\SendAskableReward::class);
        }
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
                        'question' => 'Poll watcher?',
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
            // [
            //     'category' => 'Popular',
            //     'type' => 'text',
            //     'enabled_at' => now(),
            //     'options' => [
            //         'twosome' => false,
            //         'reward' => 0,
            //         'pollcount' => false,
            //     ],
            //     'questions' => [
            //         [
            //             'question' => 'Who will you vote for in the 2019 elections?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Erap Estrada',
            //                     'Isko Moreno',
            //                     'Lito Atienza',
            //                     'Alfredo Lim',
            //                 ],
            //             ],
            //         ],
            //         [
            //             'question' => 'Why?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Honest',
            //                     'Track Record',
            //                     'Popular',
            //                     'Rich',
            //                 ],
            //             ],
            //         ],
            //         [
            //             'question' => 'Why is Erap Estrada not your #1?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Corrupt',
            //                     'Gay',
            //                     'Tamad',
            //                     'Killer',
            //                 ],
            //             ],
            //         ],
            //         [
            //             'question' => 'Why is Isko Moreno not your #1?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Corrupt',
            //                     'Gay',
            //                     'Tamad',
            //                     'Killer',
            //                 ],
            //             ],
            //         ],
            //         [
            //             'question' => 'Why is Lito Atienza not your #1?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Corrupt',
            //                     'Gay',
            //                     'Tamad',
            //                     'Killer',
            //                 ],
            //             ],
            //         ],
            //         [
            //             'question' => 'Why is Alfredo Lim not your #1?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Corrupt',
            //                     'Gay',
            //                     'Tamad',
            //                     'Killer',
            //                 ],
            //             ],
            //         ],
            //         [
            //             'question' => 'What is the most important issue?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Crime',
            //                     'Corruption',
            //                     'Environment',
            //                 ],
            //             ],
            //         ],
            //         [
            //             'question' => 'What is your problem?',
            //             'type' => 'radio',
            //             'options' => [
            //                 'required' => false,
            //                 'values' => [
            //                     'Health',
            //                     'Labor',
            //                     'Education',
            //                 ],
            //             ],
            //         ],
            //     ],
            // ],
        ]);
    }
}
