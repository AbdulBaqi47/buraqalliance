@extends('Tenant.layouts.app')

@section('page_title')
    Breakdown TLX-{{ $ledger->id }}
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
<div class="kt-portlet" id="kt-portlet__tlbreakdown" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title"> Breakdown TLX-{{ $ledger->id }} </h3>
        </div>
        <div class="kt-portlet__head-label">
            <div class="kr-widget__tagger kr-widget__tagger--warning">
                <div>
                    <span>
                        AED
                        <span> {{ $ledger->amount }} </span>
                    </span>
                    <small>TOTAL</small>
                </div>
            </div>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>

    <div class="kt-portlet__body">

        <div class="row">

            <div class="col-md-5">
                <div class="d-flex justify-content-between">
                    <div class="h6">Payables Detail</div>
                    <span class="kt-font-inverse-light h5 m-0"> {{ $total_payables }} </span>
                </div>
                <table class="table table-bordered m-table table-sm dt-payables">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Paid?</th>
                            <th>Account</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ledger->source->payables as $payable)
                        <tr>
                            <th scope="row">{{ $payable->id }}</th>
                            <td>
                                <span class="text-nowrap">
                                    {{ Carbon\Carbon::parse($payable->additional_details['charge_date'])->format('d F, Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="tl-payable-input" data-amount="@if(isset($payable->amount)){{ $payable->amount }}@else{{ $payable->real_amount }}@endif">
                                    <span class="text-nowrap">
                                        @if(!isset($payable->amount))
                                            <span class="kt-font-warning">{{ $payable->real_amount }}</span>
                                        @else
                                        {{ $payable->amount }}
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if ($payable->status === 'paid')
                                    <i class="fa fa-check text-success"></i>
                                @else
                                    <i class="fa fa-times text-danger"></i>
                                @endif
                            </td>
                            <td>
                                <span class="text-nowrap">
                                    {{ $payable->account->title }}
                                </span>
                            </td>
                            <td>

                                @if(isset($payable->amount))

                                    <div class="tl-payable-confirmation" style="display: none;">
                                        <div class="d-flex">
                                            <span class="text-success cursor-pointer" data-id="{{ $payable->id }}" onclick="TL_BREAKDOWN.events.handleConfirmClick(this, event)">
                                                <i class="fa fa-check"></i>
                                            </span>
                                            <span class="text-danger cursor-pointer ml-2" onclick="TL_BREAKDOWN.events.handleCancelClick(this, event)">
                                                <i class="fa fa-times"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="tl-payable-actions">
                                        <div class="d-flex">
                                            <span class="text-primary cursor-pointer" onclick="TL_BREAKDOWN.events.handleEditClick(this, event)">
                                                <i class="fa fa-edit"></i>
                                            </span>
                                            <span class="text-danger cursor-pointer ml-2" data-id="{{ $payable->id }}" onclick="TL_BREAKDOWN.events.handleDeleteClick(this, event)">
                                                <i class="fa fa-trash"></i>
                                            </span>
                                        </div>
                                    </div>

                                @endif
                            </td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="col-md-7">
                <div class="d-flex justify-content-between">
                    <div class="h6">Chargeable Detail</div>
                    <span class="kt-font-inverse-light h5 m-0"> {{ $total_chargeables }} </span>
                </div>
                <table class="table table-bordered m-table table-sm dt-chargeables">
                    <thead>
                        <tr>
                            <th>To</th>
                            <th>Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ledger->source->chargeables as $chargeable)
                        <tr>
                            <td scope="row">
                                <span>
                                    @php
                                        $title = $chargeable->source_id;
                                        if($chargeable->source_model === "App\Models\Tenant\Driver"){
                                            

                                            $title = '
                                                <div class="d-flex flex-column">
                                                    <span>
                                                        <a href="'.route('tenant.admin.drivers.viewDetails', $chargeable->source->id).'">'.$chargeable->source->full_name.'</a>
                                                    </span>
                                                    <small>
                                                        <a href="'.route('tenant.admin.statementledger.driver.view', $chargeable->source->id).'">View statement</a>
                                                    </small>
                                                </div>
                                            ';

                                        }
                                        else if($chargeable->source_model === "App\Models\Tenant\VehicleBooking"){
                                            $booking = $chargeable->source;

                                            $id = '';
                                            if($booking->status === "closed"){

                                                $id = 'V#'.$booking->id.' / '.$booking->vehicle->plate;
                                            }
                                            else{
                                                $id = 'B#'.$booking->id;

                                            }

                                            $title = '
                                                <div class="d-flex flex-column">
                                                    <span>
                                                        <a href="'.route('tenant.admin.bookings.single.view', $booking->id).'">'.$id.'</a>
                                                    </span>
                                                    <small>
                                                        <a href="'.route('tenant.admin.vehicleledger.booking.view', $booking->id).'">View statement</a>
                                                    </small>
                                                </div>
                                            ';
                                        }
                                        else if($chargeable->source_model === "App\Models\Tenant\Vehicle"){
                                            $vehicle = $chargeable->source;

                                            $id = $vehicle->plate;
                                            $stHtml = '';
                                            if(isset($vehicle->vehicle_booking_id)){
                                                $id = '#'.$vehicle->vehicle_booking_id.' / '.$vehicle->plate;
                                                $stHtml = '
                                                    <small>
                                                        <a href="'.route('tenant.admin.vehicleledger.booking.view', $vehicle->vehicle_booking_id).'">View statement</a>
                                                    </small>
                                                ';
                                            }

                                            $title = '
                                                <div class="d-flex flex-column">
                                                    <span>
                                                        <a href="'.route('tenant.admin.vehicles.single.edit', $vehicle->id).'">'.$id.'</a>
                                                    </span>
                                                    '.$stHtml.'
                                                </div>
                                            ';

                                        }
                                        else if($chargeable->source_model === "App\Models\Tenant\User"){
                                            $user = $chargeable->source;

                                            $month = Carbon\Carbon::parse($ledger->month)->format('Y-m-d');

                                            $title = '
                                                <div class="d-flex flex-column">
                                                    <span>
                                                        '.$user->name.' | <a class="small" href="'.route('tenant.admin.employee.ledger.view').'?m='.$month.'&e='.$user->id.'">View ledger</a>
                                                    </span>
                                                </div>
                                            ';

                                        }
                                    @endphp
                                    {!! $title !!}
                                    <small class="d-block">{{ (new $chargeable->source_model)->getTable() }}</small>
                                </span>
                            </td>
                            <td>

                                <div class="d-flex flex-column">

                                    <div class="d-flex justify-content-between">
                                        <span class="text-nowrap">
                                            {{ $chargeable->amount }}
                                        </span>

                                        @if( isset($chargeable->additional_details) && isset($chargeable->additional_details['chargedon']) )
                                            @if (isset($chargeable->additional_details['charged']) && $chargeable->additional_details['charged'] === true)
                                                <span class="kt-badge kt-badge--inline kt-badge--success">
                                                    <small>
                                                        Charged On:
                                                        <span class="kt-font-bold ml-1">{{ Carbon\Carbon::parse($chargeable->additional_details['chargedon'])->format('d/M/Y') }}</span>
                                                    </small>
                                                </span>
                                            @else
                                                <span class="kt-badge kt-badge--inline kt-badge--metal">
                                                    <small>
                                                        Pending Charge:
                                                        <span class="kt-font-bold ml-1">{{ Carbon\Carbon::parse($chargeable->additional_details['chargedon'])->format('d/M/Y') }}</span>
                                                    </small>
                                                </span>
                                            @endif

                                        @endif

                                    </div>

                                    @if(isset($chargeable->additional_details))
                                        @if ($chargeable->source_model === \App\Models\Tenant\Addon::class && isset($chargeable->additional_details['addon_type']) && isset($chargeable->additional_details['addon_expense']))
                                            <small><i>{{ $chargeable->additional_details['addon_type'] }}: {{ $chargeable->additional_details['addon_expense'] }}</i></small>
                                        @endif
                                        @if (isset($chargeable->additional_details['date']) && isset($chargeable->additional_details['month']))
                                            <small><i>{{ Carbon\Carbon::parse($chargeable->additional_details['month'])->format('M Y') }}: {{ Carbon\Carbon::parse($chargeable->additional_details['date'])->format('d/M/Y') }}</i></small>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if (isset($chargeable->description) && $chargeable->description !== '')
                                <span style="white-space: pre-line;">
                                    {!! trim($chargeable->description) !!}
                                </span>
                                @endif
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

        var TL_BREAKDOWN = {
            container: '#kt-portlet__tlbreakdown',
            datatable: {
                payables: null,
                chargeables: null
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
                    destroy: true,
                    serverSide: false,
                    deferRender: true,
                    ordering: false,

                });
            },


            // Events
            events: {

                // "Edit" button - Make amount editable
                handleEditClick: function(self, event){

                    // Make the amount editable
                    // append 2 buttons [tick/cross] to take confirmation from user

                    var rowNode = self.closest('tr');

                    var amountEl = $(rowNode).find('.tl-payable-input');
                    amountEl.html('<input type="number" class="form-control py-1 px-2 h-auto border-primary" value="'+amountEl.attr('data-amount')+'" />');

                    $(rowNode).find('.tl-payable-confirmation').show();
                    $(rowNode).find('.tl-payable-actions').hide();

                },

                // "Delete" button, ask for confirmation and delete data
                handleDeleteClick: function(self, event){

                    var id = self.getAttribute('data-id');

                    swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        showLoaderOnConfirm: true,
                        scrollbarPadding: false,
                        allowOutsideClick: function() {
                            return !swal.isLoading()
                        },
                        preConfirm: function() {

                            var url = "{{ route('tenant.admin.tl.breakdown.delete_payable', '_:param') }}".replace("_:param", id);
                            return $.ajax({
                                url: url,
                                headers: {
                                    'X-NOFETCH': ''
                                },
                                /* don't allow fetch accounts */
                                type: 'DELETE',
                            })
                            .done(function(response) {
                                return response;
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {

                                swal.hideLoading();

                                /* this will handle & show errors */
                                var errorObj = kingriders.Plugins.KR_AJAX.generateErrors(jqXHR);

                                swal.showValidationMessage(errorObj.msg);
                            });

                        },
                    })
                    .then(function(result) {
                        if (result.value && result.value.status === 1) {

                            var rowNode = self.closest('tr');

                            /* remove from datatables */
                            TL_BREAKDOWN.datatable.payables.row(rowNode).remove();

                            /* remove from DOM */
                            rowNode.remove();

                            swal.fire(
                                'Deleted!',
                                'Record has been deleted.',
                                'success'
                            );
                        }
                    });

                },

                // After "Edit" click, user can either "Tick" or "Cross"
                // "Tick" btn - Save data on server
                handleConfirmClick: function(self, event){

                    var rowNode = self.closest('tr');
                    var amount = $(rowNode).find('.tl-payable-input input').val();
                    var id = self.getAttribute('data-id');

                    // Generate formdata to sent to sevrer
                    var formData = new FormData();
                    formData.append('amount', amount);

                    var url = "{{ route('tenant.admin.tl.breakdown.edit_payable', '_:param') }}".replace('_:param', id);
                    $.ajax({
                        url: url,

                        /* don't allow fetch accounts */
                        headers: {
                            'X-NOFETCH': ''
                        },

                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                    })
                    .done(function(response) {
                        if(typeof response.status !== "undefined" && response.status == 1){
                            // Show success alert
                            toastr.success("Data saved successfully!");

                            // Make the amount uneditable
                            var rowNode = self.closest('tr');
                            var amountEl = $(rowNode).find('.tl-payable-input');
                            amountEl.attr('data-amount', amountEl.find('input').val());
                            amountEl.html(amountEl.attr('data-amount'));

                            $(rowNode).find('.tl-payable-confirmation').hide();
                            $(rowNode).find('.tl-payable-actions').show();
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {

                        /* this will handle & show errors */
                        var errorObj = kingriders.Plugins.KR_AJAX.showErrors(jqXHR);

                    });



                },

                // After "Edit" click, user can either "Tick" or "Cross"
                // "Cross" btn - Make the amount un-editable
                handleCancelClick: function(self, event){

                    var rowNode = self.closest('tr');

                    var amountEl = $(rowNode).find('.tl-payable-input');
                    amountEl.html(amountEl.attr('data-amount'));

                    $(rowNode).find('.tl-payable-confirmation').hide();
                    $(rowNode).find('.tl-payable-actions').show();

                },
            }
        }

        $(function(){
            TL_BREAKDOWN.initTable('payables', $('.dt-payables'));
            TL_BREAKDOWN.initTable('chargeables', $('.dt-chargeables'));
        });
    </script>
@endsection

