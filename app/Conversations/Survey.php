<?php

namespace App\Conversations;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\{Question, Answer};
use BotMan\BotMan\Messages\Conversations\Conversation;

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

    public function run()
    {
        $this->quizQuestions = Question::all();
        $this->questionCount = $this->quizQuestions->count();
        $this->quizQuestions = $this->quizQuestions->keyBy('id');

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
            $quizAnswer = Answer::find($answer->getValue());

            if (! $quizAnswer) {
                $this->say(trans('survey.fallback'));
                return $this->checkForNextQuestion();
            }

            $this->quizQuestions->forget($question->id);
            $this->currentQuestion++;

            $this->say("Your answer: {$quizAnswer->text}");
            $this->checkForNextQuestion();
        });
    }

    private function createQuestionTemplate(Question $question)
    {
        $questionTemplate = BotManQuestion::create(trans('survey.question', [
            'current' => $this->currentQuestion,
            'count' => $this->questionCount,
            'text' => $question->text
        ]));

        foreach ($question->answers as $answer) {
            $questionTemplate->addButton(Button::create($answer->text)->value($answer->id));
        }

        return $questionTemplate;
    }

    private function showResult()
    {
        $this->say(trans('invite.survey.finished'));
    }

}
