<?php

namespace App\Conversations;

use App\Answer;
use BotMan\BotMan\BotMan;
use App\Eloquent\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use MCesar\Survey\Models\{Category, Question};


class Survey extends Conversation
{
    /** @var Question */
    protected $quizQuestions;

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
        $this->category = Category::first();
        $this->user = $this->getMessenger()->getUser();

        $this->quizQuestions = $this->category->questions;
        $this->questionCount = $this->quizQuestions->count();
        $this->quizQuestions = $this->quizQuestions->keyBy('id');

        return $this;
    }

    public function introduction()
    {
        $this->bot->reply(trans('survey.introduction'));

        return $this;
    }

    public function start()
    {
        $this->survey();
    }

    protected function survey()
    {
        $this->say(trans('survey.info', ['count' => $this->questionCount]));
        $this->checkForNextQuestion();
    }

    private function checkForNextQuestion()
    {
        if ($this->quizQuestions->count()) {
            return $this->askQuestion($this->quizQuestions->first());
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

            $ans->forceFill(['answer' => $surveyAnswer]); 
            $ans->save();   

            if (! $surveyAnswer) {
                $this->say(trans('survey.fallback'));
                return $this->checkForNextQuestion();
            }

            $this->quizQuestions->forget($question->id);
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
            $questionTemplate->addButton(Button::create($answer)->value($answer));
        }

        return $questionTemplate;
    }

    private function showResult()
    {
        $this->say(trans('invite.survey.finished'));
    }

}
