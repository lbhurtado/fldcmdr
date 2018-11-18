<?php


use App\Answer;
use App\Question;
use Illuminate\Database\Seeder;

class QuestionAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Question::truncate();
        Answer::truncate();
        $questionAndAnswers = $this->getData();
        $questionAndAnswers->each(function ($question) {
            $createdQuestion = Question::create([
                'text' => $question['question'],
                'points' => $question['points'],
            ]);
            collect($question['answers'])->each(function ($answer) use ($createdQuestion) {
                Answer::create([
                    'question_id' => $createdQuestion->id,
                    'text' => $answer['text'],
                    'correct_one' => $answer['correct_one'],
                ]);
            });
        });
    }

    private function getData()
    {
        return collect([
            [
                'question' => 'Gender',
                'points' => '10',
                'answers' => [
                    ['text' => 'Male', 'correct_one' => true],
                    ['text' => 'Female', 'correct_one' => false],
                ],
            ],
            [
                'question' => 'Age Group',
                'points' => '10',
                'answers' => [
                    ['text' => '18 to 30', 'correct_one' => true],
                    ['text' => '31 to 40', 'correct_one' => false],
                    ['text' => '41 to 50', 'correct_one' => false],
                    ['text' => '51 and above', 'correct_one' => true],
                ],
            ],
            [
                'question' => 'District',
                'points' => '10',
                'answers' => [
                    ['text' => 'Intramuros', 'correct_one' => true],
                    ['text' => 'Tondo', 'correct_one' => false],
                    ['text' => 'Paco', 'correct_one' => false],
                    ['text' => 'Sampaloc', 'correct_one' => true],
                    ['text' => 'Sta. Ana', 'correct_one' => true],
                    ['text' => 'San Nicolas', 'correct_one' => true],
                    ['text' => 'Santa Cruz', 'correct_one' => true],
                    ['text' => 'Binondo', 'correct_one' => true],
                    ['text' => 'Port Area', 'correct_one' => true],
                    ['text' => 'Malate', 'correct_one' => true],
                    ['text' => 'Ermita', 'correct_one' => true],
                    ['text' => 'San Miguel', 'correct_one' => true],
                    ['text' => 'Pandacan', 'correct_one' => true],
                    ['text' => 'San Andres', 'correct_one' => true],
                    ['text' => 'Santa Mesa', 'correct_one' => true],
                ],
            ],
            [
                'question' => 'Who will you vote for in the 2019 elections?',
                'points' => '10',
                'answers' => [
                    ['text' => 'Erap Estrada', 'correct_one' => true],
                    ['text' => 'Isko Moreno', 'correct_one' => false],
                    ['text' => 'Lito Atienza', 'correct_one' => false],
                    ['text' => 'Alfredo Lim', 'correct_one' => true],
                ],
            ],
            [
                'question' => 'What is the most important issue?',
                'points' => '10',
                'answers' => [
                    ['text' => 'Crime', 'correct_one' => true],
                    ['text' => 'Corruption', 'correct_one' => false],
                    ['text' => 'Environment', 'correct_one' => true],
                ],
            ],
            [
                'question' => 'What is your problem?',
                'points' => '20',
                'answers' => [
                    ['text' => 'Health', 'correct_one' => false],
                    ['text' => 'Labor', 'correct_one' => false],
                    ['text' => 'Education', 'correct_one' => true],
                ],
            ],
        ]);
    }
}
