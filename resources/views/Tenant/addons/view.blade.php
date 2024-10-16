@extends('Tenant.layouts.app')

@section('page_title')
@php
    $addon_name_cleaned = str_replace("_"," ",$dept_name);
    if($addon_name_cleaned === 'view') $addon_name_cleaned = '';
@endphp
    {{ucfirst($addon_name_cleaned)}} Addons
@endsection
@section('head')

@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title text-capitalize">{{$addon_name_cleaned}} Addons</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <label class="kt-checkbox small ml-5 mt-2">
                <input id="all_filter" name="all_filter" type="checkbox" onchange="ADDONS.all_filter(this)">  Show all entries <br/>(Including pending balance = 0)
                <span></span>
            </label>
            @if($dept_name !== 'all')
            <label class="kt-checkbox small ml-5 mt-2">
                <input id="completed_filter" name="completed_filter" type="checkbox" onchange="ADDONS.completed_filter(this)">  Show completed entries
                <span></span>
            </label>
            @endif
        </div>
        <div class="kt-portlet__head-toolbar">

            @if ($helper_service->routes->has_access('tenant.admin.addons.add'))
            <button kr-ajax-size="60%" type="button" class="btn small btn-info btn-elevate btn-sm btn-square m-1" kr-ajax-modalclosed="ADDONS.modal_closed" kr-ajax-submit="ADDONS.create_submit" kr-ajax-contentloaded="Function()" kr-ajax="{{route('tenant.admin.addons.add')}}?type=staff">
                <i class="flaticon2-plus-1"></i>
                Add Staff Addon
            </button>
            <button kr-ajax-size="60%" type="button" class="btn small btn-info btn-elevate btn-sm btn-square m-1" kr-ajax-modalclosed="ADDONS.modal_closed" kr-ajax-submit="ADDONS.create_submit" kr-ajax-contentloaded="Function()" kr-ajax="{{route('tenant.admin.addons.add')}}?type=driver">
                <i class="flaticon2-plus-1"></i>
                Add Driver Addon
            </button>
            {{-- <button kr-ajax-size="60%" type="button" class="btn small btn-info btn-elevate btn-sm btn-square m-1" kr-ajax-modalclosed="ADDONS.modal_closed" kr-ajax-submit="ADDONS.create_submit" kr-ajax-contentloaded="Function()" kr-ajax="{{route('tenant.admin.addons.add')}}?type=vehicle">
                <i class="flaticon2-plus-1"></i>
                Add Vehicle Addon
            </button> --}}
            @endif

        </div>
    </div>

    <div class="kt-portlet__body ledger__container">
        <table class="table table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Total Chargable</th>
                    <th>Pending Balance</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

<div class="modal fade modal-setting-overrides" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Settings Overrides</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped- table-bordered table-hover table-checkable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Display Title</th>
                            <th>Amount</th>
                            <th>Charged</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-brand" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('foot')


{{------------------------------------------------------------------------------
                            SCRIPTS (use in current page)
--------------------------------------------------------------------------------}}

<script type="text/javascript">

    var ADDONS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = ADDONS.table;

            // begin first table
            ADDONS.datatable = table.DataTable({
                lengthMenu: [[-1], ["All"]],
                responsive: true,
                searchDelay: 100,
                processing: false,
                dom: 'rfti',
                serverSide: false,
                deferRender: true,
                destroy:true,
                ajax: {
                    url : `{{ route('tenant.admin.addons.data') }}`,
                    data:{
                        route: '{{$dept_name}}',
                        showAll: kingriders.Utils.getUrlParem('showAll'),
                        showCompleted: kingriders.Utils.getUrlParem('showCompleted'),
                    },
                },
                rowId:'id',
                createdRow(row, data, dataIndex){
                    $(row).attr('data-activity-id',data.id);
                    $(row).attr('data-activity-modal','App\\Models\\Addon');
                },
                columns: [
                    {data: 'id', width: "5%"},
                    {data: 'setting.title', orderable: false, width: "20%"},
                    {data: 'price', orderable: false, width: "10%"},
                    {data: 'remaining', orderable: false, width: "10%"},
                    {data: 'source', orderable: false, width: "30%"},
                    {data: 'status', orderable: false, width: "15%"},
                    {data: 'actions', width: "5%"},
                ],
                order:[[0, 'desc']],
                columnDefs: [
                    {
                        targets: -1,
                        title: '',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            let queryString = '';
                            if(full.source_id){
                                switch(full.setting.source_type){
                                    case 'vehicle':

                                        if(full.source_model === 'App\\Models\\VehicleBooking'){
                                            // Booking
                                            queryString = `?namespace=booking&resource_id=${full.source_id}`;
                                        }
                                        else if (!!full.link && !!full.link.vehicle_booking_id){
                                            // Vehicle
                                            queryString = `?namespace=booking&resource_id=${full.link.vehicle_booking_id}`;
                                        }
                                        else{
                                            // TMP Vehicle
                                            queryString = `?namespace=vehicle&resource_id=${full.source_id}`;
                                        }
                                        break;

                                    case 'staff':
                                        queryString = `?namespace=staff&resource_id=${full.source_id}`;
                                        break;

                                    case 'driver':
                                        queryString = `?namespace=booking&resource_id=${full.link.booking_id}`;
                                        break;

                                    default:
                                        break;
                                }
                            }

                            let param = full.id;
                            return `
                                <span class="dtr-data d-block text-center">
                                    <span class="dropdown">
                                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                            <i class="la la-ellipsis-h"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            ${full.remaining > 0 && queryString != '' ? `
                                            @if ($helper_service->routes->has_access('tenant.admin.addons.charge'))
                                            <a href='#' kr-ajax="${`{{ route('tenant.admin.addons.charge', '__param') }}${queryString}`.replace('__param', param)}" class="dropdown-item" title="Charge This Addon"  kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="ADDONS.modal_closed" kr-ajax-submit="ADDONS.edit_data_sent" kr-ajax-contentloaded="Function()">
                                                <i class="la la-cc-mastercard"></i>
                                                Charge
                                            </a>
                                            @endif
                                            `:``}
                                            ${full.status !== 'completed' ?  `
                                            @if ($helper_service->routes->has_access('tenant.admin.addons.changeStatus'))
                                            <a href='#' onclick="ADDONS.changeStatus(event, this, '${`{{ route('tenant.admin.addons.changeStatus', '__param') }}`.replace('__param', full.id)}')" class="dropdown-item" title="Change Status To Completed">
                                                <i class="la la-check-circle"></i>
                                                Mark as Completed
                                            </a>
                                            @endif
                                            `:``}
                                            ${full.payment_status !== 'paid' ?  `
                                            @if ($helper_service->routes->has_access('tenant.admin.addons.mark_as_paid'))
                                            <a href='#' onclick="ADDONS.changeStatus(event, this, '${`{{ route('tenant.admin.addons.mark_as_paid', '__param') }}`.replace('__param', full.id)}','payment')" class="dropdown-item" title="Change Status To Completed">
                                                <i class="la la-check-circle"></i>
                                                Mark as paid
                                            </a>
                                            @endif
                                            `:``}
                                            @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown'))
                                            <a href='#' kr-ajax="${`{{ route('tenant.admin.addons.breakdown', '__param') }}`.replace('__param', full.id)}" class="dropdown-item" title="View BreakDown"  kr-ajax-block-page-when-processing kr-ajax-size="90%" kr-ajax-modalclosed="ADDONS.modal_closed" kr-ajax-submit="Function()" kr-ajax-contentloaded="Function()">
                                                <i class="la la-eye"></i>
                                                View Breakdown
                                            </a>
                                            @endif
                                            @if ($helper_service->routes->has_access('tenant.admin.addons.single.edit'))
                                            <a href='#' kr-ajax="${`{{ route('tenant.admin.addons.single.edit', '__param') }}`.replace('__param', full.id)}" class="dropdown-item" title="Edit"  kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="ADDONS.modal_closed" kr-ajax-submit="ADDONS.edit_data_sent" kr-ajax-contentloaded="Function()">
                                                <i class="la la-edit"></i>
                                                Edit
                                            </a>
                                            @endif
                                        </div>
                                    </span>
                                </span>`;
                        },
                    },
                    {
                        targets: 1, // Title
                        render: function(data, type, full, meta) {

                            return `
                                <span>
                                    ${data}

                                    ${full.setting && full.setting.source_type ? `
                                        <small class="d-block">
                                            <span class="font-weight-bold">${full.setting.source_type.charAt(0).toUpperCase() + full.setting.source_type.slice(1)}</span>

                                            ${full.setting.source_required ? '<span> - Required</span>' : ''}
                                        </small>
                                    `
                                    :''}

                                    ${full.readable_details ? `
                                        <small class="d-block">
                                            ${full.readable_details}
                                        </small>
                                    `
                                    :''}

                                    ${typeof full.override_types !== "undefined" && !!full.override_types ? `
                                    <a href="" class="kt-link kt-link-info small" onclick="ADDONS.handleOverrideSettingsClick(event, this)" >
                                        Settings Overrides
                                    </a>

                                    ` : ''}
                                </span>
                            `;
                        },
                    },
                    {
                        targets: 4,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            if(full.source_type === 'driver' && full.source_id){
                                return `<a class="kt-link kt-link-primary" href="{{route('tenant.admin.drivers.viewDetails','_:param')}}">${data}</a> | <a class="kt-link kt-link-primary" href='{{ route('tenant.admin.statementledger.driver.view', '_:param2') }}'>View Statement</a>`
                                .replace('_:param',full.source_id)
                                .replace('_:param2', full.source_id);
                            }
                            if(full.source_type === 'staff' && full.source_id){
                                return `<a class="kt-link kt-link-primary" href="{{route('tenant.admin.employee.ledger.view')}}?m=_:pMonth&e=_:pId">${data}</a>`
                                .replace('_:pMonth', moment().startOf('month').format('YYYY-MM-DD'))
                                .replace('_:pId', full.source_id);
                            }
                            return `<span class="text-success">${data}</span>`;
                        },
                    },
                    {
                        targets: 5,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            let status_text = '';
                            if(full.status === 'initiated'){
                                status_text = `<span class="badge badge-danger text-capitalize">${full.status}</span>`;
                            }else if(full.status === 'pending_to_start'){
                                status_text = `<span class="badge badge-info text-capitalize">Pending</span>`;
                            }else if(full.status === 'inprogress'){
                                status_text = `<span class="badge badge-warning text-capitalize">${full.current_stage}</span>`;
                            }else{
                                status_text = `<span class="badge badge-success text-capitalize">${full.status}</span>`;
                            }

                            if(full.payment_status === 'paid'){
                                status_text += `<span class="kt-badge kt-badge--sm kt-badge--success mx-2"><i class="fa fa-check"></i></span>`;
                            }
                            return status_text;
                        },
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
            all_filter(element){
                kingriders.Utils.updateUrlParem('showAll',$(element).is(":checked"));
                ADDONS.init();
            },
            completed_filter(element){
                kingriders.Utils.updateUrlParem('showCompleted',$(element).is(":checked"));
                ADDONS.init();
            },
            changeStatus(e, element, route, type = 'status'){
                e.preventDefault();
                swal.fire({
                    title: 'Are You Sure ?',
                    position: 'center',
                    type: 'info',
                    html: type === 'status' ? 'To change addon status':'Mark As Paid',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then(e => {
                    if(e.value){
                        $.ajax({url: route,method: 'POST'}).then(res => {
                            ADDONS.datatable.ajax.reload();
                            swal.fire({
                                title: 'Success',
                                text: type === 'status' ? 'Status Changed To Completed.':'Addon Status Updated to Paid.',
                                type: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                            })
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            if(!!jqXHR.responseJSON && !!jqXHR.responseJSON.message) jqXHR.responseJSON.message = "Fill these fields of driver before completing addon";
                            /* this will handle & show errors */
                            kingriders.Plugins.KR_AJAX.showErrors(jqXHR);
                        });
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


                    /* we need to create  a row dynamically and add to datatables */
                    var tempid = $(ADDONS.table).find('tbody tr').length;


                    var rowObj={};
                    rowObj.id=tempid;
                    rowObj.setting={title:'<span class="kr-skeleton-box" style="width: 100%;height: 15px;"></span>'};
                    rowObj.price=parseFloat(response.price)||0;
                    rowObj.source='<span class="kr-skeleton-box" style="width: 40%;height: 15px;"></span>';
                    rowObj.actions={status:0};
                    rowObj.remaining=0;
                    rowObj.status = null;
                    rowObj.created_at=moment.utc().format();
                    rowObj.updated_at=moment.utc().format();

                    var rowNode = ADDONS.datatable
                    .row.add( rowObj )
                    .draw()
                    .node();


                    /* add the linker to row and change the color */
                    $( rowNode )
                    .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                    .attr('data-temp', linker);
                }
                else{
                    /* request might be completed and we have response from server */

                    ADDONS.datatable.ajax.reload();
                    return; // For now just reload the table

                }
            },
            modal_loaded:function(){
                if(typeof ADDONS_MODULE !== "undefined")ADDONS_MODULE.Utils.reset_page();
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
            edit_data_sent:function(e){
                if(e.state == 'completed'){
                    if(e.response.message){
                        toastr.success(e.response.message);
                    }
                    ADDONS.datatable.ajax.reload();
                }
            },

            handleOverrideSettingsClick(event, self){
                event.preventDefault();

                var rowNode = $(self).parents('tr');
                if (rowNode.hasClass('child')) {//Check if the current row is a child row
                    rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                }
                var rowData = ADDONS.datatable.row(rowNode).data();

                kingriders.Utils.isDebug() && console.log('clicked', rowData);

                var MODAL = $('.modal-setting-overrides');
                var table = MODAL.find('table tbody');
                var html = `
                    ${rowData.override_types.map(function(item, index){

                        return `<tr>
                            <td>${index + 1}</td>
                            <td>${item.title}</td>
                            <td>${item.display_title}</td>
                            <td>${item.amount ? item.amount : ''}</td>
                            <td>${item.charge ? `<i class="fa fa-check text-success"></i>` : `<i class="fa fa-times text-danger"></i>`}</td>
                        </tr>`;
                    })}
                `;
                table.html(html);


                MODAL.modal('show');
            },

        };
    }();


    $(function(){
        let showAll = true;
        $('#all_filter').prop('checked', showAll);
        kingriders.Utils.updateUrlParem('showAll',$('#all_filter').is(":checked"));
        @if($dept_name !== 'all')
        let showCompleted = false;
        $('#completed_filter').prop('checked', showCompleted);
        kingriders.Utils.updateUrlParem('showCompleted',$('#completed_filter').is(":checked"));
        @endif
        ADDONS.init();
    })

</script>


@endsection
