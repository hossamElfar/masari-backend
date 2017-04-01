<?php

use Illuminate\Database\Seeder;

class FieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Field::class, 5)->create()->each(function ($field) {
            $users = array();
            array_push($users, \App\User::findOrFail(1));
            array_push($users, \App\User::findOrFail(2));
            $index = array_rand($users);
            $users[$index]->fileds()->attach($field);
        });
    }
}
