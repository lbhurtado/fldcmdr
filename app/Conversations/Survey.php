<?php

namespace App\Conversations;

use BotMan\BotMan\BotMan;
use App\Conversations\Invite;
use App\Eloquent\{Conversation, Phone};
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use App\{Category, Question, Answer, Invitation as Invitee};
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;

class Survey extends Conversation
{
    protected $categories;

    /** @var Question */
    protected $surveyQuestions;

    /** @var integer */
    protected $userPoints = 0;

    /** @var integer */
    protected $userCorrectAnswers = 0;  

    /** @var integer */
    protected $questionCount = 0; // we already had this one

    /** @var integer */
    protected $currentQuestion = 1;

    /** @var App\User or App\Invitation */
    public $askable;

    /** @var float */
    protected $reward;

    /** @var bool */
    protected $twosome;

    public $qanda;

    public function ready()
    {
        $this->setup()->introduction()->start();
    }

    protected function setup()
    {
        $this->categories = Category::all();
        $this->askable = $this->user = $this->getMessenger()->getUser();

        return $this;
    }

    public function introduction()
    {
        $this->say(trans('survey.intro'));

        return $this;
    }

    public function start()
    {
        if ($this->categories->count() == 0)
            return $this->abort();

        return $this->inputCategory();
    }

    public function inputCategory()
    {
        // if ($this->categories->count() == 1) {
        //     $category_id = $this->categories->first()->id;
        //     $this->processCategoryQuestions($category_id);     

        //     return $this->survey();
        // }

        $question = BotManQuestion::create(trans('survey.input.category'))
            ->fallback(trans('survey.category.error'))
            ->callbackId('survey.input.cateogry')
            ;

        $this->categories->each(function ($category) use ($question) {
            $question->addButton(Button::create(ucfirst($category->title))->value($category->id));
        });

        return $this->ask($question, function (BotManAnswer $answer) {
            $this->processCategoryQuestions($answer->getValue());         

            return $this->inputLocation();
        });
    }

    protected function inputLocation()
    {
        return $this->askForLocation(trans('survey.input.location'), function (Location $location) {
            $lat = $location->getLatitude();
            $lon = $location->getLongitude();

            return $this->survey();
        });   
    }

    protected function survey()
    {
        $this->say(trans('survey.info', ['category' => $this->category->title, 'count' => $this->questionCount]));

        if ($this->category->twosome) {
            return $this->inputMobile();
        }
        else
            $this->checkForNextQuestion();
    }

    protected function inputMobile()
    {
        $question = BotManQuestion::create(trans('survey.input.mobile'))
            ->fallback(trans('invite.mobile.error'))
            ->callbackId('invite.input.mobile')
            ;

        return $this->ask($question, function (BotManAnswer $answer) {
            
            if (! $this->mobile = Phone::validate($answer->getText()))
                return $this->repeat(trans('invite.input.mobile'));
            
            $invitee = $this->user
                    ->invitees()
                    ->firstOrCreate([
                        'mobile' => $this->mobile
                    ],[
                        'role' => 'subscriber',
                        'message' => trans('invite.message'),
                    ]);

            if ($invitee->wasRecentlyCreated)
                $invitee->send();

            $this->askable = $invitee;

            return $this->checkForNextQuestion();
        });
    }

    protected function checkForNextQuestion()
    {
        if ($this->surveyQuestions->count())
            return $this->askQuestion($this->surveyQuestions->first());

        $this->showResult();
    }

    protected function showResult()
    {
        $this->say(trans('survey.finished'));

        $qanda = $this->qanda;
        $this->say(trans('survey.result', ['qanda' => $this->qanda]));

        $this->sendReward();
    }

    protected function sendReward()
    {
        if ($this->category->reward > 0.00) {
            if ($this->askable->sendReward($this->category->reward))
                $this->say(trans('survey.reward'));                 
        }
    }

    protected function askQuestion(Question $question)
    {
        $this->ask($this->createQuestionTemplate($question), function (BotManAnswer $answer) use ($question) {

            $surveyAnswer = $this->category->type == 'numeric' 
                            ? $answer->getText()
                            : $answer->getValue()
                            ;

            $q = $question->question;            
            $a = $surveyAnswer; 
            $this->qanda .= $q . " " . $a . "\n"; //refactor this!

            $askable_type = get_class($this->askable);
            $askable_id = $this->askable->id;
            $ans = $question->answers()->firstOrNew(compact('askable_type','askable_id'));
            $ans->user()->associate($this->user); //remove this in the future
            $ans->answer = $surveyAnswer;

            if ($this->category->type == 'numeric')
                $ans->weight = $surveyAnswer;
            $ans->save();   

            if (! $surveyAnswer) {
                $this->say(trans('survey.fallback'));
                
                return $this->checkForNextQuestion();
            }

            $this->surveyQuestions->forget($question->id);
            $this->currentQuestion++;

            $this->say(trans('survey.answer', ['answer' => $surveyAnswer]));
            $this->checkForNextQuestion();
        });
    }

    protected function abort()
    {
        $this->say(trans('survey.abort'));
    }

    protected function createQuestionTemplate(Question $question)
    {
        $questionTemplate = BotManQuestion::create(trans('survey.question', [
            'current' => $this->currentQuestion,
            'count' => $this->questionCount,
            'text' => $question->question
        ]));

        foreach ($question->values as $answer) {
            if (in_array($question->type, ['radio', 'checkbox']))
                $questionTemplate->addButton(Button::create($answer)->value($answer));
        }

        return $questionTemplate;
    }

    protected function processCategoryQuestions($category_id)
    {
        $this->category = $this->categories->find($category_id);
        $this->surveyQuestions = $this->category->questions;
        $this->questionCount = $this->surveyQuestions->count();
        $this->surveyQuestions = $this->surveyQuestions->keyBy('id');  
    }
}
