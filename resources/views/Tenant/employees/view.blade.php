@extends('Tenant.layouts.app')

@section('page_title')
    Employees
@endsection
@section('head')
@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Employees</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            @if ($helper_service->routes->has_access('tenant.admin.employee.add'))
            <button type="button" kr-ajax-submit="EMPLOYEES.create_submit" kr-ajax-size="30%" kr-ajax-modalclosed="EMPLOYEES.modal_closed" kr-ajax-contentloaded="EMPLOYEES.modal_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.employee.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Employee
            </button>
            @endif

            @if ($helper_service->routes->has_access('tenant.admin.employee.routes.add'))
            <button type="button" data-create-routes hidden kr-ajax-submit="EMPLOYEES.routes_module.create_submit" kr-ajax-size="80%" kr-ajax-contentloaded="EMPLOYEES.routes_module.modal_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.employee.routes.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Manage Routes
            </button>
            @endif
        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Salary</th>
                    <th>Status</th>
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
    var EMPLOYEES = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = EMPLOYEES.table;

            // begin first table
            EMPLOYEES.datatable = table.DataTable({
                responsive: true,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax: "{{route('tenant.admin.employee.data')}}",
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false},
                    {data: 'name', orderable: false},
                    {data: 'email', orderable: false},
                    {data: 'basic_salary', orderable: false},
                    {data: 'status', orderable: false},
                    {data: 'created_at', visible:false},
                    {data: 'actions'},
                ],
                order: [[5, 'desc']],
                columnDefs: [
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            if(data.status==0) return '';
                            return `
                            @if ($helper_service->routes->has_access('tenant.admin.employee.edit')||$helper_service->routes->has_access('tenant.admin.employee.routes.add'))
                            <span class="dtr-data">
                                <span class="dropdown">
                                    <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                        <i class="la la-ellipsis-h"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @if ($helper_service->routes->has_access('tenant.admin.employee.routes.add'))
                                        <a href="#" class="dropdown-item" onclick="EMPLOYEES.routes_module.handle_click(this);return false;">
                                            <i class="flaticon-add"></i>
                                            Handle Routes Access
                                        </a>
                                        @endif
                                        @if ($helper_service->routes->has_access('tenant.admin.employee.custom_routes.add'))
                                            <a href='#' kr-ajax="${`{{ route('tenant.admin.employee.custom_routes.add', '__param') }}`.replace('__param', full._id)}" class="dropdown-item" kr-ajax-block-page-when-processing kr-ajax-size="60%" kr-ajax-modalclosed="EMPLOYEES.routes_module.modal_closed" kr-ajax-submit="EMPLOYEES.routes_module.create_submit" kr-ajax-contentloaded="Function()">
                                                <i class="flaticon-add"></i>
                                                Handle Custom Access
                                            </a>
                                        @endif
                                        @if ($helper_service->routes->has_access('tenant.admin.employee.edit'))
                                        <a href="#" class="dropdown-item" title="Edit Employee" onclick="EMPLOYEES.edit(this);return false;">
                                            <i class="flaticon-eye"></i>
                                            Edit
                                        </a>
                                        @endif
                                    </div>
                                </span>
                            </span>
                            @endif
                            `;
                        },
                    },
                    {
                        targets: 4,
                        render: function(data, type, full, meta) {
                            // kingriders.Utils.isDebug() && console.log('data',data,type,full,meta);
                            var status = {
                                0: {'title': 'Inactive', 'class': ' kt-badge--danger'},
                                1: {'title': 'Active', 'class': ' kt-badge--success'},
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="kt-badge ' + status[data].class + ' kt-badge--inline kt-badge--pill">' + status[data].title + '</span>';
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
                kingriders.Utils.isDebug() && console.log('create_submit', e);
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){
                    /* request is not completed yet, we have form data available */

                    /* need to check if edit */
                    var is_edit=false;
                    if(typeof response.user_id !== "undefined")is_edit=true;

                    /* we need to create  a row dynamically and add to datatables */
                    var tempid = EMPLOYEES.table.find('tbody tr').length;
                    if(is_edit)tempid=response.user_id;
                    var rowObj={};
                    rowObj._id=tempid;
                    rowObj.email='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.name='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                    rowObj.basic_salary='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                    rowObj.status='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                    rowObj.actions={status:0};
                    rowObj.created_at=moment.utc().format();
                    rowObj.updated_at=moment.utc().format();

                    if(is_edit){
                        var row=EMPLOYEES.datatable.row('#'+tempid);
                        row.data(rowObj).invalidate();
                        var rowNode = row.node();

                        /* Check if child is shown, hide it, (Will help when row is updated) */
                        if (row.child.isShown()) {
                            row.child.hide();
                            $( rowNode ).removeClass('parent');
                        }
                    }
                    else{
                        var rowNode = EMPLOYEES.datatable
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
                    var rowNode = EMPLOYEES.table.find('tbody tr[data-temp="'+linker+'"]');

                    EMPLOYEES.datatable.row(rowNode[0]).data(response).invalidate();


                    /* remove the effect after some time */
                    setTimeout(function(){
                        rowNode
                        .removeAttr('data-temp')
                        .removeAttr('style');
                    },2000);

                }

            },
            edit:function(self){
                var rowNode = $(self).parents('tr');
                if (rowNode.hasClass('child')) {//Check if the current row is a child row
                    rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                }
                var rowData = EMPLOYEES.datatable.row(rowNode).data();
                if(typeof EMPLOYEES_MODULE !== "undefined")EMPLOYEES_MODULE.Utils.load_page(rowData);
            },
            modal_loaded:function(){
                $('#kt-portlet__create-employee form').attr('action', $('#kt-portlet__create-employee form').attr('data-add')).find('[name=user_id]').remove();
                if(typeof EMPLOYEES_MODULE !== "undefined")EMPLOYEES_MODULE.Utils.reset_page();
            },
            modal_closed:function(e){
                /* Reset url */
                var MODAL = $(this);
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/employees/add')}}",
                        title:'Create Employee | Administrator'
                    });
                }
            },

            routes_module:{
                handle_click:function(self){
                    var rowNode = $(self).parents('tr');
                    if (rowNode.hasClass('child')) {//Check if the current row is a child row
                        rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                    }
                    var rowData = EMPLOYEES.datatable.row(rowNode).data();
                    if(typeof ROUTES_MODULE !== "undefined")ROUTES_MODULE.employee=rowData;

                    $(`[data-create-routes]`).trigger('click');
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
                        var tempid = EMPLOYEES.table.find('tbody tr').length;
                        tempid=response.employee_id;
                        var rowObj={};
                        rowObj._id=tempid;
                        rowObj.email='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                        rowObj.name='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.basic_salary='<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>';
                        rowObj.status='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                        rowObj.actions={status:0};
                        rowObj.created_at=moment.utc().format();
                        rowObj.updated_at=moment.utc().format();

                        var row=EMPLOYEES.datatable.row('#'+tempid);

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


                        /* add the linker to row and change the color */
                        $( rowNode )
                        .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                        .attr('data-temp', linker);
                    }
                    else if(state=="error"){
                        /* it seems server respond with some errors, we need to delete the newly added row */

                        /* corresponding row */
                        var rowNode = EMPLOYEES.table.find('tbody tr[data-temp="'+linker+'"]');

                        /* check if row data found, we need to rollback the data. otherwise, jsut remove the row */

                        if(rowNode[0].hasAttribute('data-row')){
                            var orgData = JSON.parse(rowNode.attr('data-row'));
                            EMPLOYEES.datatable.row(rowNode[0]).data(orgData).invalidate();
                        }
                        else{
                            /* remove from datatables */
                            EMPLOYEES.datatable.row(rowNode[0]).remove();

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
                        var rowNode = EMPLOYEES.table.find('tbody tr[data-temp="'+linker+'"]');

                        EMPLOYEES.datatable.row(rowNode[0]).data(response).invalidate();

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
                modal_loaded:function(Modal){
                    if(typeof ROUTES_MODULE !== "undefined"){
                        ROUTES_MODULE.Utils.reset_page();
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
            }
        };
    }();

    $(function(){


        EMPLOYEES.init();
    });
</script>


@endsection
