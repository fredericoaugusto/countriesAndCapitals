<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    private $app_data;

    public function __construct()
    {
        $this->app_data = require(app_path('app_data.php'));
    }

    public function startGame()
    {
        return view('home');
    }

    public function prepareGame(Request $request)
    {
        $request->validate([
            'total_questions' => 'required|integer|min:3|max:30',
        ], [
            'total_questions.required' => 'Por favor, insira o número total de perguntas',
            'total_questions.integer' => 'O número total de perguntas deve ser um número inteiro',
            'total_questions.min' => 'O número total de perguntas deve ser no mínimo :min',
            'total_questions.max' => 'O número total de perguntas não deve ser maior que :max',
        ]);

        $total_questions = (int)$request->input('total_questions');

        $quiz = $this->prepareQuiz($total_questions);

        dd($quiz);
    }

    private function prepareQuiz(int $total_questions): array
    {
        $questions = [];
        $total_countries = count($this->app_data);

        $indexes = range(0, $total_countries - 1);
        shuffle($indexes);
        $indexes = array_slice($indexes, 0, $total_questions);

        $question_number = 1;
        foreach ($indexes as $index) {
            $question['question_number'] = $question_number++;
            $question['country'] = $this->app_data[$index]['country'];
            $question['correct_answer'] = $this->app_data[$index]['capital'];

            $other_capitals = array_column($this->app_data, 'capital');

            $other_capitals = array_diff($other_capitals, [$question['correct_answer']]);

            shuffle($other_capitals);

            $question['wrong_answers'] = array_slice($other_capitals, 0, 3);

            $question['correct'] = null;

            $questions[] = $question;
        }

        return $questions;
    }
}
