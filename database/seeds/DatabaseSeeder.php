<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        factory(App\News::class, 20)->create();
        factory(App\Video::class, 20)->create();
        factory(App\Link::class, 20)->create();

        factory(App\Questionnaire::class, 5)
            ->create()
            ->each(function ($questionnaire) {
                $questionnaire->questions()->saveMany(factory(App\Question::class, 30)
                    ->create(['questionnaire_id' => $questionnaire->id])->each(function ($question) {
                        $question->answers()->saveMany(factory(App\Answer::class, 4)
                            ->create(['question_id' => rand(1, 20)]));
                    }));
            });

        
    }
}
