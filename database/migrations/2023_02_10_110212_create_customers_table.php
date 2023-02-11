<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

      public function up()
    {
        try{
            if(!Schema::hasTable('customers')) {
                Schema::create('customers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('customer_name');
                $table->string('driver_license');
                $table->string('vehicle_number');
                $table->integer('phone')->unique();
                $table->dateTime('booking_start_time');
                $table->dateTime('booking_stop_time');
                $table->string('slot_id');
                $table->string('appointment_number');
                $table->string('parking_fee');
                $table->string('is_status');
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('slot_id')->references('id')->on('slots')
                  ->onDelete('cascade');
        
                });
            }
            
        } catch(\Exception $e){
            echo "something went wrong, check logs";
            Log::channel('migrationlogs')->error($e);
        }
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }

    
}

