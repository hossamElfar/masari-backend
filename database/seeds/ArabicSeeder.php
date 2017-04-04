<?php

use Illuminate\Database\Seeder;

class ArabicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Questionnaire::class, 5)
            ->create()
            ->each(function ($questionnaire) {
                $questionnaire->questions()->saveMany(factory(App\Question::class, 10)
                    ->create(['questionnaire_id' => $questionnaire->id])->each(function ($question) {
                        $question->answers()->saveMany(factory(App\Answer::class, 4)
                            ->create(['question_id' => rand(1, 20)]));
                    }));
            });
    }
}
