<?php

namespace App\Conversations;

use BotMan\BotMan\BotMan;
use App\Eloquent\Conversation;
use App\{Category, Question, Answer};
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;



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

    public function ready()
    {
        $this->setup()->introduction()->start();
    }

    protected function setup()
    {
        $this->categories = Category::all();
        $this->user = $this->getMessenger()->getUser();

        return $this;
    }

    public function introduction()
    {
        $this->bot->reply(trans('survey.introduction'));

        return $this;
    }

    public function start()
    {

        $this->askCategory();
    }

    public function askCategory()
    {
        $question = BotManQuestion::create(trans('survey.input.category'))
            ->fallback(trans('survey.category.error'))
            ->callbackId('survey.category.mobile')
            ;

        $this->categories->each(function ($category) use ($question) {
            $question->addButton(Button::create(ucfirst($category->title))->value($category->id));
        });

        return $this->ask($question, function (BotManAnswer $answer) {
            
            $category_id = $answer->getValue(); 

            $this->category = $this->categories->find($category_id);
            $this->surveyQuestions = $this->category->questions;
            $this->questionCount = $this->surveyQuestions->count();
            $this->surveyQuestions = $this->surveyQuestions->keyBy('id');            

            return $this->survey();
        });
    }

    protected function survey()
    {
        $this->say(trans('survey.info', ['count' => $this->questionCount]));
        $this->checkForNextQuestion();
    }

    private function checkForNextQuestion()
    {
        if ($this->surveyQuestions->count()) {
            return $this->askQuestion($this->surveyQuestions->first());
        }

        $this->showResult();
    }

    private function askQuestion(Question $question)
    {
        $this->ask($this->createQuestionTemplate($question), function (BotManAnswer $answer) use ($question) {
            $surveyAnswer = $answer->getValue();

            if (! $ans = $question->answers()->first()) {
                $ans = new Answer();
                $ans->user()->associate($this->user);
                $ans->question()->associate($question);
            }
            $ans->answer = array($surveyAnswer);
            $ans->save();   

            if (! $surveyAnswer) {
                $this->say(trans('survey.fallback'));
                return $this->checkForNextQuestion();
            }

            $this->surveyQuestions->forget($question->id);
            $this->currentQuestion++;

            $this->say("Your answer: {$surveyAnswer}");
            $this->checkForNextQuestion();
        });
    }

    private function createQuestionTemplate(Question $question)
    {
        $questionTemplate = BotManQuestion::create(trans('survey.question', [
            'current' => $this->currentQuestion,
            'count' => $this->questionCount,
            'text' => $question->question
        ]));

        foreach ($question->options as $answer) {
            if (in_array($question->type, ['radio', 'checkbox']))
                $questionTemplate->addButton(Button::create($answer)->value($answer));
        }

        return $questionTemplate;
    }

    private function showResult()
    {
        $this->say(trans('invite.survey.finished'));
    }

}
