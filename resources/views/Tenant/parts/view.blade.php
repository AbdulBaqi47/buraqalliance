@extends('Tenant.layouts.app')

@section('page_title')
    Parts
@endsection
@section('head')
<style>
    .description-title{
        font-weight: 500;
    }
    .description-subtitle{
        font-size: 12px;
        color: #909090;
        display: block;
        line-height: 10px;
    }
</style>
@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Parts</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            @if ($helper_service->routes->has_access('tenant.admin.parts.add'))
            <button type="button" kr-ajax-submit="PARTS.create_submit" kr-ajax-size="30%" kr-ajax-modalclosed="PARTS.modal_closed" kr-ajax-contentloaded="PARTS.modal_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.parts.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Part
            </button>
            @endif

            @if ($helper_service->routes->has_access('tenant.admin.parts.inventory.add'))
            <button type="button" data-create-inventory hidden kr-ajax-submit="PARTS.inventory_module.create_submit" kr-ajax-size="30%" kr-ajax-contentloaded="PARTS.inventory_module.modal_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.parts.inventory.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Part Inventory
            </button>
            @endif
        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Detail</th>
                    <th>Sale Price</th>
                    <th>Stock Left</th>
                    <th>Low Inventory Threshold</th>
                    <th>Rack Number</th>
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
    var PARTS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = PARTS.table;

            // begin first table
            PARTS.datatable = table.DataTable({
                responsive: true,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax: "{{route('tenant.admin.parts.data')}}",
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false},
                    {data: 'code', orderable: false, width:'10%'},
                    {data: 'detail', orderable: false, width:'31%'},
                    {data: 'sale_price', orderable: false, width:'9%'},
                    {data: 'stock_left', orderable: false, width:'10%'},
                    {
                        data: null, // low_inventory_qty
                        orderable: false,
                        width:'10%',
                        render: function(data, type, full, meta) {
                            if(typeof full.low_inventory_qty == "undefined" || data.low_inventory_qty === null)return '';
                            return full.low_inventory_qty;
                        }

                    },
                    {
                        data: null, // Rack
                        orderable: false,
                        width:'10%',
                        render: function(data, type, full, meta) {
                            if(typeof full.rack == "undefined" || data.rack === null)return '';
                            return full.rack;
                        }

                    },
                    {data: 'created_at', visible:false},
                    {data: 'actions', width:'20%'},
                ],
                order: [[7, 'desc']],
                columnDefs: [
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            if(data.status==0) return '';
                            return `
                            @if ($helper_service->routes->has_access('tenant.admin.parts.inventory.add'))
                            <a href="#" class="btn btn-outline-success btn-elevate btn-square btn-sm py-0 px-2" onclick="PARTS.inventory_module.handle_click(this);return false;">
                                <i class="flaticon-add"></i>
                                Add Inventory
                            </a>
                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.parts.edit'))
                            <a href="#" class="btn btn-outline-info btn-elevate btn-square btn-sm py-0 px-2" onclick="PARTS.edit(this);return false;">
                                <i class="flaticon-eye"></i>
                                Edit
                            </a>
                            @endif`;
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
                    if(typeof response.part_id !== "undefined")is_edit=true;

                    /* we need to create  a row dynamically and add to datatables */
                    var tempid = PARTS.table.find('tbody tr').length;
                    if(is_edit)tempid=response.part_id;
                    var rowObj={};
                    rowObj._id=tempid;
                    rowObj.code='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.detail='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                    rowObj.sale_price='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.stock_left='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                    rowObj.low_inventory_qty='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                    rowObj.actions={status:0};
                    rowObj.created_at=moment.utc().format();
                    rowObj.updated_at=moment.utc().format();




                    if(is_edit){

                        var row=PARTS.datatable.row('#'+tempid);

                        /* before updating the row, we should save the current data, so we can roll back */
                        var rowElem = row.node();
                        rowElem.setAttribute('data-row', JSON.stringify(row.data()));


                        row.data(rowObj).invalidate();
                        var rowNode = row.node();

                        /* Check if child is shown, hide it, (Will help when row is updated) */
                        if (row.child.isShown()) {
                            row.child.hide();
                            $( rowNode ).removeClass('parent');
                        }
                    }
                    else{
                        var rowNode = PARTS.datatable
                        .row.add( rowObj )
                        .draw()
                        .node();
                    }


                    /* add the linker to row and change the color */
                    $( rowNode )
                    .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                    .attr('data-temp', linker);
                }
                else if(state=="error"){
                    /* it seems server respond with some errors, we need to delete the newly added row */

                    /* corresponding row */
                    var rowNode = PARTS.table.find('tbody tr[data-temp="'+linker+'"]');

                    /* check if row data found, we need to rollback the data. otherwise, jsut remove the row */

                    if(rowNode[0].hasAttribute('data-row')){
                        var orgData = JSON.parse(rowNode.attr('data-row'));
                        PARTS.datatable.row(rowNode[0]).data(orgData).invalidate();
                    }
                    else{
                        /* remove from datatables */
                        PARTS.datatable.row(rowNode[0]).remove();

                        /* remove from DOM */
                        rowNode.remove();
                    }

                    /* remove the cache data */
                    rowNode.removeAttr('data-row');

                    rowNode
                    .removeAttr('data-temp')
                    .removeAttr('style');
                }
                else{
                    /* request might be completed and we have response from server */

                    /* corresponding row */
                    var rowNode = PARTS.table.find('tbody tr[data-temp="'+linker+'"]');

                    PARTS.datatable.row(rowNode[0]).data(response).invalidate();

                    /* remove the cache data */
                    rowNode.removeAttr('data-row');


                    /* remove the effect after some time */
                    setTimeout(function(){
                        rowNode
                        .removeAttr('data-temp')
                        .removeAttr('style');
                    },2000);

                }

                kingriders.Utils.isDebug() && console.log('response', e);
            },
            edit:function(self){
                var rowNode = $(self).parents('tr');
                if (rowNode.hasClass('child')) {//Check if the current row is a child row
                    rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                }
                var rowData = PARTS.datatable.row(rowNode).data();

                if(typeof PART_MODULE !== "undefined")PART_MODULE.Utils.load_page(rowData);
            },
            modal_loaded:function(){
                $('#kt-portlet__create-part form').attr('action', $('#kt-portlet__create-part form').attr('data-add')).find('[name=part_id]').remove();
                if(typeof PART_MODULE !== "undefined")PART_MODULE.Utils.reset_page();
            },
            modal_closed:function(e){
                /* Reset url */
                var MODAL = $(this);
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/parts/add')}}",
                        title:'Create Part | Administrator'
                    });
                }
            },
            inventory_module:{
                current_part_id:null,
                handle_click:function(self){
                    var rowNode = $(self).parents('tr');
                    if (rowNode.hasClass('child')) {//Check if the current row is a child row
                        rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                    }
                    var rowData = PARTS.datatable.row(rowNode).data();
                    if(typeof INVENTORY_MODULE !== "undefined")INVENTORY_MODULE.part=rowData;

                    $('[data-create-inventory]').trigger('click');
                },
                create_submit:function(e){
                    // return;
                    var response = e.response;
                    var modal = e.modal;
                    var state = e.state; // can be 'beforeSend' or 'completed'
                    var linker = e.linker;
                    if(state=='beforeSend'){
                        /* request is not completed yet, we have form data available */

                        /* we need to create  a row dynamically and add to datatables */
                        var tempid = PARTS.table.find('tbody tr').length;
                        tempid=response.part_id;
                        var rowObj={};
                        rowObj._id=tempid;
                        rowObj.code='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                        rowObj.detail='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.sale_price='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                        rowObj.stock_left='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.actions={status:0};
                        rowObj.created_at=moment.utc().format();
                        rowObj.updated_at=moment.utc().format();

                        var row=PARTS.datatable.row('#'+tempid);
                        row.data(rowObj).invalidate();
                        var rowNode = row.node();

                        /* Check if child is shown, hide it, (Will help when row is updated) */
                        if (row.child.isShown()) {
                            row.child.hide();
                            $( rowNode ).removeClass('parent');
                        }


                        /* add the linker to row and change the color */
                        $( rowNode )
                        .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                        .attr('data-temp', linker);
                    }
                    else if(state=="error"){
                        /* it seems server respond with some errors, we need to delete the newly added row */

                        /* corresponding row */
                        var rowNode = PARTS.table.find('tbody tr[data-temp="'+linker+'"]');

                        /* remove from datatables */
                        PARTS.datatable.row(rowNode[0]).remove();

                        /* remove from DOM */
                        rowNode.remove();
                    }
                    else{
                        /* request might be completed and we have response from server */

                        /* corresponding row */
                        var rowNode = PARTS.table.find('tbody tr[data-temp="'+linker+'"]');

                        PARTS.datatable.row(rowNode[0]).data(response).invalidate();


                        /* remove the effect after some time */
                        setTimeout(function(){
                            rowNode
                            .removeAttr('data-temp')
                            .removeAttr('style');
                        },2000);

                    }

                    kingriders.Utils.isDebug() && console.log('response', e);
                },
                modal_loaded:function(){



                    if(typeof INVENTORY_MODULE !== "undefined"){

                        /* Update url */
                        if(INVENTORY_MODULE.part){
                            var url = "{{url('admin/parts')}}/"+INVENTORY_MODULE.part._id+"/inventory/add";
                            var title = 'Add Inventory | Administrator';
                            kingriders.Utils.replaceUrl(title, url);
                        }
                        INVENTORY_MODULE.Utils.reset_page();
                    }
                },
            }
        };
    }();

    $(function(){


        PARTS.init();
    });
</script>


@endsection
