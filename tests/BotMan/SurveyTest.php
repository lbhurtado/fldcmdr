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
            ->assertReply(trans('survey.result'))
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
                        'question' => 'Gender',
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
                        'question' => 'Age Group',
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
                        'question' => 'District',
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
}
