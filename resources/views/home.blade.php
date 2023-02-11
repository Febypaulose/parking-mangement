@extends('layouts.app')

@section('content')
<style>
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
  }
  th, td {
      border-style: dotted;
  }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('All Customers') }}</div>
                
                <div class="card-body">
                  <a href="{{url('upcoming-booking')}}" >Upcoming Appoinments</a>
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                      <table class="table table-striped table-hover table-condensed">
                        <thead>
                          <tr>
                            <th><strong>No</strong></th>
                            <th><strong>Customer Name</strong></th>
                            <th><strong>Phone</strong></th>
                            <th><strong>Licenses </strong></th>
                            <th><strong>vehicle_number</strong></th>
                            <th><strong>Start Time</strong></th>
                            <th><strong>End time </strong></th>
                            <th><strong>Slot</strong></th>
                            <th><strong>Appointments</strong></th>
                            <th><strong>Price</strong></th>
                            <th><strong>Status</strong></th>
                            <th><strong>Created Time</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($customers))
                        
                        @foreach($customers as $key => $customers)
                        <tr>    
                          <th>{{$customers->id}}</th>
                          <th>{{$customers->customer_name}}</th>
                          <th>{{$customers->phone}}</th>
                          <th> <a href="{{url('download',$customers->driver_license)}}">Downoad PDF</a></th>
                          <th>{{$customers->vehicle_number}}</th>
                          <th>{{$customers->booking_start_time}}</th> 
                          <th>{{$customers->booking_stop_time}}</th>
                          <th>{{$customers->slot->Slot}}</th>
                          <th>{{$customers->appointment_number}}</th>
                          <th>{{$customers->parking_fee}}</th>
                          <th>{{$customers->is_status}}</th>
                          <th>{{$customers->created_at}}</th>                     
                      </tr>
                      @endforeach
                      @endif
                      
                  </tbody>
              </table>
          </div>
      </div>
  </div>
</div>
</div>
</div>
@endsection
