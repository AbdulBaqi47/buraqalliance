@extends('Tenant.layouts.app')

@section('page_title')
    #{{$investor->id}} {{$investor->name}}
@endsection
@section('head')
    <style kr-ajax-head>
        .dataTables_wrapper .dataTable{
            margin:0 !important;
        }
    </style>
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-investor-detail" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title"> #{{$investor->id}} {{$investor->name}} </h3>

        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>

    <div class="kt-portlet__body">

        <div class="row">
            <div class="col-md-4">
                <table class="table table-bordered m-table">
                    <tbody>
                        <tr>
                            <th scope="row">Closing Balance</th>
                            <td>
                                <span>{{ $investor->balance }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Ref ID</th>
                            <td>
                                <span>{{ $investor->refid }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Name</th>
                            <td>
                                <span>{{ $investor->name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Phone</th>
                            <td>
                                <span>{{ $investor->phone }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Email</th>
                            <td>
                                <span>{{ $investor->email }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Notes</th>
                            <td>
                                <span>{{ $investor->notes }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Images</th>
                            <td>
                                @isset($investor->images)

                                    @foreach ($investor->images as $image)
                                    <a href="{{ Storage::url($image['src']) }}" target="_blank" class="kt-media border">
                                        <img width="100" data-placeholder-background="#eee" class="lozad" data-src="{{ Storage::url($image['src']) }}" />
                                    </a>
                                    @endforeach


                                @endisset
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>
            <div class="col-md-4">
                <table class="table table-bordered m-table dt-bookings">

                    <thead>
                        <tr>
                            <th>Bookings</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($investor->bookings->where('status', 'open') as $booking)

                        <tr>
                            <td>
                                <a href="{{ route('tenant.admin.bookings.single.view', $booking->id) }}" target="_blank">#{{ $booking->id }}</a>
                            </td>
                            <td>
                                <a href="{{ route('tenant.admin.vehicleledger.booking.view', $booking->id) }}" target="_blank">
                                    View statement
                                </a>
                            </td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <table class="table table-bordered m-table dt-vehicles">

                    <thead>
                        <tr>
                            <th>Vehicles</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($investor->bookings->where('status', 'closed') as $booking)

                        <tr>
                            <td>
                                <a href="{{ route('tenant.admin.bookings.single.view', $booking->id) }}" target="_blank">{{ $booking->vehicle->plate }} / {{ $booking->vehicle->chassis_number }}</a>
                            </td>
                            <td>
                                <a href="{{ route('tenant.admin.vehicleledger.booking.view', $booking->id) }}" target="_blank">
                                    View statement
                                </a>
                            </td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>

<!--end::Portlet-->




@endsection

@section('foot')
    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">

        var INVESTOR_DETAIL = {
            container: '#kt-portlet__create-investor-detail',
            data: {!! $investor !!},


            datatable: {
                booking: null,
                vehicle:null
            },
            table: null,

            Utils:{
                load: function(data){

                }
            },

            initTable: function(tableKey, tableEl){

                // begin first table
                this.datatable[tableKey] = tableEl.DataTable({
                    responsive: true,
                    dom:'tp',
                    lengthMenu: [5, 10, 25, 50, 100],
                    pageLength: 5,
                    searchDelay: 100,
                    processing: true,
                    serverSide: false,
                    deferRender: true,
                    ordering: false,

                });
            }
        }

        $(function(){
            INVESTOR_DETAIL.initTable('bookings', $('.dt-vehicles'));
            INVESTOR_DETAIL.initTable('vehicles', $('.dt-bookings'));

        });
    </script>
@endsection

