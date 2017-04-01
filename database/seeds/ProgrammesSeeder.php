<?php

use Illuminate\Database\Seeder;

class ProgrammesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Program::class, 20)->create();
    }
}
