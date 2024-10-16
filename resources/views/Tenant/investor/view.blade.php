@extends('Tenant.layouts.app')

@section('page_title')
    Investors
@endsection
@section('head')

@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Investors</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            @if ($helper_service->routes->has_access('tenant.admin.investors.add'))
            <button type="button" kr-ajax-size="30%" kr-ajax-block-page-when-processing kr-ajax-submit="INVESTORS.create_submit" kr-ajax-modalclosed="INVESTORS.modal_closed" kr-ajax-contentloaded="INVESTORS.modal_loaded" kr-ajax="{{route('tenant.admin.investors.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Investor
            </button>
            @endif
        </div>
    </div>

    <div class="kt-portlet__body ledger__container">

        <table class="table table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>No. of Active Booking</th>
                    <th>No. of Vehicles</th>
                    <th>Manages to</th>
                    <th></th>
                    <th>Actions</th>
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
    var INVESTORS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = INVESTORS.table;

            // begin first table
            INVESTORS.datatable = table.DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: 'rfti',
                serverSide: false,
                deferRender: true,
                destroy:true,
                ajax:"{{route('tenant.admin.investors.data')}}",
                rowId:'id',
                columns: [
                    {data: 'id', visible:false},
                    {data: 'name', orderable: false, width: "30%"},
                    {data: 'email', orderable: false, width: "20%"},
                    {data: 'phone', orderable: false, width: "10%"},
                    {data: 'bookings_count', orderable: false, width: "5%"},
                    {data: 'vehicle_count', orderable: false, width: "5%"},
                    {data: 'manages', orderable: false, width: "25%"},
                    {data: 'created_at', visible:false},
                    {data: 'actions', width: "5%"},
                ],
                ordering:false,
                columnDefs: [
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            if(data.status==0) return '';
                            var index=0;
                            return `
                            <span class="dtr-data d-block text-center">
                                <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                        <i class="la la-ellipsis-h"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">

                                        @if ($helper_service->routes->has_access('tenant.admin.investors.single.edit'))

                                        <a href="#" class="dropdown-item" title="View" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="INVESTORS.investorView.modal_closed" kr-ajax-submit="INVESTORS.onSubmitEditModal" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.investors.single.edit', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-pencil"></i>
                                            Edit
                                        </a>

                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.investors.single.view'))

                                        <a href="#" class="dropdown-item" title="View" kr-ajax-block-page-when-processing kr-ajax-size="70%" kr-ajax-modalclosed="INVESTORS.investorView.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.investors.single.view', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-eye"></i>
                                            Details
                                        </a>

                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.investors.change_manager'))

                                        <a href="#" class="dropdown-item" title="Change who's data this investor can manage to" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="INVESTORS.investorView.modal_closed" kr-ajax-submit="INVESTORS.change_manager_submit" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.investors.change_manager', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-refresh"></i>
                                            Change manages to
                                        </a>

                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.vehicleledger.investor.view'))

                                        <a href="${"{{ route('tenant.admin.vehicleledger.investor.view', '_:param') }}".replace('_:param', full.id)}"class="dropdown-item">

                                            <i class="la la-balance-scale"></i>
                                            Investor statement
                                        </a>

                                        @endif

                                    </div>
                                </span>
                            </span>
                            `;
                        },
                    },
                    {
                        targets: 6,
                        render: function(data, type, full, meta) {
                            if(!!data && data.length > 0){

                                return `
                                    <ul class="list-group list-group-flush">
                                        ${data.map((item, index) => {
                                            return `<li class="list-group-item p-0">
                                               ${data.length>1 ? `<b>${index+1})</b>` : ''} ${item.name}
                                            </li>`
                                        }).join('')}
                                    </ul>
                                `;

                            }

                            return '';
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
            create_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){
                    /* request is not completed yet, we have form data available */

                    /* need to check if edit */
                    var is_edit=false;
                    if(typeof response.investor_id !== "undefined")is_edit=true;

                    /* we need to create a row dynamically and add to datatables */
                    var tempid = INVESTORS.table.find('tbody tr').length;
                    if(is_edit)tempid=response.client_id;
                    var rowObj=response;
                    rowObj.id=tempid;
                    rowObj.name=response.name;
                    rowObj.email=response.email;
                    rowObj.phone=response.phone;
                    rowObj.notes=response.notes;
                    rowObj.bookings_count=0;
                    rowObj.vehicle_count=0;
                    rowObj.manages=null;
                    rowObj.actions={status:0};
                    rowObj.created_at=moment.utc().format();
                    rowObj.updated_at=moment.utc().format();

                    if(is_edit){
                        var row=INVESTORS.datatable.row('#'+tempid);
                        row.data(rowObj).invalidate();
                        var rowNode = row.node();

                        /* Check if child is shown, hide it, (Will help when row is updated) */
                        if (row.child.isShown()) {
                            row.child.hide();
                            $( rowNode ).removeClass('parent');
                        }
                    }
                    else{
                        var rowNode = INVESTORS.datatable
                        .row.add( rowObj )
                        .draw()
                        .node();
                    }


                    /* add the linker to row and change the color */
                    $( rowNode )
                    .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                    .attr('data-temp', linker);
                }
                else{
                    INVESTORS.datatable.ajax.reload();

                }

                kingriders.Utils.isDebug() && console.log('response', e);
            },
            change_manager_submit(e){
                if(e.state === 'completed') INVESTORS.datatable.ajax.reload();
            },
            modal_loaded:function(){
                if(typeof INVESTOR_MODULE !== "undefined"){
                    $(INVESTOR_MODULE.container+' form').attr('action', $(INVESTOR_MODULE.container+' form').attr('data-add')).find('[name=investor_id]').remove();
                    INVESTOR_MODULE.Utils.reset_page();
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
            onSubmitEditModal(e){
                if(e.state === 'completed'){
                    /* show toast of success (on adding) */
                    toastr.info("Investor is updated!", "Success!");

                    INVESTORS.datatable.ajax.reload();
                }
            },
            investorView:{
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
                }
            },
        };
    }();

    $(function(){


        INVESTORS.init();
    });
</script>


@endsection
