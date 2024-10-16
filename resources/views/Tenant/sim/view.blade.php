@extends('Tenant.layouts.app')

@section('page_title')
    Sims
@endsection
@section('head')
    <link rel="stylesheet" kr-ajax-head href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
    <style kr-ajax-head>
        .iti {
            width: 100%;
        }

        /* Adjust the width of the input field to be 100% of the ITI container */
        .iti__input {
            width: 100%;
        }
    </style>
@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Sims</h3>
        </div>
        <div class="kt-portlet__head-toolbar">

            @if ($helper_service->routes->has_access('tenant.admin.imports.sims'))
            <a href="{{ route('tenant.admin.imports.sims') }}" class="btn btn-outline-info btn-elevate btn-square mr-2">
                <i class="flaticon-download"></i>
                Import Sims
            </a>
            @endif

            @if ($helper_service->routes->has_access('tenant.admin.sims.add'))
            <button type="button" kr-ajax-size="30%" kr-ajax-submit="SIMS.create_submit" kr-ajax-modalclosed="SIMS.modal_closed" kr-ajax-contentloaded="SIMS.modal_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.sims.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Sim
            </button>
            @endif

        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sim Number</th>
                    <th>Type</th>
                    <th>Assigned to</th>
                    <th>Allowed Balance</th>
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
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>

<script type="text/javascript">
    var SIMS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = SIMS.table;

            // begin first table
            SIMS.datatable = table.DataTable({
                responsive: true,
                lengthMenu: [5, 10, 25, 50, 100],
                pageLength: 50,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax:"{{route('tenant.admin.sims.data')}}",
                createdRow(row, data, dataIndex){
                    $(row).attr('data-activity-id',data.id);
                    $(row).attr('data-activity-modal','App\\Models\\Sim');
                },
                rowId:'id',
                columns: [
                    {data: 'id', visible:false},
                    {data: 'number', orderable: false},
                    {data: 'type', orderable: false},
                    {data: 'entities', orderable: false,
                        render(data, type, full, meta){
                            if (typeof data === "undefined" || data.length == 0) return '<span class="text-success">Free</span>';
                            return `
                            <ul class="list-group list-group-flush">
                                ${data.map(entity => {
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
                    {data: 'entities', orderable: false,
                        render(data, type, full, meta){
                            if (typeof data === "undefined" || data.length == 0) return '';
                            let balances = data.map(entity => {
                                if(!entity.unassign_date){
                                    return `<li class="list-group-item p-0">${entity.allowed_balance}</li>`;
                                }
                            });
                            return `<ul class="list-group list-group-flush">${balances}</ul>`.replaceAll(',','');
                        }
                    },
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

                                        @if ($helper_service->routes->has_access('tenant.admin.sims.edit'))
                                            <a href='#' kr-ajax="${`{{ route('tenant.admin.sims.edit', '__param') }}`.replace('__param', full.id)}" class="dropdown-item" title="Edit"  kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="SIMS.modal_closed" kr-ajax-submit="SIMS.edit_data_sent" kr-ajax-contentloaded="SIMS.edit_data_loaded">
                                                <i class="la la-edit"></i>
                                                Edit
                                            </a>
                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.sims.entities.add'))
                                        <a href="#" class="dropdown-item" title="Assign" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-modalclosed="SIMS.assign_module.modal_closed" kr-ajax-submit="SIMS.assign_module.modal_submit" kr-ajax-contentloaded="Function()" kr-ajax="${"{{route('tenant.admin.sims.entities.add', '_:param')}}".replace('_:param', full.id)}">
                                            <i class="la la-share"></i>
                                            Assign entity
                                        </a>
                                        @endif

                                        @if ($helper_service->routes->has_access('tenant.admin.sims.entities.view'))
                                        <a href="${"{{ route('tenant.admin.sims.entities.view', '_:param')}}".replace('_:param', full.id)}" class="dropdown-item" title="View">
                                            <i class="la la-eye"></i>
                                            View entities
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
            edit_data_sent:function(e){
                if(e.state == 'completed') SIMS.datatable.ajax.reload();
            },
            edit_data_loaded:function(e){
                // if(typeof SIM_EDIT_MODULE !== "undefined") SIM_EDIT_MODULE.reset_page();
            },
            create_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='completed'){
                    SIMS.datatable.ajax.reload();
                }
                kingriders.Utils.isDebug() && console.log('response', e);
            },
            modal_loaded:function(){
                if(typeof SIM_CREATE_MODULE !== "undefined") SIM_CREATE_MODULE.reset_page();
            },
            modal_closed:function(){
                // SIM_CREATE_MODULE.reset_page();
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
                            SIMS.datatable.ajax.reload();
                        });
                    }
                }
            },
        };
    }();

    $(function(){


        SIMS.init();
    });
</script>


@endsection
