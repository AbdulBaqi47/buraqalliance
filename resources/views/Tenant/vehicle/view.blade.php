@extends('Tenant.layouts.app')

@section('page_title')
    {{ $type === 'vehicle' ? 'Vehicles' : 'Bikes' }}
@endsection
@section('head')

@endsection
@section('content')

<div class="kt-section__content kt-section__content--border">

    <!-- Modal -->
    <div class="modal fade" id="add_bulk_vehicles_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Multiple Vehicles</h5>
                    <button id="add_bulk_vehicles_modal_dismiss_btn" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <!--begin::Form-->
                <form class="kt-form" id="bulk_create_vehicle_form" enctype="multipart/form-data">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="kt-portlet__body">

                            <div class="form-group">
                                <label>Number <span class="text-danger">*<span></label>
                                <input type="number" autocomplete="off" name="count" required class="form-control" placeholder="Number Of Vehicles">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-brand">Create</button>
                    </div>
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>
</div>

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title"> {{ $type === 'vehicle' ? 'Vehicles' : 'Bikes' }} </h3>
        </div>
        <div class="kt-portlet__head-toolbar ledger__fields">
            <div class="kt-radio-inline kt-font-bold">
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success m-0 mr-2">
                    <input type="checkbox" onchange="VEHICLES.handleShowAllClick(event, this);" name="show_all"> Show all (including assigned vehicles)
                    <span></span>
                </label>
            </div>
        </div>
        <div class="kt-portlet__head-toolbar">

            <div>
                @if ($helper_service->routes->has_access('tenant.admin.vehicles.add'))
                <a href="{{route('tenant.admin.vehicles.add')}}?type={{$type}}" class="btn btn-info btn-elevate btn-square">
                    <i class="flaticon2-plus-1"></i>
                    Create {{ $type === 'vehicle' ? 'Vehicle' : 'Bike' }}
                </a>
                @endif
            </div>

        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plate #</th>
                    <th>Chassis #</th>
                    <th>Engine #</th>
                    <th>Model / Year</th>
                    <th>Assigned To</th>
                    <th>Owner</th>
                    <th>State</th>
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
    var VEHICLES = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = VEHICLES.table;

            var q = VEHICLES.Utils.buildQuery();

            // begin first table
            VEHICLES.datatable = table.DataTable({
                responsive: true,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                destroy:true, // So it can be re-initialize
                ajax: {
                    url : `{{ route('tenant.admin.vehicles.data', ['type' => $type])}}`,
                    data:{
                        show_all: q.show_all ? 1 : 0
                    },
                },
                rowId:'id',
                columns: [
                    {data: 'id'},
                    {data: 'plate', orderable: false},
                    {data: 'chassis_number', orderable: false},
                    {data: 'engine_number', orderable: false},
                    {data: 'model', orderable: false},
                    {data: null, orderable: false}, // Assigned To
                    {data: null, orderable: false}, // Owner
                    {data: 'state', orderable: false},
                    {data: 'created_at', visible:false},
                    {data: 'actions'},
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
                            <span class="dtr-data d-block text-center">
                                <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                        <i class="la la-ellipsis-h"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">

                                        @if ($helper_service->routes->has_access('tenant.admin.vehicles.single.edit'))
                                            <a href="${`{{route('tenant.admin.vehicles.single.edit', "__param")}}`
                                                    .replace('__param', full.id)}?type=${full.type}" class="dropdown-item">
                                                <i class="la la-eye"></i>
                                                Edit
                                            </a>
                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.vehicles.entities.add'))
                                        <a href="#" class="dropdown-item" title="Assign" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLES.assign_module.modal_closed" kr-ajax-submit="VEHICLES.assign_module.modal_submit" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.vehicles.entities.add', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-share"></i>
                                            Assign entity
                                        </a>
                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.vehicles.entities.show_rental_company'))
                                        ${typeof full.vehicle_client_entities[0] !== 'undefined' ? `
                                            <a href="#" class="dropdown-item" title="Change Rental Company" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="VEHICLES.change_rental_module.modal_closed" kr-ajax-submit="VEHICLES.change_rental_module.modal_submit" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.vehicles.entities.show_rental_company', '_:param')}}".replace('_:param', full.id)}">
                                                <i class="la la-edit"></i>
                                                Change Rental Company
                                            </a>
                                        `:``}
                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.vehicles.entities.view'))
                                        <a href="${"{{ route('tenant.admin.vehicles.entities.view', '_:param')}}".replace('_:param', full.id)}" class="dropdown-item" title="View">
                                            <i class="la la-eye"></i>
                                            View entities
                                        </a>
                                        @endif
                                    </div>
                                </span>
                            </span>`;
                        },
                    },
                    {
                        targets: 0, // ID
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return `
                                <div class="d-flex flex-column">
                                    ${data}
                                </div>
                            `;
                            return full.model + (full.year ? " / " + full.year : "");
                        },
                    },
                    {
                        targets: 1, // Plate Title
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return full.plate_title;
                        },
                    },
                    {
                        targets: 4, // Model
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            return full.model + (full.year ? " / " + full.year : "");
                        },
                    },
                    {
                        targets: 5, // Assigned To
                        render(data, type, full, meta){
                            var entities = full.entities ?? [];
                            if (typeof entities === "undefined" || entities.length == 0) return '<span class="text-success">Free</span>';
                            return `
                            <ul class="list-group list-group-flush">
                                ${entities.map(entity => {
                                    if(!entity.unassign_date){
                                        if(entity.source_model === 'App\\Models\\Tenant\\Driver'){
                                            return `<li class="list-group-item p-0"><a href="${"{{route('tenant.admin.drivers.viewDetails', '_:param')}}".replace('_:param', entity.source.id)}">${entity.source.full_name}</a></li>`;
                                        }else{
                                            return `<li class="list-group-item p-0">Staff : ${entity.source.name}</li>`;
                                        }
                                    }
                                }).join('')}
                            </ul>`;
                        }
                    },
                    {
                        targets: 6, // Owner
                        render(data, type, full, meta){
                            var entities = full.vehicle_client_entities ?? [];
                            if (typeof entities === "undefined" || entities.length == 0) return '<span class="text-success">No Owner</span>';
                            return `
                            <ul class="list-group list-group-flush">
                                ${entities.map(entity => {
                                    if(!entity.unassign_date){
                                        return `<li class="list-group-item p-0"><a href="${"{{route('tenant.admin.clients.entities.view', '_:param')}}".replace('_:param', entity.client.id)}">${entity.client.name}</a></li>`;
                                    }
                                }).join('')}
                            </ul>`;
                        }
                    },
                    {
                        targets: 7, // State
                        render: function(data, type, full, meta) {
                            var status = {
                                "ready_to_assign": {'title': 'Ready to Assign', 'class': ' kt-badge--success'},
                                "maintenance": {'title': 'Maintenance', 'class': ' kt-badge--danger'},
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="kt-badge ' + status[data].class + ' kt-badge--inline kt-badge--pill text-nowrap">' + status[data].title + '</span>';
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
            handleShowAllClick(e, target){
                /* update the url before initiating */
                VEHICLES.Utils.update_url();

                /* init the table */
                VEHICLES.init();
            },

            Utils:{
                buildQuery:function(){
                    /* Use this to fetch input, like month and employee id */

                    var show_all = $('.ledger__fields [name="show_all"]').is(':checked');

                    return {show_all};
                },
                update_url:function(){
                    /* add employee id and month as query string in url, so we can save the state */
                    var q = VEHICLES.Utils.buildQuery();

                    /* make data to append on url */
                    var data = {
                        all:q.show_all ? 1 : 0
                    }

                    /* update URL */
                    kingriders.Utils.buildQueryString(data);

                },
                update_input:function(q){
                    /* Use this to update the employee_id and month */
                    if(typeof q.show_all !== "undefined"){
                        $('.ledger__fields [name=show_all]').prop('checked', q.show_all == 1);
                    }

                },
                fetchQuery:function(){
                    /* we will fetch month and range from url */
                    var show_all = kingriders.Utils.fetchQueryString("all")||null;

                    return { show_all };
                }
            },

            createMultiple(e){
                e.preventDefault();
                var _form = $(this);
                $.ajax({
                    url: '{{ route('tenant.admin.vehicles.bulk.add')}}',
                    method: 'POST',
                    data:  _form.serializeArray(),
                    success(response){
                        $("#bulk_create_vehicle_form [name='count']").val('');
                        $('#add_bulk_vehicles_modal_dismiss_btn').click();
                        swal.fire({
                            position: 'center',
                            type: 'success',
                            title: response.message,
                        }).then(()=>{
                            VEHICLES.datatable.ajax.reload()
                        })
                    }
                });
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
                    if(typeof response.vehicle_id !== "undefined")is_edit=true;

                    /* we need to create  a row dynamically and add to datatables */
                    var tempid = VEHICLES.table.find('tbody tr').length;
                    if(is_edit)tempid=response.vehicle_id;
                    var rowObj={};
                    rowObj.id=tempid;
                    rowObj.plate='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.model='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.year='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.chassis_number='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.engine_number='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.color='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.actions={status:0};
                    rowObj.created_at=moment.utc().format();
                    rowObj.updated_at=moment.utc().format();

                    if(is_edit){
                        var row=VEHICLES.datatable.row('#'+tempid);

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
                        var rowNode = VEHICLES.datatable
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
                    var rowNode = VEHICLES.table.find('tbody tr[data-temp="'+linker+'"]');

                    /* check if row data found, we need to rollback the data. otherwise, jsut remove the row */

                    if(rowNode[0].hasAttribute('data-row')){
                        var orgData = JSON.parse(rowNode.attr('data-row'));
                        VEHICLES.datatable.row(rowNode[0]).data(orgData).invalidate();
                    }
                    else{
                        /* remove from datatables */
                        VEHICLES.datatable.row(rowNode[0]).remove();

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
                    var rowNode = VEHICLES.table.find('tbody tr[data-temp="'+linker+'"]');

                    VEHICLES.datatable.row(rowNode[0]).data(response).invalidate();


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
                var rowData = VEHICLES.datatable.row(rowNode).data();

                if(typeof VEHICLE_MODULE !== "undefined")VEHICLE_MODULE.Utils.load_page(rowData);
            },
            modal_loaded:function(){
                if(typeof VEHICLE_MODULE !== "undefined"){
                    $(VEHICLE_MODULE.container + ' form').attr('action', $(VEHICLE_MODULE.container+' form').attr('data-add')).find('[name=vehicle_id]').remove();

                    VEHICLE_MODULE.Utils.reset_page();
                }
            },
            modal_closed:function(e){
                /* Reset url */
                var MODAL = $(this);
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/vehicles/add')}}",
                        title:'Create Vehicle | Administrator'
                    });
                }
            },
            assign_module:{
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

                modal_submit: function(e){
                    var response = e.response;
                    var modal = e.modal;
                    var state = e.state; // can be 'beforeSend' or 'completed'
                    var linker = e.linker;
                    if(state=='completed'){
                        // Show alert
                        swal.fire({
                            toast: true,
                            customClass: {
                                content:'mt-0 pl-2'
                            },
                            position: 'top',
                            showConfirmButton: true,
                            timer: 1000,
                            type: 'success',
                            html: `Data saved successfully`,
                        }).then(res => {
                            VEHICLES.datatable.ajax.reload();
                        });
                    }
                }
            },
            change_rental_module:{
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

                modal_submit: function(e){
                    var response = e.response;
                    var modal = e.modal;
                    var state = e.state; // can be 'beforeSend' or 'completed'
                    var linker = e.linker;
                    if(state=='completed'){
                        // Show alert
                        swal.fire({
                            toast: true,
                            customClass: {
                                content:'mt-0 pl-2'
                            },
                            position: 'top',
                            showConfirmButton: true,
                            timer: 1000,
                            type: 'success',
                            html: `Data saved successfully`,
                        }).then(res => {
                            VEHICLES.datatable.ajax.reload();
                        });
                    }
                }
            },
        };
    }();

    $(function(){
        $('#bulk_create_vehicle_form').on('submit',VEHICLES.createMultiple);

        // Match query param to update DOM
        var q = VEHICLES.Utils.fetchQuery();
        VEHICLES.Utils.update_input(q)

        VEHICLES.init();
    });
</script>


@endsection
