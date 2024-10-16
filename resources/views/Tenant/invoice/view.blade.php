@extends('Tenant.layouts.app')

@section('page_title')
    Invoices
@endsection
@section('head')

@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5 ledger__container">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Invoices</h3>
        </div>
        <div class="kt-portlet__head-toolbar">

            @if ($helper_service->routes->has_access('tenant.admin.invoices.create'))
            <button kr-ajax-modal-type="full" data-backdrop="static" type="button" class="btn btn-info btn-elevate btn-square m-1" kr-ajax-modalclosed="INVOICES.modal_closed" kr-ajax-submit="INVOICES.create_submit" kr-ajax-contentloaded="Function()" kr-ajax="{{route('tenant.admin.invoices.create')}}">
                <i class="flaticon2-plus-1"></i>
                New Invoice
            </button>
            @endif

        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped" id="datatable">
            <thead>
                <tr>
                    <th></th>
                    <th>#</th>
                    <th>Client</th>
                    <th>Month</th>
                    <th>Total</th>
                    <th>Ref</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

@endsection


@section('foot')


{{------------------------------------------------------------------------------
                            SCRIPTS (use in current page)
--------------------------------------------------------------------------------}}

<script type="text/javascript">

    var INVOICES = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = INVOICES.table;

            // begin first table
            INVOICES.datatable = table.DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: 'rfti',
                serverSide: false,
                deferRender: true,
                destroy:true,
                ajax: {
                    "url": "{{ route('tenant.admin.invoices.data') }}"
                },
                rowId:'_id',
                columns: [
                    {data: '_id', visible: false},
                    {data: 'display_name', width: '10%'},
                    {data: 'client.name', width: '10%'},
                    {data: 'month', width: '10%'},
                    {data: 'total', width: '10%'},
                    {data: 'payment_refs', width: '40%'},
                    {data: null, width: '10%'}, // Status
                    {data: 'actions', width: '10%'},
                ],
                ordering:false,
                columnDefs: [
                    {
                        targets: -1,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return `

                                <div class="d-flex flex-wrap justify-content-between">
                                    
                                    @if ($helper_service->routes->has_access('tenant.admin.invoices.single.edit'))
                                        <div class="flex-grow-1">    
                                            <a href="#" class="btn btn-sm btn-outline-focus btn-elevate btn-square w-100 py-1 px-0" kr-ajax-modal-type="full" kr-ajax-contentloaded="Function()" kr-ajax-submit="INVOICES.create_submit" kr-ajax-modalclosed="INVOICES.modal_closed" kr-ajax="${`{{ route('tenant.admin.invoices.single.edit', '_:param') }}`.replace('_:param', full.id)}">
                                                <i class="flaticon-edit"></i>
                                            </a>
                                        </div>
                                    @endif

                                    @if ($helper_service->routes->has_access('tenant.admin.invoices.delete'))
                                        <div class="flex-grow-1">
                                            <a href="#" class="btn btn-sm btn-outline-danger btn-elevate btn-square w-100 py-1 px-0" title="Delete Invoice" onclick="INVOICES.events.handleDeleteClick(event, this);return false;">
                                                <i class="la la-trash"></i>
                                            </a>
                                        </div>
                                    @endif


                                    @if ($helper_service->helper->isSuperUser())
                                        <div class="w-100">
                                            <a href="#" class="kt-link mt-2 w-100 text-center" title="Print Job" onclick="JOBS.events.print_invoice(this);return false;">
                                                <i class="flaticon2-print"></i>
                                                Print
                                            </a>
                                        </div>
                                    @endif    

                                </div>

                            `;
                        },
                    },
                    {
                        targets: 3, // Month
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return moment(data).format('MMMM YYYY');
                        },
                    },
                    {
                        targets: 5, // payment_refs
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data || data.length === 0)return '';
                            return `
                                <ul class="list-group list-group-flush">
                                    ${data.map(transaction_ledger => {
                                        // Render each linked payment ref
                                        return `
                                            <li class="list-group-item p-0">
                                                ${transaction_ledger.payables.map(payable => {
                                                    // Render payables against ledger
                                                    var title = `<span class="text-${payable.status === 'pending' ? 'warning' : 'success'}">${payable.title} [${!!payable.amount ? payable.amount : payable.real_amount}]</span>`;
                                                    return `
                                                        <a href="${"{{ route('accounts.transaction.receivables') }}"}">${title}</a>
                                                    `
                                                }).join('')}
                                            </li>
                                        `
                                    }).join('')}
                                </ul>
                            `;
                        },
                    },

                    {
                        targets: 6, // status
                        render: function(data, type, full, meta) {
                            // Construct status
                            //   : pending = if no payment is paid
                            //   : partial = if some payment is paid
                            //   : paid = if all payment is paid
                            var status = 'pending';

                            // Get all refs mapped to account transactions
                            var paymentRefs = full
                            .payment_refs
                            .flatMap(ref => ref.payables.map(payable => payable) );

                            if(paymentRefs.some(item => item.status === 'paid')){
                                status = 'partial';
                            }

                            if(paymentRefs.every(item => item.status === 'paid')){
                                status = 'paid';
                            }

                            if(paymentRefs.length === 0){
                                // No refs found
                                status = 'na';
                            }

                            var statusData = {
                                'na': {'title': 'Unavailable', 'class': ' kt-badge--danger'},
                                'pending': {'title': 'Pending', 'class': ' kt-badge--metal'},
                                'partial': {'title': 'Partially Paid', 'class': ' kt-badge--warning'},
                                'paid': {'title': 'Paid', 'class': ' kt-badge--success'},
                            };
                            if (typeof statusData[status] === 'undefined') {
                                return '';
                            }
                            return '<span class="kt-badge ' + statusData[status].class + ' kt-badge--inline kt-badge--pill">' + statusData[status].title + '</span>';

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

        };

        /* page settings */
        return {
            table:$('#datatable'),
            datatable:null,
            init:function(){
                init_table();
            },
            events:{
                allow_closing: false,
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
                            var transaction = INVOICES.datatable.row(rowNode).data();

                            kingriders.Utils.isDebug() && console.log('deleting transaction', transaction);

                            var url = "{{ route('tenant.admin.invoices.delete', '_:param') }}".replace("_:param", transaction.id);
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
                            INVOICES.datatable.row(rowNode[0]).remove();

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
            },
            create_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){
                    /* request is not completed yet, we have form data available */

                    if(response.status==1){
                        /* we need to create  a row dynamically and add to datatables */
                        var tempid = $(INVOICES.table).find('tbody tr').length;


                        var rowObj={};
                        rowObj._id=tempid;
                        rowObj.client={name:'<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>'};
                        rowObj.month='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.total='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.balance='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.status='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.actions={status:0};
                        rowObj.created_at=moment.utc().format();
                        rowObj.updated_at=moment.utc().format();

                        var rowNode = INVOICES.datatable
                        .row.add( rowObj )
                        .draw()
                        .node();

                        /* add the linker to row and change the color */
                        $( rowNode )
                        .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                        .attr('data-temp', linker);
                    }
                }
                else{
                    /* request might be completed and we have response from server */

                    if(response.status==1){
                        /* show toast of success (on adding) */
                        toastr.success("Invoice <b>"+response.invoice_id+"</b> is created successfully", "Success!");
                    }
                    else if(response.status==2){
                        /* show toast of success (on updating) */
                        toastr.success("Invoice <b>"+response.invoice_id+"</b> is updated successfully", "Success!");
                    }

                    INVOICES.datatable.ajax.reload();

                }
            },
            modal_loaded:function(){
                if(typeof ADDON_SETTING_MODULE !== "undefined")ADDON_SETTING_MODULE.Utils.reset_page(true);
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

        };
    }();


    $(function(){
        INVOICES.init();
    })

</script>


@endsection
