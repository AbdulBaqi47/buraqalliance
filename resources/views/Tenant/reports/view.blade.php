@extends('Tenant.layouts.app')

@section('page_title')
    Reports
@endsection
@section('head')
    <style>
        .description-title {
            font-weight: 500;
        }

        .description-subtitle {
            font-size: 12px;
            color: #999;
            display: block;
            margin: 0;
            white-space: pre-line;
        }
    </style>
@endsection
@section('content')
    <!--begin::Portlet-->

    <div class="ledger__container mt-4">

        <div class="kt-portlet ">
            <div class="kt-portlet__body">
                <div class="kt-portlet__content">

                    <form class="kt-form" enctype="multipart/form-data" action="{{ route('tenant.admin.reports.generate') }}" method="POST">
                        @csrf


                        <div class="form-group row m-0 align-items-end">
                            <div class="col-lg-4">
                                <label class="">Report Type:</label>
                                <select class="form-control kr-select2" name="type" required>
                                    <option></option>
                                    <option value="income">Income</option>
                                    <option value="pending_balance">Pending Balance</option>
                                    <option value="salik" disabled>Salik</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="">Select Range:</label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" onchange="REPORTS.events.handleRange(event, this);" name="range" checked="" value="date"> Date
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" onchange="REPORTS.events.handleRange(event, this);" name="range" value="month"> Month
                                        <span></span>
                                    </label>
                                    <label class="kt-radio kt-radio--solid">
                                        <input type="radio" onchange="REPORTS.events.handleRange(event, this);" name="range" value="custom"> Custom
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-3">

                                <div class="range_picker" data-type="date" style="display: none;">
                                    <label>Select date:</label>
                                    <input type="text" readonly name="picker_date" data-state="date" class="kr-datepicker form-control">
                                </div>

                                <div class="range_picker" data-type="month" style="display: none;">
                                    <label>Select month:</label>
                                    <input type="text" readonly name="picker_month" data-state="month" class="kr-datepicker form-control">
                                </div>

                                <div class="range_picker" data-type="custom" style="display: none;">
                                    <label>Select range:</label>
                                    <input type="text" readonly name="picker_range" data-state="range" class="kr-datepicker form-control">
                                </div>

                            </div>

                            <div class="col-lg-2">

                                <button type="submit" class="btn btn-brand">Generate</button>

                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <div class="kt-portlet kt-portlet--height-fluid">

            <div class="kt-portlet__body">
                <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>
    </div>

@endsection

@section('foot')
{{-- ----------------------------------------------------------------------------
                        SCRIPTS (use in current page)
------------------------------------------------------------------------------ --}}

    <script type="text/javascript">
        var REPORTS = function() {



            /* Initialize the datatables */
            var init_table=function(){
                var table = $(REPORTS.table);

                // begin first table
                REPORTS.datatable = table.DataTable({
                    lengthMenu: [[-1], ["All"]],
                    responsive: true,
                    searchDelay: 100,
                    processing: false,
                    dom: 'rfti',
                    serverSide: false,
                    deferRender: true,
                    destroy:true,
                    ajax: "{{ route('tenant.admin.reports.data') }}",
                    rowId:'_id',
                    columns: [
                        {data: '_id', visible:false},
                        {data: 'date', orderable: false, width: "15%"},
                        {data: 'type', orderable: false, width: "60%"},
                        {data: 'actions', width: "25%"},
                    ],
                    order:[[1, 'desc']],
                    columnDefs: [
                        {
                            targets: -1,
                            title: '',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                if(typeof data == "undefined" || !data)return '';
                                if(full.status === 'inprogress'){
                                    return '<div class="kt-spinner kt-spinner--v2 kt-spinner--sm kt-spinner--brand"></div>';
                                }
                                return `
                                <a href="${full.attachment}" download class="btn btn-outline-hover-danger btn-sm btn-icon btn-circle"><i class="fa fa-download"></i></button>
                                `;
                            },
                        },
                        {
                            targets: 1,
                            render: function(data, type, full, meta) {
                               return moment(data).format("DD/MMM/YYYY hh:mm A");
                            },
                        },
                        {
                            targets: 2,
                            render: function(data, type, full, meta) {
                                data = data.replace(/_/, ' ');
                                data = data.charAt(0).toUpperCase() + data.slice(1);

                                var range = '';
                                if(!!full.range){

                                    if(!!full.range.type && full.range.type === "month"){
                                        range = ` ( ${moment(full.range.start).format('MMM YYYY')} )`;
                                    }
                                    else{
                                        if(full.range.start === full.range.end){
                                            range = ` ( ${moment(full.range.start).format('DD/MMM/YYYY')} )`;
                                        }
                                        else{
                                            range = ` ( ${moment(full.range.start).format('DD/MMM/YYYY')} - ${moment(full.range.end).format('DD/MMM/YYYY')} )`;
                                        }
                                    }


                                }
                               return data + range;
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

            };

            /* page settings */
            return {
                table: '#datatable',
                datatable: null,
                init: function() {
                    init_table();

                    this.events.handleRange();
                },

                events: {
                    handleRange: function(){
                        var checkedEl = $('[name="range"]:checked');

                        // Hide all range pickers
                        $('.range_picker').hide();

                        if(checkedEl.length > 0){
                            $('.range_picker[data-type="'+checkedEl.val()+'"]').show();
                        }
                    }
                }
            };
        }();


        $(function(){
            REPORTS.init();
        });
    </script>
@endsection
