@php
    $hasAccessToVehicleAddon = $helper_service->routes->has_access('tenant.admin.vehicleledger.addon.vehicle');
    $hasAccessToDriverAddon = $helper_service->routes->has_access('tenant.admin.vehicleledger.addon.driver') && $namespace === "booking";
@endphp


@extends('Tenant.layouts.app')

@section('page_title')
Vehicle Account Statement - {{ $title }}
@endsection
@section('head')

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.1/css/buttons.dataTables.min.css">

<style>
    .transaction__desc-title{
        font-weight: 400;
    }
    .transaction__desc-subtitle{
        font-size: 12px;
        color: #999;
        display: block;
        margin: 0;
        white-space: pre-line;
    }

    .transaction__prefix{
        font-size: 11px;
        letter-spacing: .6px;
        color: #7e9bff;
        position: relative;
        font-family: 'Roboto';
        line-height: 12px;
        margin-left: 7px;
    }
    .transaction__prefix svg{
        position: absolute;
        left: -15px;
        top: -2px;
    }
    .transaction__prefix svg path{
        fill: #627cff !important;
    }

    .driver-name{
        max-width: 10rem;
    }

    @media (min-width: 768px) {
        .dataTables_wrapper {
            position:relative;
        }
        .dataTables_wrapper  .dataTables_filter{
            position: absolute;
            right: 0;
            top: -3rem;
        }
    }

    .modal-group-details{
        z-index: 1049 !important;
    }

</style>
@endsection
@section('content')

<!--begin::Portlet-->

<div class="kt-portlet mt-3 mb-2">

    <div class="kt-portlet__body p-3">

        <div class="d-flex justify-content-around">

            <div class="d-flex align-items-center ledger__fields">

                <div class="d-flex flex-column">
                    <div class="kt-radio-inline">
                        <label class="kt-radio kt-radio--solid m-0 mr-2">
                            <input type="radio" onchange="VEHICLE_LEDGER.handleRange(event, this);" name="range" value="month" checked> Month
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--solid m-0 mr-2">
                            <input type="radio" onchange="VEHICLE_LEDGER.handleRange(event, this);" name="range" value="custom"> Custom
                            <span></span>
                        </label>
                    </div>
                    <span class="range_picker small text-warning" data-type="month" style="display: none;">This will use <b>"month"</b> field for filter</span>
                    <span class="range_picker small text-warning" data-type="custom" style="display: none;">This will use <b>"date"</b> field for filter</span>
                </div>

                <div class="range_picker pl-3 ml-2 border-left" data-type="month" style="display: none;">
                    <input type="text" readonly name="picker_month" onchange="VEHICLE_LEDGER.handleRange(event, this);" data-state="month" class="kr-datepicker form-control">
                </div>

                <div class="range_picker pl-3 ml-2 border-left" data-type="custom" style="display: none;">
                    <input type="text" readonly name="picker_range" onchange="VEHICLE_LEDGER.handleRange(event, this);" data-state="range" class="kr-datepicker form-control">
                </div>
            </div>


            @if ($namespace === "booking")
            <div class="d-flex flex-column align-items-center">
                <span class="text-dark h6 m-0 mb-1">Statement Structure</span>
                <div>
                    <div class="btn-group" role="group" aria-label="Statement Structure">
                        <a onclick="kingriders.Utils.updateUrlParem('new', '0');window.location.reload();return false" href="#" class="btn btn-outline-brand btn-elevate btn-sm btn-square @if(request()->get('new', 0) == 0) active @endif">
                            Combine
                        </a>

                        <a onclick="kingriders.Utils.updateUrlParem('new', '1');window.location.reload();return false" href="#" class="btn btn-outline-brand btn-elevate btn-sm btn-square @if(request()->get('new', 0) == 1) active @endif">
                            Separate
                        </a>
                    </div>
                </div>
            </div>
            @endif


        </div>

    </div>

</div>


<div class="kt-portlet">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Vehicle Account Statement</h3>

        </div>
        <div class="kt-portlet__head-toolbar">
            @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.cash_pay'))
            <button kr-ajax-size="70%" type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax-submit="VEHICLE_LEDGER.create_submit" kr-ajax-contentloaded="VEHICLE_LEDGER.modal_loaded" kr-ajax="{{route('tenant.admin.vehicleledger.transaction.cash_pay', $id)}}?namespace={{$namespace}}">
                <i class="flaticon2-plus-1"></i>
                Pay Cash
            </button>
            @endif

            @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.cash_receive'))
            <button kr-ajax-size="70%" type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax-submit="VEHICLE_LEDGER.create_submit" kr-ajax-contentloaded="VEHICLE_LEDGER.modal_loaded" kr-ajax="{{route('tenant.admin.vehicleledger.transaction.cash_receive', $id)}}?namespace={{$namespace}}">
                <i class="flaticon2-plus-1"></i>
                Receive Cash
            </button>
            @endif

            @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transfer_balance') && $namespace === 'booking')
            <button type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax-submit="VEHICLE_LEDGER.create_submit" kr-ajax-contentloaded="VEHICLE_LEDGER.modal_loaded" kr-ajax="{{route('tenant.admin.vehicleledger.transfer_balance', $booking->id)}}">
                <i class="flaticon2-plus-1"></i>
                Transfer Balance
            </button>
            @endif

            @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction'))
            <button kr-ajax-size="70%" type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax-submit="VEHICLE_LEDGER.create_submit" kr-ajax-contentloaded="VEHICLE_LEDGER.modal_loaded" kr-ajax="{{route('tenant.admin.vehicleledger.transaction', $id)}}?namespace={{$namespace}}">
                <i class="flaticon2-plus-1"></i>
                Add Record
            </button>
            @endif


        </div>
    </div>

    <div class="kt-portlet__body ledger__container">

        @if ($namespace === "booking")

        <div class="d-flex mb-2">

            <div class="d-flex flex-column">
                <div>
                {{$booking->investor->name}}
                </div>
                <div>
                    <strong>Vehicle Account ID:</strong>
                    <a href="{{ route('tenant.admin.bookings.single.view', $booking->id) }}">{{$title}}</a>
                </div>
            </div>

            <div class="kt-section pl-4 ml-5 border-left">
                <div class="kt-section__content">
                    @foreach ($booking->drivers as $driver)
                        <a title="{{ $driver->name }}" href="{{ route('tenant.admin.drivers.viewDetails', $driver->id) }}" class="kt-media kt-media--circle flex-column align-items-center ml-2 kt-media--lg align-top">

                            @if (isset($driver->profile_picture))
                                <img src="{{ Storage::url($driver->profile_picture) }}" alt="" class="border border-dark">
                            @else
                                <span class="border border-dark">KL{{ $driver->id }}</span>
                            @endif

                            @php
                                $name = preg_split('/[\s,]+/', $driver->full_name, 4);
                            @endphp

                            <h6 class="text-wrap small mt-2 driver-name">{{ $name[0] }} {{ $name[1] }} {{ $name[2] }}</h6>
                        </a>

                    @endforeach
                </div>
            </div>

        </div>

        @endif

        <div class="kt-heading kt-heading--space-sm">Vehicle Earnings</div>
        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable_1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Created at</th>
                    <th>Detail</th>
                    <th>Cash In</th>
                    <th>Cash Out</th>
                    <th>Balance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="kt-heading kt-heading--space-sm">Other Transactions</div>
        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable_2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Created at</th>
                    <th>Detail</th>
                    <th>Cash In</th>
                    <th>Cash Out</th>
                    <th>Balance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>


    </div>
</div>

<div class="modal fade modal-group-details" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Group Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr>
                            <th>Created at</th>
                            <th>Detail</th>
                            <th>Cash In</th>
                            <th>Cash Out</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-brand" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--end::Portlet-->
@if ($hasAccessToDriverAddon)
    <div class="kt-portlet mt-5">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">Driver Addon Payables</h3>
            </div>
        </div>
        <div class="kt-portlet__body addon__container">
            <table class="table table-striped- table-bordered table-hover table-checkable table-sm" id="datatable-addon-driver">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Driver</th>
                        <th>Addon Title</th>
                        <th>Payable</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endif
<!--Section::VEHICLE ADDON DATA-->

@if ($hasAccessToVehicleAddon)
    <div class="kt-portlet mt-5">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">Vehicle/Booking Addon Payables</h3>
            </div>
        </div>
        <div class="kt-portlet__body vehicle_addon__container">
            <table class="table table-striped- table-bordered table-hover table-checkable table-sm" id="datatable-addon-vehicle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle/Booking</th>
                        <th>Addon Title</th>
                        <th>Payable</th>
                        <th>Company</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endif

@if ($helper_service->routes->has_access('tenant.admin.getEntitySims.data'))
<div class="kt-portlet mt-5">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Sims</h3>
        </div>
    </div>
    <div class="kt-portlet__body sim__container">
        <table class="table table-striped- table-bordered table-hover table-checkable table-sm" id="datatable-sim-booking">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sim Number</th>
                    <th>Allowed Balance</th>
                    <th>Assign Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endif


@endsection


@section('foot')


{{------------------------------------------------------------------------------
                            SCRIPTS (use in current page)
--------------------------------------------------------------------------------}}

<script src="https://cdn.datatables.net/buttons/2.3.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script type="text/javascript">
    var VEHICLE_LEDGER = function(){

        function decodeHtml(html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }

        /* Initialize the datatables */
        var init_table=function(){

            var q = VEHICLE_LEDGER.Utils.buildQuery();

            // -----------------------
            //  Vehicle Earning Table
            // -----------------------

            var table = document.querySelector(VEHICLE_LEDGER.table1);

            // begin first table
            var datatable = $(table).DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: "rft<'d-flex justify-content-between'<i><B>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export to XLSX',
                        customize: function( xlsx ) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            // set cell style: Wrapped text
                            // debugger;
                            $('row c[r^="B"]', sheet).attr( 's', '55' );
                        },
                        exportOptions: {
                            columns: ':visible',

                            format: {
                                body: function( html, row, column, node ) {

                                    if(column === 1){
                                        var rowData = node.closest('table').datatable.row(node.closest('tr')).data();
                                        if(!!rowData && !!rowData.st_description){

                                            var data = $.fn.DataTable.Buttons.stripData( rowData.st_description, {stripHtml: false, trim: true, stripNewlines: false, decodeEntities: true} );

                                            html = rowData.st_title + "\r\n" + data.replace(/<br\s*\/?>/ig, "\r\n");
                                            // debugger;

                                        }
                                    }

                                    // begin with default formatting
                                    html = $.fn.DataTable.Buttons.stripData( html, {stripHtml: true, trim: true, stripNewlines: false, decodeEntities: true} );


                                    return html;
                                }
                            }
                        }
                    },
                ],
                serverSide: false,
                deferRender: true,
                destroy: true,
                ajax: {
                    "url": "{{ route('tenant.admin.vehicleledger.data_new') }}",
                    "data": {
                        "scope": 'earnings',
                        "id": {{$id}},
                        "namespace": "{{$namespace}}",
                        "filter_value": q.value,
                        "filter_type": q.type,
                    }
                },
                rowId: '_id',
                createdRow(row, data, dataIndex){
                    if(data.type !== 'skip'){
                        $(row).attr('data-activity-id',data._id);
                        $(row).attr('data-activity-modal','App\\Models\\VehicleLedgerItem');
                    }
                },
                columns: [
                    {data: '_id', visible: false},
                    {data: 'date', orderable: false, width: "15%"},
                    {data: 'description', orderable: false, width: "40%"},
                    {data: 'cr', orderable: false, width: "10%"},
                    {data: 'dr', orderable: false, width: "10%"},
                    {data: 'balance', orderable: false, width: "10%"},
                    {data: 'actions', width: "15%"},
                ],
                ordering: false,
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        render: function (data, type, full, meta) {
                            if (typeof data == "undefined" || !data || typeof full._id === 'number' || (!!full.groups&&full.groups.length>0)) return '';
                            let edit_field = '',
                            delete_field = '';

                            edit_field = `
                                @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.edit'))
                                    <a href="#" kr-ajax-size="70%" kr-ajax-contentloaded="Function()" kr-ajax-submit="VEHICLE_LEDGER.edit_completed" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax="${`{{ route('tenant.admin.vehicleledger.transaction.edit','_:param') }}?namespace={{$namespace}}&resource_id={{$id}}`.replace('_:param',full._id)}" class="btn btn-sm btn-outline-primary btn-icon btn-icon-md" title="Edit Ledger">
                                        <i class="la la-pencil"></i>
                                    </a>
                                @endif
                            `;
                            delete_field = `
                                @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.delete'))
                                    <a href="#" class="btn btn-sm btn-outline-danger btn-icon btn-icon-md" title="Delete Ledger" onclick="VEHICLE_LEDGER.handleDelete('${full._id}', '${full.channel}', event, this);return false;">
                                        <i class="la la-trash"></i>
                                    </a>
                                @endif
                            `;
                            return `
                                @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.viewDetails'))
                                    <a href="#" class="btn btn-sm btn-outline-focus btn-icon btn-icon-md" kr-ajax-size='30%' kr-ajax-contentloaded="Function()" kr-ajax-submit="Function()" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax="${`{{ route('tenant.admin.vehicleledger.transaction.viewDetails', '__param') }}`.replace('__param', full._id)}">
                                        <i class="la la-eye"></i>
                                    </a>
                                @endif
                                ${edit_field}
                                ${delete_field}
                            `;
                        },
                    },
                ],
            })
            .on('processing.dt',function( e, settings, processing ){
                if (processing){
                    KTApp.block('.ledger__container', {
                        size:"sm",
                        type: 'v2',
                        state: 'primary',
                        message:"Processing..."
                    });
                }else {
                    KTApp.unblock('.ledger__container');
                }
            });

            // add niceselect instanct to proto
            Object.defineProperty(table, 'datatable', {
                enumerable: false,
                configurable: true,
                value: datatable
            });



            // --------------------------
            //  Other Transaction Table
            // --------------------------

            table = document.querySelector(VEHICLE_LEDGER.table2);

            // begin second table
            datatable = $(table).DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: "rft<'d-flex justify-content-between'<i><B>>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export to XLSX',
                        customize: function( xlsx ) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            // set cell style: Wrapped text
                            // debugger;
                            $('row c[r^="B"]', sheet).attr( 's', '55' );
                        },
                        exportOptions: {
                            columns: ':visible',

                            format: {
                                body: function( html, row, column, node ) {

                                    if(column === 1){
                                        var rowData = node.closest('tr').datatable.row(node.closest('tr')).data();
                                        if(!!rowData && !!rowData.st_description){

                                            var data = $.fn.DataTable.Buttons.stripData( rowData.st_description, {stripHtml: false, trim: true, stripNewlines: false, decodeEntities: true} );

                                            html = rowData.st_title + "\r\n" + data.replace(/<br\s*\/?>/ig, "\r\n");
                                            // debugger;

                                        }
                                    }

                                    // begin with default formatting
                                    html = $.fn.DataTable.Buttons.stripData( html, {stripHtml: true, trim: true, stripNewlines: false, decodeEntities: true} );


                                    return html;
                                }
                            }
                        }
                    },
                ],
                serverSide: false,
                deferRender: true,
                destroy: true,
                ajax: {
                    "url": "{{ route('tenant.admin.vehicleledger.data_new') }}",
                    "data": {
                        "scope": 'others',
                        "id": {{$id}},
                        "namespace": "{{$namespace}}",
                        "filter_value": q.value,
                        "filter_type": q.type,
                    }
                },
                rowId: '_id',
                createdRow(row, data, dataIndex){
                    if(data.type !== 'skip'){
                        $(row).attr('data-activity-id',data._id);
                        $(row).attr('data-activity-modal','App\\Models\\VehicleLedgerItem');
                    }
                },
                columns: [
                    {data: '_id', visible: false},
                    {data: 'date', orderable: false, width: "15%"},
                    {data: 'description', orderable: false, width: "40%"},
                    {data: 'cr', orderable: false, width: "10%"},
                    {data: 'dr', orderable: false, width: "10%"},
                    {data: 'balance', orderable: false, width: "10%", visible:false},
                    {data: 'actions', width: "15%"},
                ],
                ordering: false,
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        render: function (data, type, full, meta) {
                            if (typeof data == "undefined" || !data || typeof full._id === 'number' || (!!full.groups&&full.groups.length>0)) return '';
                            let edit_field = '',
                            delete_field = '';

                            edit_field = `
                                @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.edit'))
                                    <a href="#" kr-ajax-size="70%" kr-ajax-contentloaded="Function()" kr-ajax-submit="VEHICLE_LEDGER.edit_completed" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax="${`{{ route('tenant.admin.vehicleledger.transaction.edit','_:param') }}?namespace={{$namespace}}&resource_id={{$id}}`.replace('_:param',full._id)}" class="btn btn-sm btn-outline-primary btn-icon btn-icon-md" title="Edit Ledger">
                                        <i class="la la-pencil"></i>
                                    </a>
                                @endif
                            `;
                            delete_field = `
                                @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.delete'))
                                    <a href="#" class="btn btn-sm btn-outline-danger btn-icon btn-icon-md" title="Delete Ledger" onclick="VEHICLE_LEDGER.handleDelete('${full._id}', '${full.channel}', event, this);return false;">
                                        <i class="la la-trash"></i>
                                    </a>
                                @endif
                            `;
                            return `
                                @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.viewDetails'))
                                    <a href="#" class="btn btn-sm btn-outline-focus btn-icon btn-icon-md" kr-ajax-size='30%' kr-ajax-contentloaded="Function()" kr-ajax-submit="Function()" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax="${`{{ route('tenant.admin.vehicleledger.transaction.viewDetails', '__param') }}`.replace('__param', full._id)}">
                                        <i class="la la-eye"></i>
                                    </a>
                                @endif
                                ${edit_field}
                                ${delete_field}
                            `;
                        },
                    },
                    {
                        targets: 4, // dr
                        render: function (data, type, full, meta) {

                            if(full.type === 'skip' && full._id == 2){
                                return full.balance;
                            }
                            return data;
                        }
                    }
                ],
            })
            .on('processing.dt',function( e, settings, processing ){
                if (processing){
                    KTApp.block('.ledger__container', {
                        size:"sm",
                        type: 'v2',
                        state: 'primary',
                        message:"Processing..."
                    });
                }else {
                    KTApp.unblock('.ledger__container');
                }
            });

            // add niceselect instanct to proto
            Object.defineProperty(table, 'datatable', {
                enumerable: false,
                configurable: true,
                value: datatable
            });


        };

        /* page settings */
        return {
            is_initialized: false,
            table1: '#datatable_1',
            table2: '#datatable_2',
            datatable1: null,
            datatable2: null,
            init: function () {
                init_table();
            },
            edit_completed(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if (state == 'completed') {

                    // Show success alert
                    if(typeof response.status !== "undefined" && response.status == 1){
                        if(typeof response.feed !== "undefined"){
                            var html = `
                                <div class="text-left">
                                    <p class="kt-font-bold mb-2">These tables are effected:</p>
                                    <ul class="list-group">
                                        ${response.feed.map(function(item){
                                            return `
                                                <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                                                    <span>${item.table}</span>
                                                    <i class="small">(${item.action})</i>
                                                </li>
                                            `;
                                        }).join('')}
                                    </ul>
                                </div>
                            `;

                            swal.fire({
                                position: 'center',
                                type: 'success',
                                title: 'Data saved!',
                                html: html,
                            });


                        }
                    }

                    // Hide group modal if shown
                    var groupModel = $('.modal-group-details');
                    if(groupModel.hasClass('show')){
                        groupModel.modal('hide')
                    }

                    // RELOAD TABLES
                    document.querySelector(VEHICLE_LEDGER.table1).datatable.ajax.reload();
                    document.querySelector(VEHICLE_LEDGER.table2).datatable.ajax.reload();
                }
            },
            create_submit: function (e) {
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if (state == 'beforeSend') {
                    /* request is not completed yet, we have form data available */



                } else {
                    /* request might be completed and we have response from server */

                    document.querySelector(VEHICLE_LEDGER.table1).datatable.ajax.reload();
                    document.querySelector(VEHICLE_LEDGER.table2).datatable.ajax.reload();

                    if(typeof VEHICLE_LEDGER_DRIVER_ADDON !== "undefined") VEHICLE_LEDGER_DRIVER_ADDON.datatable.ajax.reload();
                    if(typeof VEHICLE_LEDGER_VEHICLE_ADDON !== "undefined") VEHICLE_LEDGER_VEHICLE_ADDON.datatable.ajax.reload();
                    return; // For now just reload the table


                }

            },
            modal_loaded: function () {
                var q = VEHICLE_LEDGER.Utils.buildQuery();
                if (typeof SL_TRANSACTION_MODULE !== "undefined") SL_TRANSACTION_MODULE.Utils.reset_page({month: q.type === "month" ? q.value : q.value.split(',')[0]});
            },
            modal_closed:function(e){
                var target = e.target;

                if(target){
                    /* Get the index of modal */
                    var index = parseFloat(target.getAttribute('kr-index'))||null;
                    if(index){
                        /* Reset the modal */
                        kingriders.Plugins.KR_AJAX.resetModal(index);
                    }


                }
            },
            handleDelete: function(id, channel, e, self) {
                e.preventDefault();

                var html = `
                    <div class="d-flex flex-column">
                        <span>You won't be able to revert this!</span>
                        ${channel === 'import' ? `
                            <div class="alert alert-outline-warning m-0 py-2 px-3 mt-2" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Deleting this entry will not effect other entries since it was imported!</div>
                            </div>
                        ` : ''}
                    </div>
                `;

                swal.fire({
                    title: 'Are you sure?',
                    html: html,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    showLoaderOnConfirm: true,
                    scrollbarPadding: false,
                    allowOutsideClick: function() {
                        return !swal.isLoading()
                    },
                    preConfirm: function() {

                        var url = "{{ route('tenant.admin.vehicleledger.transaction.delete', '_:param') }}".replace("_:param", id);
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

                        var response = result.value;

                        // Show success alert
                        if(typeof response.feed !== "undefined"){
                            var html = `
                                <div class="text-left">
                                    <p class="kt-font-bold mb-2">These tables are effected:</p>
                                    <ul class="list-group">
                                        ${response.feed.map(function(item){
                                            return `
                                                <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                                                    <span>${item.table}</span>
                                                    <i class="small">(${item.action})</i>
                                                </li>
                                            `;
                                        }).join('')}
                                    </ul>
                                </div>
                            `;

                            swal.fire({
                                position: 'center',
                                type: 'success',
                                title: 'Records has been deleted.',
                                html: html,
                            });


                        }

                        // Hide group modal if shown
                        var groupModel = $('.modal-group-details');
                        if(groupModel.hasClass('show')){
                            groupModel.modal('hide')
                        }


                        // RELOAD TABLES
                        document.querySelector(VEHICLE_LEDGER.table1).datatable.ajax.reload();
                        document.querySelector(VEHICLE_LEDGER.table2).datatable.ajax.reload();
                    }
                });
            },
            Utils:{
                buildQuery:function(){
                    /* Use this to fetch input, like month and employee id */


                    var value=moment($('.ledger__fields [name="picker_month"]').val()||moment().format("MMMM YYYY"), "MMMM YYYY").format('YYYY-MM-DD');
                    var type = $('.ledger__fields [name="range"]:checked').val();

                    if(type === "custom"){
                        var el = $('.ledger__fields [name=picker_range]').data('daterangepicker');
                        value=`${el.startDate.format('YYYY-MM-DD')},${el.endDate.format('YYYY-MM-DD')}`
                    }


                    return {value, type};
                },
                update_url:function(){
                    /* add employee id and month as query string in url, so we can save the state */
                    var q = VEHICLE_LEDGER.Utils.buildQuery();

                    /* make data to append on url */
                    var data = {
                        t:q.type,
                        v:q.value,
                        new:1 // TEMP_NEWSTATEMENT_AND_INVESTOR_PAGE
                    }

                    /* update URL */
                    kingriders.Utils.buildQueryString(data);

                },
                update_input:function(q){
                    /* Use this to update the employee_id and month */
                    if(q.value && q.type){
                        $('.ledger__fields [name=range][value="'+q.type+'"]').prop('checked', true);

                        /* updating month value will trigger the 'change' event */
                        if(q.type === 'month'){
                            var formatted_month = new Date(q.value).format('mmmm yyyy');
                            $('.ledger__fields [name=picker_month]').attr('data-default', formatted_month).datepicker('update',formatted_month);
                        }
                        else{

                            var value = q.value.split(',');

                            $('.ledger__fields [name=picker_range]').data('daterangepicker').setStartDate(new Date(value[0]).format('mmm dd, yyyy'));
                            $('.ledger__fields [name=picker_range]').data('daterangepicker').setEndDate(new Date(value[1]).format('mmm dd, yyyy'));
                        }




                    }

                },
                fetchQuery:function(){
                    /* we will fetch month and range from url */
                    var value = kingriders.Utils.fetchQueryString("v")||null;
                    var type = kingriders.Utils.fetchQueryString("t")||null;

                    return { value, type };
                }
            },

            handleGroupDetailClick(event, self){
                event.preventDefault();

                var rowNode = $(self).parents('tr');
                if (rowNode.hasClass('child')) {//Check if the current row is a child row
                    rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                }
                var table = rowNode.closest('table');
                var rowData = table[0].datatable.row(rowNode).data();

                kingriders.Utils.isDebug() && console.log('clicked', rowData);

                var MODAL = $('.modal-group-details');
                var table = MODAL.find('table tbody');
                var html = `
                    ${rowData.groups.map(function(item){

                        // Remove all extra line breaks & make it clean
                        var desc = decodeHtml(item.description).trim()
                        .split('\n')
                        .map(function(x){return x.trim()})
                        .filter(function(x){return !!x})
                        .join('\n');

                        var suffix = '';

                        // Driver
                        if(!!item.driver_id){
                            desc += `<p class="m-0">Driver: <a href="${`{{ route('tenant.admin.drivers.viewDetails', '_:param') }}`.replace('_:param', item.driver_id)}">${item.driver.full_name}</a></p>`;
                        }

                        // Attachment
                        if(!!item.attachment && item.attachment !== ''){
                            suffix += `
                            <a href="${item.attachment}" target="_blank">
                                <i class="la la-file-picture-o"></i>
                            </a>
                            `;
                        }

                        // Append "Breakdown" button to addon_charge type entires
                        if(/_addon$/.test(item.tag)){
                            suffix += `
                            <a href="#" kr-ajax="${"{{ route('tenant.admin.vehicleledger.linked.view', '_:param') }}?view=addon_breakdown".replace('_:param', item._id)}" class="btn btn-sm btn-outline-primary btn-square btn-evelate ml-2 py-1 px-2" title="View BreakDown" kr-ajax-block-page-when-processing="" kr-ajax-size="50%" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()">
                                View Breakdown
                            </a>
                            `;
                        }

                        let edit_field = '',
                        delete_field = '';

                        edit_field = `
                            @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.edit'))
                                <a href="#" kr-ajax-size="70%" kr-ajax-contentloaded="Function()" kr-ajax-submit="VEHICLE_LEDGER.edit_completed" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax="${`{{ route('tenant.admin.vehicleledger.transaction.edit','_:param') }}?namespace={{$namespace}}&resource_id={{$id}}`.replace('_:param', item._id)}" class="btn btn-sm btn-outline-primary btn-icon btn-icon-md" title="Edit Ledger">
                                    <i class="la la-pencil"></i>
                                </a>
                            @endif
                        `;
                        delete_field = `
                            @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.delete'))
                                <a href="#" class="btn btn-sm btn-outline-danger btn-icon btn-icon-md" title="Delete Ledger" onclick="VEHICLE_LEDGER.handleDelete('${item._id}', '${item.channel}', event, this);return false;">
                                    <i class="la la-trash"></i>
                                </a>
                            @endif
                        `;

                        return `<tr data-activity-id="${item._id}" data-activity-modal="App\\Models\\VehicleLedgerItem">
                            <td>${moment(item.date).format("MMM DD, YYYY")}</td>
                            <td>
                                <span class="transaction__desc-title">${item.title}${suffix}</span>
                                <span class="transaction__desc-subtitle">${desc}</span>
                            </td>
                            <td>${item.type === "cr" ? item.amount : 0}</td>
                            <td>${item.type === "dr" ? item.amount : 0}</td>
                            <td>
                                @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.transaction.viewDetails'))
                                <a href="#" class="btn btn-sm btn-outline-focus btn-icon btn-icon-md" kr-ajax-size='30%' kr-ajax-contentloaded="Function()" kr-ajax-submit="Function()" kr-ajax-modalclosed="VEHICLE_LEDGER.modal_closed" kr-ajax="${`{{ route('tenant.admin.vehicleledger.transaction.viewDetails', '__param') }}`.replace('__param', item._id)}">
                                    <i class="la la-eye"></i>
                                </a>
                                @endif
                                ${edit_field}
                                ${delete_field}
                            </td>
                        </tr>`;
                    })}
                `;
                table.html(html);


                MODAL.modal('show');
            },

            handleRange: function(e, self){

                if(!this.is_initialized) return;
                var checkedEl = $('[name="range"]:checked');

                // Hide all range pickers
                $('.range_picker').hide();

                if(checkedEl.length > 0){
                    $('.range_picker[data-type="'+checkedEl.val()+'"]').show();
                }

                /* update the url before initiating */
                VEHICLE_LEDGER.Utils.update_url();

                // /* we need to update 'data-default' too soo it wont reset */
                // $('.ledger__fields [name="month"]').attr('data-default', $('.ledger__fields [name="month"]').val());

                /* init the table */
                VEHICLE_LEDGER.init();


            }

        };
    }();

    @if ($hasAccessToDriverAddon)

    var VEHICLE_LEDGER_DRIVER_ADDON = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = VEHICLE_LEDGER_DRIVER_ADDON.table;

            // begin first table
            VEHICLE_LEDGER_DRIVER_ADDON.datatable = table.DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: 't',
                serverSide: false,
                deferRender: true,
                destroy:true,
                ajax: {
                    "url": "{{ route('tenant.admin.vehicleledger.addon.driver') }}",
                    "data": {
                        "id": {{$id}},
                        "namespace": "{{$namespace}}"
                    }
                },
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false},
                    {data: 'driver', orderable: false, width: "40%"},
                    {data: 'setting.title', orderable: false, width: "20%"},
                    {data: 'remaining', orderable: false, width: "20%"},
                    {data: 'actions', width: "20%"},
                ],
                ordering:false,
                columnDefs: [
                    {
                        targets: -1,
                        title: '',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';

                            return `
                            @if ($helper_service->routes->has_access('tenant.admin.addons.charge'))

                            <a href="#" class="btn btn-sm btn-brand btn-square" kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLE_LEDGER_DRIVER_ADDON.modal_closed" kr-ajax-submit="VEHICLE_LEDGER.create_submit" kr-ajax-contentloaded="VEHICLE_LEDGER_DRIVER_ADDON.content_loaded" kr-ajax="${`{{route('tenant.admin.addons.charge', '_:param')}}?namespace={{ $namespace }}&resource_id={{ $id }}`.replace('_:param', full.id)}">
                                <i class="la la-eye"></i>
                                Charge
                            </a>

                            @endif

                            @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown'))
                            <a href='#' kr-ajax="${`{{ route('tenant.admin.addons.breakdown', '__param') }}?view=statement`.replace('__param', full.id)}" class="btn btn-sm btn-brand btn-square" title="View BreakDown"  kr-ajax-block-page-when-processing kr-ajax-size="90%" kr-ajax-modalclosed="VEHICLE_LEDGER_DRIVER_ADDON.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()">
                                <i class="la la-eye"></i>
                                Breakdown
                            </a>
                            @endif
                            `;
                        },
                    },
                    {
                        targets: 1,
                        render: (data, type, full, meta) => {
                            return ` <a href="${"{{route('tenant.admin.drivers.viewDetails', '_:param')}}".replace('_:param', data.id)}">KL${data.id} | ${data.name}</a>`;
                        }
                    },
                    {
                        targets: 2,
                        render: (data, type, full, meta) => {
                            return `
                                <div class="d-flex justify-content-between">
                                    ${data}

                                    ${!!full.status ? `<small class="mr-2 kt-font-${full.status === "inprogress" ? 'warning' : (full.status === "completed" ? 'success' : 'danger')}">${full.status === "inprogress" ? full.current_stage : full.status}</small>` :''}
                                </div>
                            `;
                        }
                    },
                ],
            })
            .on('processing.dt',function( e, settings, processing ){
                if (processing){
                    KTApp.block('.addon__container', {
                        size:"sm",
                        type: 'v2',
                        state: 'primary',
                        message:"Processing..."
                    });
                }else {
                    KTApp.unblock('.addon__container');
                }
            });

        };

        /* page settings */
        return {
            table:$('#datatable-addon-driver'),
            datatable:null,
            init:function(){
                init_table();
            },

            modal_closed:function(e){
                var target = e.target;

                if(target){
                    /* Get the index of modal */
                    var index = parseFloat(target.getAttribute('kr-index'))||null;
                    if(index){
                        /* Reset the modal */
                        kingriders.Plugins.KR_AJAX.resetModal(index);
                    }


                }
            },

            content_loaded:function(){
                var q = VEHICLE_LEDGER.Utils.buildQuery();

                if(typeof ADDON_CHARGE_MODULE !== "undefined")ADDON_CHARGE_MODULE.Utils.reset_page({month: q.type === "month" ? q.value : q.value.split(',')[0]});
            }
        };
    }();

    @endif

    @if( $hasAccessToVehicleAddon )
    var VEHICLE_LEDGER_VEHICLE_ADDON = function(){


        /* Initialize the datatables */
        var init_table=function(){
            var table = VEHICLE_LEDGER_VEHICLE_ADDON.table;

            // begin first table
            VEHICLE_LEDGER_VEHICLE_ADDON.datatable = table.DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: 'rfti',
                serverSide: false,
                deferRender: true,
                destroy:true,
                ajax: {
                    "url": "{{ route('tenant.admin.vehicleledger.addon.vehicle') }}",
                    "data": {
                        "id": {{$id}},
                        "namespace": "{{$namespace}}",
                    }
                },
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false},
                    {data: 'dt_title', orderable: false, width: "40%"},
                    {data: 'setting.title', orderable: false, width: "20%"},
                    {data: 'remaining', orderable: false, width: "20%"},
                    {data: 'actions', width: "20%"},
                ],
                ordering:false,
                columnDefs: [
                    {
                        targets: -1,
                        title: '',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';

                            return `
                            @if ($helper_service->routes->has_access('tenant.admin.addons.charge'))

                            <a href="#" class="btn btn-sm btn-brand btn-square" kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLE_LEDGER_VEHICLE_ADDON.modal_closed" kr-ajax-submit="VEHICLE_LEDGER.create_submit" kr-ajax-contentloaded="VEHICLE_LEDGER_VEHICLE_ADDON.content_loaded" kr-ajax="${`{{route('tenant.admin.addons.charge', '_:param')}}?namespace={{ $namespace }}&resource_id={{ $id }}`.replace('_:param', full.id)}">
                                <i class="la la-eye"></i>
                                Charge
                            </a>

                            @endif

                            @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown'))
                            <a href='#' kr-ajax="${`{{ route('tenant.admin.addons.breakdown', '__param') }}?view=statement`.replace('__param', full.id)}" class="btn btn-sm btn-brand btn-square" title="View BreakDown"  kr-ajax-block-page-when-processing kr-ajax-size="90%" kr-ajax-modalclosed="VEHICLE_LEDGER_VEHICLE_ADDON.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()">
                                <i class="la la-eye"></i>
                                Breakdown
                            </a>
                            @endif
                            `;
                        },
                    },
                    {
                        targets: 2,
                        render: (data, type, full, meta) => {
                            return `
                                <div class="d-flex justify-content-between">
                                    ${data}

                                    ${!!full.status ? `<small class="mr-2 kt-font-${full.status === "inprogress" ? 'warning' : (full.status === "completed" ? 'success' : 'danger')}">${full.status === "inprogress" ? full.current_stage : full.status}</small>` :''}
                                </div>
                            `;
                        }
                    },
                ],
            })
            .on('processing.dt',function( e, settings, processing ){
                if (processing){
                    KTApp.block('.vehicle_addon__container', {
                        size:"sm",
                        type: 'v2',
                        state: 'primary',
                        message:"Processing..."
                    });
                }else {
                    KTApp.unblock('.vehicle_addon__container');
                }
            });

        };

        /* page settings */
        return {
            table:$('#datatable-addon-vehicle'),
            datatable:null,
            init:function(){
                init_table();
            },

            modal_closed:function(e){
                var target = e.target;

                if(target){
                    /* Get the index of modal */
                    var index = parseFloat(target.getAttribute('kr-index'))||null;
                    if(index){
                        /* Reset the modal */
                        kingriders.Plugins.KR_AJAX.resetModal(index);
                    }


                }
            },

            content_loaded:function(){
                var q = VEHICLE_LEDGER.Utils.buildQuery();

                if(typeof ADDON_CHARGE_MODULE !== "undefined")ADDON_CHARGE_MODULE.Utils.reset_page({month: q.type === "month" ? q.value : q.value.split(',')[0]});
            }
        };
    }();
    @endif


    @if ($helper_service->routes->has_access('tenant.admin.getEntitySims.data'))

    var VEHICLE_LEDGER_VEHICLE_SIMS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = VEHICLE_LEDGER_VEHICLE_SIMS.table;

            // begin first table
            VEHICLE_LEDGER_VEHICLE_SIMS.datatable = table.DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: 't',
                serverSide: false,
                deferRender: true,
                destroy:true,
                ajax: {
                    url : `{{ route('tenant.admin.getEntitySims.data')}}`,
                    data:{
                        source_id: '{{$id}}',
                        source_type: "booking"
                    },
                },
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false},
                    {data: 'sim', orderable: false, width: "20%"},
                    {data: 'allowed_balance', orderable: false, width: "20%"},
                    {data: 'assign_date', orderable: false, width: "20%"},
                    {data: 'sim', width: "20%"},
                ],
                ordering:false,
                columnDefs: [
                    {
                        targets: -1,
                        title: 'Company',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return data.company;
                        },
                    },
                    {
                        targets: 1,
                        orderable: false,
                        render(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return data.number;
                        }
                    },
                    {
                        targets: 3,
                        orderable: false,
                        render(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return moment(data).format("MMMM DD, YYYY");
                        }
                    },
                ],
            })
            .on('processing.dt',function( e, settings, processing ){
                if (processing){
                    KTApp.block('.sim__container', {
                        size:"sm",
                        type: 'v2',
                        state: 'primary',
                        message:"Processing..."
                    });
                }else {
                    KTApp.unblock('.sim__container');
                }
            });

        };

        /* page settings */
        return {
            table:$('#datatable-sim-booking'),
            datatable:null,
            init:function(){
                init_table();
            },

            modal_closed:function(e){
                var target = e.target;

                if(target){
                    /* Get the index of modal */
                    var index = parseFloat(target.getAttribute('kr-index'))||null;
                    if(index){
                        /* Reset the modal */
                        kingriders.Plugins.KR_AJAX.resetModal(index);
                    }


                }
            },

            content_loaded:function(){

                if(typeof ADDON_CHARGE_MODULE !== "undefined")ADDON_CHARGE_MODULE.Utils.reset_page({month: q.month});
            }
        };
    }();
    VEHICLE_LEDGER_VEHICLE_SIMS.init();
    @endif

    $(function(){



        /* fetch data from url and initiate table */
        var q = VEHICLE_LEDGER.Utils.fetchQuery();

        /* update input */
        VEHICLE_LEDGER.Utils.update_input(q);

        VEHICLE_LEDGER.is_initialized = true;

        VEHICLE_LEDGER.handleRange();

        @if ($hasAccessToDriverAddon)
        VEHICLE_LEDGER_DRIVER_ADDON.init();
        @endif

        @if( $hasAccessToVehicleAddon )
        VEHICLE_LEDGER_VEHICLE_ADDON.init();
        @endif

    });
</script>


@endsection
