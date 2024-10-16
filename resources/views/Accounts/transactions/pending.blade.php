@extends('Tenant.layouts.app')

@section('page_title')
{{ $state === "cr" ? "Receivable" : "Payable" }} Transactions {{ $state === "cr" ? "" : " & Cheques" }}
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
            <h3 class="kt-portlet__head-title">{{ $state === "cr" ? "Receivable" : "Payable" }} Transactions {{ $state === "cr" ? "" : " & Cheques" }}</h3>
        </div>
        <div class="kt-portlet__head-toolbar ledger__fields my-2">
            <div class="d-flex flex-column">
                <div class="d-flex flex-column justify-content-between">
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success m-0 mb-2">
                        <input type="checkbox" onchange="PENDING_TRANSACTIONS.handleCheckBoxChange(event, this);" name="show_paid_cheques"> Show Paid Cheques
                        <span></span>
                    </label>
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success m-0">
                        <input type="checkbox" onchange="PENDING_TRANSACTIONS.handleCheckBoxChange(event, this);" name="show_pending_installments"> Show Pending Installments
                        <span></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="kt-portlet__head-toolbar">
            @if ($state !== "cr" && $helper_service->routes->has_access('tenant.admin.cheques.add'))
            <button class="btn btn-info btn-elevate btn-square" title="Assign" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="PENDING_TRANSACTIONS.modal_closed" kr-ajax-submit="PENDING_TRANSACTIONS.create_submit" kr-ajax-contentloaded="Function()" kr-ajax="{{ route('tenant.admin.cheques.add') }}">
                <i class="la la-plus"></i>
                Create Cheque
            </button>
            @endif
        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Withdrawal Date</th>
                    <th>Transaction ID</th>
                    <th>Account</th>
                    <th>Transaction Detail</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
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
    var PENDING_TRANSACTIONS= function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table =PENDING_TRANSACTIONS.table;
            var q = PENDING_TRANSACTIONS.Utils.buildQuery();
            // begin first table
           PENDING_TRANSACTIONS.datatable = table.DataTable({
                // data:chunk,
                lengthMenu: [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"] ],
                pageLength: 10,
                responsive: true,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                destroy:true,
                ajax: {
                    url:"{{route('module.accounts.transactions.pending.data')}}",
                    data: {
                        state: '{{$state}}',
                        paid_cheques: q.paid_cheques ? 1 : 0,
                        pending_installments: q.pending_installments ? 1 : 0
                    }
                },
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false},
                    {data: 'dt_created_at', orderable: false, width: "20%"},
                    {data: 'id', visible:true, width: "10%"},
                    {data: 'account', orderable: false, width: "10%"},
                    {data: 'dt_details', orderable: false, width: "35%"},
                    {data: 'amount', orderable: false, width: "10%"},
                    {data: 'status', orderable: false, width: "5%"},
                    {data: 'time', visible:false},
                    {data: 'actions', width: "10%"},
                ],
                order:[
                    [7, 'asc']
                ],
                columnDefs: [
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';

                            // Runtime Installment
                            if(typeof full.additional_details !== "undefined" && typeof full.additional_details.pending_installment !== "undefined"){
                                return '';
                            }

                            return `
                            ${full.status === "pending" ? `
                            @if ($helper_service->routes->has_access('accounts.transaction.pending.pay'))

                            <a class="btn btn-sm btn-label-brand" href="#" kr-ajax-block-page-when-processing kr-ajax-size="70%" kr-ajax-modalclosed="PENDING_TRANSACTIONS.modal_closed" kr-ajax-submit="PENDING_TRANSACTIONS.create_submit" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('accounts.transaction.pending.pay', '_:param')}}?state={{$state}}".replace('_:param', full.id)}">
                                {{ $state === "cr" ? "Receive" : "Pay" }} Amount
                            </a>

                            @endif
                            ` : ``}
                            @if ($helper_service->routes->has_access('module.accounts.transactions.pending.edit'))

                            <a class="btn btn-sm mt-2 btn-label-danger" href="#" kr-ajax-block-page-when-processing kr-ajax-size="70%" kr-ajax-modalclosed="PENDING_TRANSACTIONS.modal_closed" kr-ajax-submit="PENDING_TRANSACTIONS.create_submit" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('module.accounts.transactions.pending.edit', '_:param')}}".replace('_:param', full.id)}">
                                Edit
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


                            // Runtime Installment
                            if(typeof full.additional_details !== "undefined" && typeof full.additional_details.pending_installment !== "undefined"){

                                return `
                                    <div class="d-flex flex-column">
                                        <span class="small">InsCode#${full.additional_details.installment_code}</span>
                                    </div>
                                `;
                            }

                            var suffixHtmlArr = [];
                            if(!!full.additional_details){

                                if( typeof full.additional_details.is_cheque !== "undefined" && full.additional_details.is_cheque === true){
                                    var chequeNo = typeof full.additional_details.cheque_number !== "undefined" && !!full.additional_details.cheque_number ? `#${full.additional_details.cheque_number}` : '';
                                    suffixHtmlArr.push( `<span class="kt-font-success kt-font-bolder small">Cheque${chequeNo}</span>` );
                                }

                                if(!!full.additional_details.cheque_beneficiary){
                                    suffixHtmlArr.push(`
                                        <p class="m-0">
                                            <span class="kt-font-boldest small">Beneficiary:</span>
                                            <span class="kt-font-bold small">${full.additional_details.cheque_beneficiary}</span>
                                        </p>
                                    `);
                                }

                                // Render installment code + number if found
                                if(!!full.additional_details.installment_code){

                                    suffixHtmlArr.push(`
                                        <span class="small">InsCode#${full.additional_details.installment_code}</span>
                                    `);
                                }
                            }

                            var suffixHtml = suffixHtmlArr.length > 0 ? `<div class="d-flex flex-column">${suffixHtmlArr.join('')}</div>` : '';

                            return `${data} ${suffixHtml}`;
                        },
                    },
                    {
                        targets: 3,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return data.title;
                        },
                    },
                    {
                        targets: 5,
                        orderable: false,
                        render: function(data, type, full, meta) {

                            if(typeof data == "undefined" || !data){
                                if(!!full.real_amount)return `<span class="kt-font-warning">${full.real_amount}</span>`;

                                return '';
                            }
                            return data;
                        },
                    },
                    {
                        targets: 6,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data) return '';

                            // Runtime Installment
                            if(typeof full.additional_details !== "undefined" && typeof full.additional_details.pending_installment !== "undefined"){
                                return `<span class="kt-badge font-weight-bold text-capitalize kt-badge--info kt-badge--inline kt-badge--pill">Installment</span>`;
                            }

                            return `<span class="kt-badge font-weight-bold text-capitalize kt-badge--${data === 'paid' ? 'success' : 'warning'} kt-badge--inline kt-badge--pill">${data}</span>`;
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
            handleCheckBoxChange(e, target){
                /* update the url before initiating */
                PENDING_TRANSACTIONS.Utils.update_url();

                // /* we need to update 'data-default' too soo it wont reset */
                // $('.ledger__fields [name="month"]').attr('data-default', $('.ledger__fields [name="month"]').val());

                /* init the table */
                PENDING_TRANSACTIONS.init();
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
                else{

                    /* unblock page */
                    KTApp.unblockPage();

                    PENDING_TRANSACTIONS.datatable.ajax.reload();
                }

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
            Utils:{
                buildQuery:function(){
                    /* Use this to fetch input, like show_paid_cheques checkbox */

                    var paid_cheques = $('[name="show_paid_cheques"]').is(':checked');
                    var pending_installments = $('[name="show_pending_installments"]').is(':checked');

                    return {paid_cheques, pending_installments};
                },
                update_url:function(){
                    /* add employee id and month as query string in url, so we can save the state */
                    var q = PENDING_TRANSACTIONS.Utils.buildQuery();

                    /* make data to append on url */
                    var data = {
                        paid_cheques:q.paid_cheques?1:0,
                        pending_installments:q.pending_installments?1:0
                    }

                    /* update URL */
                    kingriders.Utils.buildQueryString(data);

                },
                update_input:function(q){
                    /* Use this to update the employee_id and month */
                    $('[name="show_paid_cheques"]').prop('checked', q.paid_cheques == 1);
                    $('[name="show_pending_installments"]').prop('checked', q.pending_installments == 1);

                },
                fetchQuery:function(){
                    /* we will fetch month and range from url */
                    var paid_cheques = kingriders.Utils.fetchQueryString("paid_cheques")||null;
                    var pending_installments = kingriders.Utils.fetchQueryString("pending_installments")||null;

                    return { paid_cheques, pending_installments };
                }
            },
        };
    }();

    $(function(){
        // Update DOM with query param
        let q = PENDING_TRANSACTIONS.Utils.fetchQuery();
        PENDING_TRANSACTIONS.Utils.update_input(q);

        // Init data
        PENDING_TRANSACTIONS.init();
    });
</script>


@endsection
