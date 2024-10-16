@extends('Tenant.layouts.app')

@section('page_title')
    Daily Ledger
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
    @if (
        $helper_service->routes->has_access('tenant.admin.expense.add') || $helper_service->routes->has_access('tenant.admin.addons.expense.add'))
        <div class="kt-portlet mt-5">
            <div class="kt-portlet__body align-items-center">

                <div class="d-flex">
                    @if ($helper_service->routes->has_access('tenant.admin.expense.add'))
                        <button type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-size="30%"
                            kr-ajax-submit="LEDGER.create_submit" kr-ajax-contentloaded="LEDGER.modal_loaded" kr-ajax-preload
                            kr-ajax="{{ route('tenant.admin.expense.add') }}">
                            <i class="flaticon2-plus-1"></i>
                            Company Expense
                        </button>
                    @endif

                    @if ($helper_service->routes->has_access('tenant.admin.addons.expense.add'))
                        <button type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-modalclosed="LEDGER.modal_closed" kr-ajax-size="30%" kr-ajax-submit="LEDGER.create_submit" kr-ajax-contentloaded="LEDGER.modal_loaded" kr-ajax="{{ route('tenant.admin.addons.expense.add','staff') }}">
                            <i class="flaticon2-plus-1"></i>
                            Staff Addon Expense
                        </button>
                    @endif

                    @if ($helper_service->routes->has_access('tenant.admin.addons.expense.add'))
                        <button type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-modalclosed="LEDGER.modal_closed" kr-ajax-size="30%" kr-ajax-submit="LEDGER.create_submit" kr-ajax-contentloaded="LEDGER.modal_loaded" kr-ajax="{{ route('tenant.admin.addons.expense.add','driver') }}">
                            <i class="flaticon2-plus-1"></i>
                            Driver Addon Expense
                        </button>
                    @endif

                    @if ($helper_service->routes->has_access('tenant.admin.addons.expense.add'))
                        <button type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-modalclosed="LEDGER.modal_closed" kr-ajax-size="30%" kr-ajax-submit="LEDGER.create_submit" kr-ajax-contentloaded="LEDGER.modal_loaded" kr-ajax="{{ route('tenant.admin.addons.expense.add','vehicle') }}">
                            <i class="flaticon2-plus-1"></i>
                            Vehicle Addon Expense
                        </button>
                    @endif

                    @if ($helper_service->routes->has_access('tenant.admin.ledger.edit'))
                        <button type="button" data-edit-ledger hidden kr-ajax-submit="LEDGER.edit_module.submit"
                            kr-ajax-size="30%" kr-ajax-contentloaded="LEDGER.edit_module.modal_loaded" kr-ajax-preload
                            kr-ajax="{{ route('tenant.admin.ledger.edit') }}" class="btn btn-info btn-elevate btn-square">
                            <i class="fa fa-pencil"></i>
                            Edit Ledger
                        </button>
                    @endif
                </div>


            </div>
        </div>
    @endif
    <div class="kt-portlet">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">Ledger</h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">
                        <label class="kt-checkbox m-1">
                            <input id="cash_filter" @if(isset($_GET['filter']) && $_GET['filter'] === 'all') checked @endif name="cash_filter" type="checkbox" onchange="cash_filter(this)"> All
                            entries
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body">
            <div>
                <div class="row">
                    <div class="form-group col-4 col-xl-2 col-lg-2">
                        <label>Filter By</label>
                        <label class="kt-radio col">
                            <input type="radio" @if(($_GET['type'] ?? 'day') === 'day') checked @endif name="date_filter_type" value="day"> Date
                            <span></span>
                        </label>
                        <label class="kt-radio col">
                            <input type="radio"  @if(($_GET['type'] ?? 'day') === 'month') checked @endif name="date_filter_type" value="month"> Month
                            <span></span>
                        </label>
                    </div>
                    <div id="datefilter2parent" class="form-group col-6 col-lg-3 col-xl-3">
                        <label for="date_filter2">Select Date</label>
                        <input id="date_filter2" data-default="{{$_GET['value'] ?? \Carbon\Carbon::now()->format('Y-m-d')}}" type="text" required readonly name="datefilter2" data-state="{{$_GET['type'] ?? 'day'}}" class="kr-datepicker form-control" onchange="date_filter(this)">
                    </div>
                </div>
                    </div>
            <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Month</th>
                        <th>Description</th>
                        <th>Cash In</th>
                        <th>Cash Out</th>
                        <th>Paid By</th>
                        <th>Account</th>
                        <th hidden></th> {{-- for sorting purpose --}}
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>

@endsection

@section('foot')
    {{-- ----------------------------------------------------------------------------
                            SCRIPTS (use in current page)
------------------------------------------------------------------------------ --}}

    <script type="text/javascript">
        var LEDGER = function() {

            /* Initialize the datatables */
            var init_table = function() {
                var filter = kingriders.Utils.getUrlParem('filter');
                var filterBy = kingriders.Utils.getUrlParem('type');
                var filterValue = kingriders.Utils.getUrlParem('value');
                // begin first table
                LEDGER.datatable = $(LEDGER.table).DataTable({
                    responsive: true,
                    searchDelay: 100,
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    deferRender: true,
                    ajax: "{{ route('tenant.admin.ledger.data') }}" + "/" + filter + `?filter_by=${filterBy}&value=${filterValue}`,
                    rowId: 'id',
                    createdRow(row, data, dataIndex){
                        $(row).attr('data-activity-id',data.id);
                        $(row).attr('data-activity-modal','App\\Models\\Ledger');
                    },
                    columns: [{
                            data: 'id',
                            visible: false
                        },
                        {
                            data: 'date',
                            orderable: false,
                            width: "12%"
                        },
                        {
                            data: 'month',
                            orderable: false,
                            width: "12%"
                        },
                        {
                            data: 'description',
                            orderable: false,
                            width: "31%"
                        },
                        {
                            data: 'cr',
                            orderable: false,
                            width: "7%"
                        },
                        {
                            data: 'dr',
                            orderable: false,
                            width: "7%"
                        },
                        {
                            data: 'paid_by',
                            orderable: false,
                            width: "10%"
                        },
                        {
                            data: 'account',
                            orderable: false,
                            width: "20%"
                        },
                        {
                            data: 'created_at',
                            visible: false
                        },
                        {
                            data: 'actions',
                            width: "10%"
                        },
                    ],
                    order: [
                        [8, 'desc']
                    ],
                    columnDefs: [
                        {
                            targets: -1,
                            title: 'Actions',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                return `
                                @if ($helper_service->routes->has_access('tenant.admin.ledger.edit'))
                                <a href="#" class="btn btn-sm btn-outline-primary btn-icon btn-icon-md" title="Edit Ledger" onclick="LEDGER.edit_module.handleClick(this);return false;">
                                    <i class="la la-pencil"></i>
                                </a>
                                @endif

                                @if ($helper_service->routes->has_access('tenant.admin.ledger.delete'))
                                <a href="#" class="btn btn-sm btn-outline-danger btn-icon btn-icon-md" title="Delete Ledger" onclick="LEDGER.events.handleDeleteClick(event, this);return false;">
                                    <i class="la la-trash"></i>
                                </a>
                                @endif`;
                            },
                        },
                        {
                            targets: 2,
                            title: 'Month',
                            orderable: false,
                            render: function(data, type, full, meta) {
                                const date = new Date(data);
                                const year = date.getFullYear();
                                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                const month = monthNames[date.getMonth()];
                                return `${month}, ${year}`;
                            },
                        },
                        {
                            targets: 7,
                            render: function(data, type, full, meta) {

                                if(full.tag === "transaction_ledger"){
                                    return `
                                    <div class="d-flex flex-column">
                                        <span>${data}</span>
                                        @if ($helper_service->routes->has_access('tenant.admin.tl.breakdown'))

                                        <a href="#" title="View" kr-ajax-block-page-when-processing kr-ajax-size="90%" kr-ajax-modalclosed="LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.tl.breakdown', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-info"></i>
                                            View Breakdown
                                        </a>

                                        @endif
                                    </div>
                                    `;
                                }
                                if(full.tag === "client_income"){
                                    return `
                                    <div class="d-flex flex-column">
                                        <span>${data}</span>
                                        @if ($helper_service->routes->has_access('tenant.admin.tl.breakdown'))

                                        <a href="#" title="View" kr-ajax-block-page-when-processing kr-ajax-size="90%" kr-ajax-modalclosed="LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.tl.breakdown', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-info"></i>
                                            View Income Breakdown
                                        </a>

                                        @endif
                                    </div>
                                    `;
                                }
                                if(full.tag === "vehiclebills"){
                                    return `
                                    <div class="d-flex flex-column">
                                        <span>${data}</span>
                                        @if ($helper_service->routes->has_access('tenant.admin.vehicle.bills.breakdown'))
                                            <a href="#" title="View" kr-ajax-block-page-when-processing kr-ajax-size="90%" kr-ajax-modalclosed="LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.vehicle.bills.breakdown', '_:param')}}".replace('_:param', full.id)}">
                                                <i class="la la-info"></i>
                                                View Bill Breakdown
                                            </a>
                                        @endif
                                    </div>
                                    `;
                                }
                                if(full.tag === "sim_bill"){
                                    return `
                                    <div class="d-flex flex-column">
                                        <span>${data}</span>
                                        @if ($helper_service->routes->has_access('tenant.admin.tl.breakdown'))

                                        <a href="#" title="View" kr-ajax-block-page-when-processing kr-ajax-size="90%" kr-ajax-modalclosed="LEDGER.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.tl.breakdown', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-info"></i>
                                            View Bill Breakdown
                                        </a>

                                        @endif
                                    </div>
                                    `;
                                }

                                return data;
                            },
                        }
                    ],
                });

            };

            /* page settings */
            return {
                table: '#datatable',
                datatable: null,
                init: function() {
                    init_table();
                },
                create_submit: function(e) {
                    var response = e.response;
                    var modal = e.modal;
                    var state = e.state; // can be 'beforeSend' or 'completed'
                    var linker = e.linker;
                    if (state == 'beforeSend') {
                        /* request is not completed yet, we have form data available */


                        /* we need to create  a row dynamically and add to datatables */
                        var tempid = $(LEDGER.table).find('tbody tr').length;


                        var rowObj = {};
                        rowObj.id = tempid;
                        rowObj.account = '<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                        rowObj.amount = parseFloat(response.amount) || 0;
                        rowObj.cr = '<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.dr = '<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.date = '<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                        rowObj.month = '<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                        rowObj.description = `
                        <span class="description-subtitle"><span class="kr-skeleton-box" style="width: 13%;height: 10px;"></span></span>
                        <span class="description-title"><span class="kr-skeleton-box" style="width: 50%;height: 15px;"></span></span>
                        `;
                        rowObj.paid_by = '<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.actions = {
                            status: 0
                        };
                        rowObj.created_at = moment.utc().format();
                        rowObj.updated_at = moment.utc().format();

                        var rowNode = LEDGER.datatable
                            .row.add(rowObj)
                            .draw()
                            .node();


                        /* add the linker to row and change the color */
                        $(rowNode)
                            .css('background-color', 'rgba(169, 252, 220, .1)')
                            .attr('data-temp', linker);
                    } else {
                        /* request might be completed and we have response from server */

                        /* corresponding row */
                        var rowNode = $(LEDGER.table).find('tbody tr[data-temp="' + linker + '"]');

                        if (kingriders.Utils.isObjectEmpty(response)) {
                            /* we should delete the row */

                            /* check if row data found, we need to rollback the data. otherwise, jsut remove the row */

                            if (rowNode[0].hasAttribute('data-row')) {
                                var orgData = JSON.parse(rowNode.attr('data-row'));
                                LEDGER.datatable.row(rowNode[0]).data(orgData).invalidate();

                                /* remove the cache data */
                                rowNode.removeAttr('data-row');
                            } else {
                                /* remove from datatables */
                                LEDGER.datatable.row(rowNode[0]).remove();

                                /* remove from DOM */
                                rowNode.remove();
                            }

                            rowNode
                                .removeAttr('data-temp')
                                .removeAttr('style');

                            return;
                        }


                        LEDGER.datatable.row(rowNode[0]).data(response).invalidate();


                        /* remove the effect after some time */
                        setTimeout(function() {
                            rowNode
                                .removeAttr('data-temp')
                                .removeAttr('style');
                        }, 2000);

                    }

                    kingriders.Utils.isDebug() && console.log('response', e);
                },
                modal_loaded: function() {
                    if (typeof EXPENSE_MODULE !== "undefined") EXPENSE_MODULE.Utils.reset_page();
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

                edit_module: {
                    current_ledger_id: null,
                    handleClick: function(self) {
                        var rowNode = $(self).parents('tr');
                        if (rowNode.hasClass('child')) { //Check if the current row is a child row
                            rowNode = rowNode.prev(); //If it is, then point to the row before it (its 'parent')
                        }
                        var rowData = LEDGER.datatable.row(rowNode).data();
                        if (typeof LEDGER_EDIT_MODULE !== "undefined") LEDGER_EDIT_MODULE.ledger = rowData;

                        $('[data-edit-ledger]').trigger('click');
                    },
                    submit: function(e) {
                        var response = e.response;
                        var modal = e.modal;
                        var state = e.state; // can be 'beforeSend' or 'completed'
                        var linker = e.linker;
                        if (state == 'beforeSend') {
                            /* request is not completed yet, we have form data available */

                            var ledger_id = response.ledger_id;
                            var amount = parseFloat(response.amount) || 0;

                            /* corresponding row */
                            var row = LEDGER.datatable.row("#" + ledger_id);
                            var rowElem = row.node();
                            var rowObj = JSON.parse(JSON.stringify(row.data()));
                            rowObj.amount = amount;
                            rowObj.cr = rowObj.type === "cr" ? amount : 0;
                            rowObj.dr = rowObj.type === "dr" ? amount : 0;
                            rowElem.setAttribute('data-row', JSON.stringify(rowObj));


                            if (rowObj.type === "cr") rowObj.cr =
                                '<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                            else rowObj.dr =
                                '<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';

                            row.data(rowObj).invalidate();

                            /* add the linker to row and change the color */
                            $(rowElem)
                                .css('background-color', 'rgba(169, 252, 220, .1)')
                                .attr('data-temp', linker);
                        } else {
                            LEDGER.datatable.ajax.reload()
                        }

                        kingriders.Utils.isDebug() && console.log('response', e);
                    },
                    modal_loaded: function() {



                        if (typeof LEDGER_EDIT_MODULE !== "undefined") {

                            /* Update url */
                            if (LEDGER_EDIT_MODULE.ledger) {
                                var url = "{{ route('tenant.admin.ledger.single.edit', '__:param') }}".replace('__:param', LEDGER_EDIT_MODULE.ledger.id);
                                var title = 'Edit Ledger | Administrator';
                                kingriders.Utils.replaceUrl(title, url);
                            }
                            LEDGER_EDIT_MODULE.Utils.reset_page();
                        }
                    },
                },

                events: {
                    handleDeleteClick: function(e, self) {
                        e.preventDefault();

                        var rowNode = $(self).parents('tr');
                        if (rowNode.hasClass('child')) { //Check if the current row is a child row
                            rowNode = rowNode.prev(); //If it is, then point to the row before it (its 'parent')
                        }

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
                                var ledger = LEDGER.datatable.row(rowNode).data();

                                kingriders.Utils.isDebug() && console.log('deleting ledger', ledger);

                                var url = "{{ route('tenant.admin.ledger.delete', '_:param') }}".replace(
                                    "_:param", ledger.id);
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
                                        var errorObj = kingriders.Plugins.KR_AJAX.generateErrors(
                                            jqXHR);

                                        swal.showValidationMessage(errorObj.msg);
                                    });

                            },
                        }).then(function(result) {
                            if (result.value && result.value.status === 1) {

                                /* remove from datatables */
                                LEDGER.datatable.ajax.reload()

                                swal.fire(
                                    'Deleted!',
                                    'Record has been deleted.',
                                    'success'
                                );
                            }
                        });
                    }
                }
            };
        }();

        function cash_filter() {
            if ($('input[name="cash_filter"]').is(':checked')) {
                filter = "all";
            } else {
                filter = "cash";
            }
            kingriders.Utils.updateUrlParem("filter", filter);
            LEDGER.init();
        }
        function initDateFilter() {
            let date = new Date($('input[name="datefilter2"]').val()).format('yyyy-mm-dd');
            let value = kingriders.Utils.getUrlParem('value');
            let type = kingriders.Utils.getUrlParem('type');
            kingriders.Utils.updateUrlParem('value',value ? value:date);
            kingriders.Utils.updateUrlParem('type',type ? type:'day');
        }
        function date_filter() {
            let date = new Date($('input[name="datefilter2"]').val()).format('yyyy-mm-dd');
            kingriders.Utils.buildQueryString([]);
            kingriders.Utils.updateUrlParem('value',date);
            kingriders.Utils.updateUrlParem('type',$('input[name="date_filter_type"]:checked').val());
            cash_filter();
        }
        $('input[name="date_filter_type"]').on('change',function(e) {
            let datefilter = $('#date_filter2');
            datefilter.attr('data-state', e.target.value);
            kingriders.Plugins.refresh_plugins();
            let date = new Date($('input[name="datefilter2"]').val()).format('yyyy-mm-dd');
            kingriders.Utils.updateUrlParem('value',date);
            kingriders.Utils.updateUrlParem('type',e.target.value);
            cash_filter();
        });
        $(function() {
            initDateFilter();
            cash_filter();
            // LEDGER.init();
        });
    </script>
@endsection
