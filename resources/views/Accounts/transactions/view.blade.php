@extends('Tenant.layouts.app')

@section('page_title')
    Account Transaction
@endsection
@section('head')

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
        letter-spacing: .6px;
        color: #7e9bff;
        position: relative;
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
</style>
@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Account Transactions | {{$account->title}}</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            @if ($helper_service->routes->has_access('module.accounts.transactions.add'))
            <button type="button" kr-ajax-size="30%" kr-ajax-submit="TRANSACTIONS.create_submit" kr-ajax-contentloaded="TRANSACTIONS.modal_loaded" kr-ajax-preload kr-ajax="{{route('module.accounts.transactions.add', $account->id)}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Add Transaction
            </button>
            @endif

            @if ($helper_service->routes->has_access('module.accounts.transactions.edit'))
                <button type="button" data-edit-transaction hidden kr-ajax-submit="TRANSACTIONS.edit_module.submit" kr-ajax-size="30%" kr-ajax-contentloaded="TRANSACTIONS.edit_module.modal_loaded" kr-ajax-preload kr-ajax="{{ route('module.accounts.transactions.edit', $account->id) }}" class="btn btn-info btn-elevate btn-square">
                    <i class="fa fa-pencil"></i>
                    Edit Transaction
                </button>
            @endif
        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Created at</th>
                    <th>Transaction ID</th>
                    <th>Transaction Detail</th>
                    <th>Cash In</th>
                    <th>Cash Out</th>
                    <th>Balance</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {{-- Show 1st 10 records --}}
                {{-- @foreach ($jobs_chunk as $job)
                <tr class="@if($loop->even) even @else odd @endif">
                    <td>{{$job->id}}</td>
                    <td>PC10</td>
                    <td>88886</td>
                    <td>January 19, 2021</td>
                    <td>
                        <span class="kt-badge  kt-badge--warning kt-badge--inline kt-badge--pill">In Progress</span>
                    </td>
                    <td>309.75</td>
                    <td>{{$job->created_at}}</td>
                    <td>
                        {status:1}
                    </td>
                </tr>
                @endforeach --}}
            </tbody>
        </table>

    </div>
</div>

<!--end::Portlet-->




@endsection


@section('foot')


{{------------------------------------------------------------------------------
                            SCRIPTS (use in current page)
--------------------------------------------------------------------------------}}

<script type="text/javascript">
    var TRANSACTIONS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = TRANSACTIONS.table;

            // var first_chunk = { !! $jobs_chunk !!};
            // var chunk = first_chunk.map(function(el) {
            //     var o = Object.assign({}, el);
            //     o.actions = {status:1};
            //     o.date = moment(el.data).format('MMMM DD, YYYY');
            //     return o;
            // });

            // begin first table
            TRANSACTIONS.datatable = table.DataTable({
                // data:chunk,
                lengthMenu: [[10, 25, 50, 100, 1000, '-1'], [10, 25, 50, 100, 1000, 'All']],
                pageLength: 10,
                responsive: true,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                destroy:true,
                // deferLoading: { !! $job_count !!},
                ajax: {
                    url:"{{route('module.accounts.transactions.data', $account->_id)}}",
                    // dataSrc: function(json) {
                    //     return json.data.filter(function(item) {
                    //         return chunk.findIndex(function(x){return x._id==item._id})==-1;
                    //     });
                    // }
                },
                rowId:'_id',
                createdRow(row, data, dataIndex){
                    $(row).attr('data-activity-id',data.id);
                    $(row).attr('data-activity-modal','App\\Accounts\\Models\\Account_transaction');
                    $(row).attr('data-activity-category','accounts');
                },
                columns: [
                    {data: '_id', visible:false},
                    {data: 'dt_created_at', orderable: false, width: "20%"},
                    {data: 'id', orderable:true, width: "10%"},
                    {data: 'dt_details', orderable: false, width: "35%"},
                    {data: 'dt_cr', orderable: false, width: "10%"},
                    {data: 'dt_dr', orderable: false, width: "10%"},
                    {data: 'dt_balance', orderable: false, width: "10%"},
                    {data: 'actions', width: "5%"},
                ],
                order:[],
                ordering:false,
                columnDefs: [
                    {
                        targets: -1,
                        title: '',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';

                            // Disable buttons on import sheets
                            if(full.tag === "transaction_ledger") return '';

                            return `
                                @if ($helper_service->routes->has_access('module.accounts.transactions.single.edit'))
                                <a href="#" class="btn btn-sm btn-outline-primary btn-icon btn-icon-md" title="Edit Transaction" onclick="TRANSACTIONS.edit_module.handleClick(this);return false;">
                                    <i class="la la-pencil"></i>
                                </a>
                                @endif

                                @if ($helper_service->routes->has_access('module.accounts.transactions.delete'))
                                <a href="#" class="btn btn-sm btn-outline-danger btn-icon btn-icon-md" title="Delete Transaction" onclick="TRANSACTIONS.events.handleDeleteClick(event, this);return false;">
                                    <i class="la la-trash"></i>
                                </a>
                                @endif
                            `;
                        },
                    },
                    {
                        targets: 2,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return full.additional_details?.is_cheque ? `
                                ${data} <span class="badge badge-info">Cheque</span>
                            `: data;
                        },
                    }
                ],
            });

        };

        /* page settings */
        return {
            table:$('#datatable'),
            datatable:null,
            init:function(){
                init_table();
            },
            create_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){

                    /* just block the page for now */
                    KTApp.blockPage({
                        opacity:.2,
                        size:"sm",
                        type: 'v2',
                        state: 'primary',
                        message:"Please wait"
                    });
                }
                else if(state=="error"){

                    /* unblock page */
                    KTApp.unblockPage();
                }
                else{

                    /* unblock page */
                    KTApp.unblockPage();

                    /* reload datatables */
                    TRANSACTIONS.init();

                }

                kingriders.Utils.isDebug() && console.log('response', e);
            },
            modal_loaded:function(){
                $('#kt-portlet__create-transaction form').attr('action', $('#kt-portlet__create-transaction form').attr('data-add')).find('[name=bike_id]').remove();
                if(typeof TRANSACTION_MODULE !== "undefined")TRANSACTION_MODULE.Utils.reset_page();
            },

            edit_module: {
                current_id: null,
                handleClick: function(self) {
                    var rowNode = $(self).parents('tr');
                    if (rowNode.hasClass('child')) { //Check if the current row is a child row
                        rowNode = rowNode.prev(); //If it is, then point to the row before it (its 'parent')
                    }
                    var rowData = TRANSACTIONS.datatable.row(rowNode).data();
                    if (typeof TRANSACTION_EDIT_MODULE !== "undefined") TRANSACTION_EDIT_MODULE.transaction = rowData;

                    $('[data-edit-transaction]').trigger('click');
                },
                submit: function(e) {
                    var response = e.response;
                    var modal = e.modal;
                    var state = e.state; // can be 'beforeSend' or 'completed'
                    var linker = e.linker;
                    if(state=='beforeSend'){

                        /* just block the page for now */
                        KTApp.blockPage({
                            opacity:.2,
                            size:"sm",
                            type: 'v2',
                            state: 'primary',
                            message:"Please wait"
                        });
                    }
                    else if(state=="error"){

                        /* unblock page */
                        KTApp.unblockPage();
                    }
                    else{

                        /* unblock page */
                        KTApp.unblockPage();

                        /* reload datatables */
                        TRANSACTIONS.init();

                    }

                    kingriders.Utils.isDebug() && console.log('response', e);
                },
                modal_loaded: function() {



                    if (typeof TRANSACTION_EDIT_MODULE !== "undefined") {

                        /* Update url */
                        if (TRANSACTION_EDIT_MODULE.ledger) {
                            var url = "{{ url('admin/transactions') }}/" + TRANSACTION_EDIT_MODULE.transaction.id + "/edit";
                            var title = 'Edit Transaction | Administrator';
                            kingriders.Utils.replaceUrl(title, url);
                        }
                        TRANSACTION_EDIT_MODULE.Utils.reset_page();
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
                            var transaction = TRANSACTIONS.datatable.row(rowNode).data();

                            kingriders.Utils.isDebug() && console.log('deleting transaction', transaction);

                            var url = "{{ route('module.accounts.transactions.delete', '_:param') }}".replace("_:param", transaction.id);
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

                            /* remove from datatables */
                            TRANSACTIONS.datatable.row(rowNode[0]).remove();

                            /* remove from DOM */
                            rowNode.remove();

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

    $(function(){


        TRANSACTIONS.init();
    });
</script>


@endsection
