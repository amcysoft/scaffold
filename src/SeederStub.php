<?php

use Illuminate\Database\Seeder;

use App\{{capSingle}};

class {{capPlural}}TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {{capSingle}}::truncate();
        {{capSingle}}::create([
        	{{seeder_data}}
        ]);
    }
}
