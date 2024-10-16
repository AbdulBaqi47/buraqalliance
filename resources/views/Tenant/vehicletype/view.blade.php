@extends('Tenant.layouts.app')

@section('page_title')
    Clients
@endsection
@section('head')

@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Clients</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            @if ($helper_service->routes->has_access('tenant.admin.clients.add'))
            <button type="button" kr-ajax-size="30%" kr-ajax-submit="CLIENTS.create_submit" kr-ajax-modalclosed="CLIENTS.modal_closed" kr-ajax-contentloaded="CLIENTS.modal_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.clients.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Client
            </button>
            @endif
        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>No. of Bikes</th>
                    <th>Open Balance</th>
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
    var CLIENTS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = CLIENTS.table;

            // begin first table
            CLIENTS.datatable = table.DataTable({
                responsive: true,
                lengthMenu: [5, 10, 25, 50, 100],
                pageLength: 50,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax:"{{route('tenant.admin.clients.data')}}",
                ajax: {
                    url: "{{route('tenant.admin.clients.data')}}",
                    dataSrc: function ( json ) {

                        /* Filter clients that are subclient & having no open balance */
                        var clients = json.data.filter(function(client){

                            /* Invalid row if condition not met */
                            if(client.is_sub==1 && client.open_balance==0)return false;

                            /* Otherwise valid row */
                            return true;
                        });
                        return clients;
                    }
                },
                rowId:'id',
                columns: [
                    {data: 'id', visible:false},
                    {data: 'name', orderable: false, width: "63%"},
                    {data: 'phone', orderable: false, width: "7%"},
                    {data: 'bikes_count', orderable: false, width: "10%"},
                    {data: 'open_balance', orderable: false, width: "15%"},
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
                            // kingriders.Utils.isDebug() && console.log('data',data,type,full,meta);
                            return `
                            <span class="dtr-data d-block text-center">
                                <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                        <i class="la la-ellipsis-h"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @if ($helper_service->routes->has_access('tenant.admin.clients.edit'))
                                        <a href="#" class="dropdown-item" title="Edit Client" onclick="CLIENTS.edit_client(this);return false;">
                                            <i class="la la-edit"></i>
                                            Edit
                                        </a>
                                        @endif
                                        @if ($helper_service->routes->has_access('tenant.admin.clients.viewDetails'))
                                        <a href="{{route('tenant.admin.clients.viewDetails')}}?index=${meta.row+1}" class="dropdown-item">
                                            <i class="la la-info"></i>
                                            Details
                                        </a>
                                        @endif
                                    </div>
                                </span>
                            </span>
                            `;
                        },
                    },
                    {
                        targets: 1,
                        render: function(data, type, full, meta) {
                            if(full.is_sub==1)return '<span class="pl-5 kt-font-bold">'+data+'</span>';
                            return '<span class="kt-font-bold">'+data+'</span>';
                        },
                    },
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
                    /* request is not completed yet, we have form data available */

                    /* need to check if edit */
                    var is_edit=false;
                    if(typeof response.client_id !== "undefined")is_edit=true;

                    var is_walking=false;
                    if(typeof response.walking_customer !== "undefined")is_walking=true;

                    /* we need to create a row dynamically and add to datatables */
                    var tempid = CLIENTS.table.find('tbody tr').length;
                    if(is_edit)tempid=response.client_id;
                    var rowObj=response;
                    rowObj.id=tempid;
                    rowObj.name=response.name;
                    rowObj.email=response.email;
                    rowObj.phone=response.phone;
                    rowObj.trn=response.trn;
                    rowObj.bikes_count=0;
                    rowObj.walking_customer=is_walking?1:0;
                    rowObj.actions={status:0};
                    rowObj.created_at=moment.utc().format();
                    rowObj.updated_at=moment.utc().format();

                    if(is_edit){
                        var row=CLIENTS.datatable.row('#'+tempid);
                        row.data(rowObj).invalidate();
                        var rowNode = row.node();

                        /* Check if child is shown, hide it, (Will help when row is updated) */
                        if (row.child.isShown()) {
                            row.child.hide();
                            $( rowNode ).removeClass('parent');
                        }
                    }
                    else{
                        var rowNode = CLIENTS.datatable
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
                    /* request might be completed and we have response from server */

                    /* corresponding row */
                    var rowNode = CLIENTS.table.find('tbody tr[data-temp="'+linker+'"]');

                    CLIENTS.datatable.row(rowNode[0]).data(response).invalidate();


                    /* remove the effect after some time */
                    setTimeout(function(){
                        rowNode
                        .removeAttr('data-temp')
                        .removeAttr('style');
                    },2000);

                }

                kingriders.Utils.isDebug() && console.log('response', e);
            },
            edit_client:function(self){
                var rowNode = $(self).parents('tr');
                if (rowNode.hasClass('child')) {//Check if the current row is a child row
                    rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                }
                var rowData = CLIENTS.datatable.row(rowNode).data();

                if(typeof CLIENT_MODULE !== "undefined")CLIENT_MODULE.Utils.load_page(rowData);
            },
            modal_loaded:function(){
                if(typeof CLIENT_MODULE !== "undefined"){
                    $(CLIENT_MODULE.container+' form').attr('action', $(CLIENT_MODULE.container+' form').attr('data-add')).find('[name=client_id]').remove();
                    CLIENT_MODULE.Utils.reset_page();
                }
            },
            modal_closed:function(e){
                /* Reset url */
                var MODAL = $(this);
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/clients/add')}}",
                        title:'Create Client | Administrator'
                    });
                }
            },
            recieve_invoice:{
                handle_click:function(self){
                    var rowNode = $(self).parents('tr');
                    if (rowNode.hasClass('child')) {//Check if the current row is a child row
                        rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                    }
                    var rowData = CLIENTS.datatable.row(rowNode).data();
                    if(typeof CLIENT_RECEIVEINVOICE_MODULE !== "undefined")CLIENT_RECEIVEINVOICE_MODULE.client=rowData;

                    $('[data-recieve-invoice').trigger('click');

                },
                modal_loaded:function(){
                    if(typeof CLIENT_RECEIVEINVOICE_MODULE !== "undefined")CLIENT_RECEIVEINVOICE_MODULE.Utils.reset_page();
                },
            }
        };
    }();

    $(function(){


        CLIENTS.init();
    });
</script>


@endsection
