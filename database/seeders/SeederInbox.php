<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inbox;

class SeederInbox extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 25; $i++) { 
            Inbox::create([
                'name' => 'sample input : '.$i,
                'email' => 'sample input : '.$i,
                'phone' => 'sample input : '.$i,
                'subjeck' => 'sample input : '.$i,
                'message' => 'sample input : '.$i,
            ]);
        }
    }
}
