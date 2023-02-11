<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Slot;
class SlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	foreach (range('A', 'Z') as $char) {
    		for ($i = 1; $i < 6; $i++){
    			$slot_number =$char ."0". $i;
    			Slot::create([
    				'Slot' => $slot_number ,
    				'is_slot' => 0,
    			]);
    		}
    	}


    }
    
}
