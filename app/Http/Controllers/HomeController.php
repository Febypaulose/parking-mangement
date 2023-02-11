<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;
use Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {     $customers=Customer::withTrashed()->get();
        return view('home',compact('customers'));
    }
     public function bookingLatest()
    {     $customers=Customer::orderBy('booking_start_time', 'asc')->get();
        return view('upcoming',compact('customers'));
    }
     public function download(Request $request)
    {    
      $file=  public_path('images/'.$request->id);

    $headers = array(
              'Content-Type: application/pdf',
            );

    return Response::download($file, 'filename.pdf', $headers);
    }
}
