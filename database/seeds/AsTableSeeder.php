<?php

use Illuminate\Database\Seeder;

class AsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Q::class, 10)->create()->each(function ($question) {
            $question->answers()->saveMany(factory(App\A::class,20)->make(['question_id'=>$question->id]));
        });
    }
}
