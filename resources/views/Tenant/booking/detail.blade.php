@extends('Tenant.layouts.app')

@section('page_title')
    #{{$booking->id}} {{$booking->status === "open" ? "Booking" : "Vehicle"}}
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet mt-5" id="kt-portlet__create-booking-detail" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title"> {{$booking->status === "open" ? "Booking" : "Vehicle"}} #{{$booking->id}} </h3>

        </div>
        <div class="kt-portlet__head-toolbar">
            @switch($booking->status)
                @case("open")
                    <span class="badge badge-warning">Open</span>
                    @break
                @case("closed")
                    <span class="badge badge-success">Closed</span>
                    @break
                @default

            @endswitch
        </div>
    </div>

    <div class="kt-portlet__body">

        <div class="row">
            <div class="col-md-8">
                <table class="table table-bordered m-table">
                    <tbody>
                        <tr>
                            <th scope="row">Investor</th>
                            <td>
                                <span>{{ $booking->investor->refid }} {{ $booking->investor->name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Vehicle Type</th>
                            <td>
                                <span>{{ $booking->vehicle_type->make }} {{ $booking->vehicle_type->model }} {{ $booking->vehicle_type->cc }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Initial Deposit Amount (AED)</th>
                            <td>
                                <span>{{ $booking->initial_amount }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Deposit Account</th>
                            <td>
                                <span>
                                    @isset($booking->account_relation)
                                        {{ $booking->account_relation->transaction->account->title }}
                                    @else
                                        No initial amount depositted
                                    @endisset
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Other Detail</th>
                            <td>
                                <pre style="white-space: pre-wrap;">{{ $booking->notes }}</pre>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="col-md-4">
                @if ($booking->status === "closed")
                    <table class="table table-bordered m-table bg-success text-white">

                        <tbody>
                            <tr>
                                <th scope="row">Plate</th>
                                <td>
                                    <span>{{ $booking->vehicle->plate }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Chassis Number</th>
                                <td>
                                    <span>{{ $booking->vehicle->chassis_number }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Engine Number</th>
                                <td>
                                    <span>{{ $booking->vehicle->engine_number }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Color</th>
                                <td>
                                    <span>{{ ucfirst($booking->vehicle->color) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                @isset($booking->reserve_vehicle)
                <div class="kt-heading m-0 mb-2">Reserved vehicle</div>
                <table class="table table-bordered m-table bg-primary text-white m-0">

                    <tbody>
                        <tr>
                            <th scope="row">Plate</th>
                            <td>
                                <span>{{ $booking->reserve_vehicle->plate }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Chassis Number</th>
                            <td>
                                <span>{{ $booking->reserve_vehicle->chassis_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Engine Number</th>
                            <td>
                                <span>{{ $booking->reserve_vehicle->engine_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Color</th>
                            <td>
                                <span>{{ ucfirst($booking->reserve_vehicle->color) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                @endisset
            </div>
        </div>


    </div>
    <div class="kt-portlet__foot kt-portlet__foot--sm kt-align-right kt-portlet__foot--solid">

        <button type="button" class="btn btn-sm btn-brand " onclick="BOOKING_DETAIL.print()">Print receipt</button>
        <button type="button" class="btn btn-sm btn-outline-brand ">Add drivers</button>

        @if ($booking->status === "open")

        @if (!isset($booking->reserve_vehicle))

        <button type="button" class="btn btn-sm btn-outline-brand " kr-ajax-size="30%" kr-ajax-submit="BOOKING_DETAIL.vehicle_module.form_submit" kr-ajax-contentloaded="BOOKING_DETAIL.vehicle_module.form_loaded" kr-ajax-preload kr-ajax="{{ route('tenant.admin.bookings.vehicle.reserve') }}">Reserve Vehicle</button>
        @endif

        <button type="button" class="btn btn-sm btn-outline-brand " kr-ajax-size="30%" kr-ajax-submit="BOOKING_DETAIL.vehicle_module.form_submit" kr-ajax-contentloaded="BOOKING_DETAIL.vehicle_module.form_loaded" kr-ajax-preload kr-ajax="{{ route('tenant.admin.bookings.vehicle.add') }}">Add vehicle details</button>
        @endif

        <button type="button" class="btn btn-sm btn-outline-brand ">Goto investor statement</button>
        <a href="{{route('tenant.admin.vehicleledger.booking.view', $booking->id)}}" class="btn btn-sm btn-outline-brand ">Goto {{$booking->status === "open" ? "booking" : 'vehicle'}} statement</a>

    </div>

    <div class="invoice__wrapper" style="display:none"></div>

</div>

<!--end::Portlet-->




@endsection

@section('foot')
    <script kr-ajax-head src="{{asset('js/print.min.js')}}" type="text/javascript"></script>

    {{------------------------------------------------------------------------------
                                    HANDLEBARS TEMPLAATES
    --------------------------------------------------------------------------------}}

    {{-- INVOICE TEMPLATE --}}
    @include('Tenant.booking.handlebars_templates.print_receipt')

    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">

    var BOOKING_DETAIL = {
        container: '#kt-portlet__create-booking-detail',
        data: {!! $booking !!},
        print: function(){

            setTimeout(function(){
                /* pass data to handlebars template to compile */
                var template = $('#handlebars-printreceipt').html();
                // Compile the template data into a function
                var templateScript = Handlebars.compile(template);

                var html = templateScript(BOOKING_DETAIL.data);
                $('.invoice__wrapper').html(html);
                printJS('invoice_slip', 'html');
            },0);
        },

        vehicle_module: {

            form_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){

                }
                else if(state=="error"){

                }
                else{

                    window.location.reload();
                }
            },
            form_loaded:function(){
                if(typeof BOOKINGVEHICLE_MODULE !== "undefined"){
                    BOOKINGVEHICLE_MODULE.Utils.reset_page(BOOKING_DETAIL.data);
                }
            }
        },
    }

    $(function(){


    });
    </script>
@endsection

