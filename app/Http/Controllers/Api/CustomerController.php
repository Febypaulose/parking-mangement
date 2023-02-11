<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Slot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use Carbon\Carbon;
use Illuminate\Validation\Rule; 

class CustomerController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required',
                'driver_license' => 'required|mimes:pdf|min:2000|max:5000',
                'vehicle_number' => 'required',
                'phone' =>     Rule::unique('customers')->where(function($query) {
                  $query->where('is_status','Booking');
              }),
                'booking_start_time' => 'required|date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),
                'booking_stop_time' => 'required|date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),

            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $is_slot_taken=Customer::where('booking_start_time',$request->booking_start_time)->where('booking_stop_time',$request->booking_stop_time)->where('vehicle_number',$request->vehicle_number)->first();
            if(!empty($is_slot_taken)){
               return $this->sendError('Validation Error.', "Vehicle is already booked"); 
            }

               $driver_license = time().'.'.$request->driver_license->extension();  
               $request->driver_license->move(public_path('images'), $driver_license);

               $booking_start_time=Carbon::createFromFormat('Y-m-d H:s:i',$request->booking_start_time);
               $booking_stop_time=Carbon::createFromFormat('Y-m-d H:s:i',$request->booking_stop_time);
               if($booking_start_time>$booking_stop_time){
                return $this->sendError('Validation Error.', "Select proper date"); 
                }
            $totalDuration = $booking_start_time->diffInHours($booking_stop_time);

            $slot_number=Slot::where('is_slot',0)->first();
            $slot=$slot_number->id;

            $start = 3;
            $diff=$totalDuration-$start;
            $incremnet=5;
            $basic = 10;
            if($totalDuration <=3){
                $price=10;
            }
            else{
                if($totalDuration>=24){
                    $days=round($totalDuration/24);
                    $fine=$days*100;
                    $price_diffe= $diff*5;
                    $price_increment=$basic+$price_diffe;
                    $price= $price_increment+$fine;
                }
                else{

                  $price_diffe= $diff*5;
                  $price=$basic+$price_diffe;
              }

            }
  

            $customer=Customer::orderBy('id', 'desc')->first();
            if(empty($customer)){
               $appointments = $slot_number->Slot.'AAA';
           }
           else{

             $str_to_replace = $slot_number->Slot;
             $input_str=$customer->appointment_number;
             $current_appointment_number = $str_to_replace . substr($input_str, 3);
             $alphabets = ["A" => 1, "B" => 2, "C" => 3, "D" => 4, "E" => 5, "F" => 6, "G" => 7, "H" => 8, "I" => 9, "J" => 10, "K" => 11, "L" => 12, "M" => 13, "N" => 14, "O" => 15, "P" => 16, "Q" => 17, "R" => 18, "S" => 19, "T" => 20, "U" => 21, "V" => 22, "W" => 23, "X" => 24, "Y" => 25, "Z" => 26];

             "Current: " . $current_appointment_number . "<br />";
             $arr = str_split($current_appointment_number);

             $appointments = $arr[0] . $arr[1] . $arr[2];
             if (count($arr) > 4) {
                if ($alphabets[$arr[count($arr) - 1]] == 26) {
                    $arr[count($arr) - 1] = "ZA";
                } else {
                    $arr[count($arr) - 1] = array_search(($alphabets[$arr[count($arr) - 1]] + 1), $alphabets);
                }

                $last_numbers = "";
                for ($i = 3; $i < count($arr); $i++) {
                    $last_numbers .= $arr[$i];
                }

                $appointments .= $last_numbers;
            } else {
                if ($alphabets[$arr[3]] == 26) {
                    $appointments .= "ZA";
                } else {
                    $appointments .= array_search(($alphabets[$arr[3]] + 1), $alphabets);
                }
            }

            }

            $Customer = new Customer();
            $Customer->customer_name = $request->customer_name;
            $Customer->driver_license = $driver_license;
            $Customer->vehicle_number = $request->vehicle_number;
            $Customer->phone = $request->phone;
            $Customer->booking_start_time = $request->booking_start_time;
            $Customer->booking_stop_time = $request->booking_stop_time;
            $Customer->slot_id = $slot;
            $Customer->appointment_number = $appointments;
            $Customer->parking_fee = $price;
            $Customer->is_status = "Booking";
            $Customer->save();
            $post = Slot::find($slot);
            $post->is_slot = 1;
            $post->save();
            $success['Appointment number']=$appointments;
            $success['Slot']=$slot_number->Slot;
            $success['Expeted parking fee']=$price;
            return $this->sendResponse($success, $request->customer_name.' Booked successfully.');

            }  

    public function checkout(Request $request)
            {

                 $validator = Validator::make($request->all(), [
                    'appointment_number' => 'required',
                    'booking_start_time' => 'required|date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),
                    'booking_stop_time' => 'required|date_format:Y-m-d H:i:s|after_or_equal:' . date(DATE_ATOM),

                ]);
                
            if($validator->fails()){
                    return $this->sendError('Validation Error.', $validator->errors());       
                }
        $customer=Customer::where('appointment_number',$request->appointment_number)->first();
        if(empty($customer)){
            return $this->sendError('Validation Error.', "No Booking allowed");   
        }
            $booking_start_time=Carbon::createFromFormat('Y-m-d H:s:i',$request->booking_start_time);
             $booking_stop_time=Carbon::createFromFormat('Y-m-d H:s:i',$request->booking_stop_time);
             if($booking_start_time>$booking_stop_time){
                return $this->sendError('Validation Error.', "Select proper date"); 
            }
            $totalDuration = $booking_start_time->diffInHours($booking_stop_time);

            $slot_number=Slot::where('is_slot',0)->first();
            $slot=$slot_number->id;

            $start = 3;
            $diff=$totalDuration-$start;
            $incremnet=5;
            $basic = 10;
            if($totalDuration <=3){
                $price=10;
            }
            else{
                if($totalDuration>=24){
                    $days=round($totalDuration/24);
                    $fine=$days*100;
                    $price_diffe= $diff*5;
                    $price_increment=$basic+$price_diffe;
                    $price= $price_increment+$fine;
                }
                else{

                  $price_diffe= $diff*5;
                  $price=$basic+$price_diffe;
              }

            } 

        $Customer = Customer::find($customer->id);
        $Customer->is_status = "Checkoff";
        $Customer->parking_fee = $price;
        $Customer->booking_start_time = $request->booking_start_time;
        $Customer->booking_stop_time = $request->booking_stop_time;
        $Customer->save();
        $customer->delete();
        $post = Slot::find($customer->slot_id);
        $post->is_slot = 0;
        $post->save();
        $success="checkoff";
        return $this->sendResponse($success, $request->customer_name.'Thanks come again');
        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
