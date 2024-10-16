@extends('Tenant.layouts.app')

@section('page_title')
{{ $type === 'driver' ? 'Driver' : 'Rider' }}
@endsection
@section('head')
    <style>
        .child .dtr-details>li {
            display: table-row !important;
            border: 0 !important;
        }

        ul.dtr-details {
            -webkit-column-count: 3;
            -moz-column-count: 3;
            column-count: 3;
            -webkit-column-gap: 5px;
            -moz-column-gap: 5px;
            column-gap: 5px;
            display: inline-block !important;
            width: 100%;
            background-color: #f9f9f9;
            padding: 10px 5px;
            column-rule: 1px solid #ddd;
            border: 1px solid #ddd;
        }

        .o-pad {
            padding: 0px !important;
        }

        tr {
            vertical-align: center;
        }

        td.details-control {
            background: url('https://krapp.kingriders.net/details_open.png') no-repeat center center !important;
            cursor: pointer;
        }

        tr.parent td.details-control {
            background: url('https://krapp.kingriders.net/details_close.png') no-repeat center center !important;
        }

    </style>
@endsection
@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid mt-5">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">{{ $type === 'driver' ? 'Driver' : 'Rider' }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    @if ($helper_service->routes->has_access('tenant.admin.drivers.add'))
                        <a href="{{ route('tenant.admin.drivers.add') }}?type={{$type}}" class="btn btn-info btn-elevate btn-square">
                            <i class="flaticon2-plus-1"></i>
                            Create {{ $type === 'driver' ? 'Driver' : 'Rider' }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="kt-portlet__body">

                <!--begin: Datatable -->
                <div class="table-responsive">
                    <table class="table table-rounded table-row-bordered gy-7 gs-7" id="datatable">
                        <thead>
                            <tr role="row">
                                <th></th>
                                <th>KLID</th>
                                <th>Name</th>
                                <th>Clients</th>
                                <th>Vehicles</th>
                                <th>Sims</th>
                                <th>Missing Fields</th>
                                <th>Addons</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!--end: Datatable -->
            </div>
        </div>
    </div>
@endsection

@section('foot')
    {{-- ----------------------------------------------------------------------------
                            SCRIPTS (use in current page)
------------------------------------------------------------------------------ --}}

    <script type="text/javascript">
        var DRIVERS = function() {

            /* Initialize the datatables */
            var init_table = function() {
                var table = DRIVERS.table;

                // begin first table
                DRIVERS.datatable = table.DataTable({
                    responsive: true,
                    searchDelay: 100,
                    processing: true,
                    serverSide: false,
                    deferRender: true,
                    ajax: "{{ route('tenant.admin.drivers.data', ['type' => $type]) }}",
                    rowId: '_id',
                    createdRow(row, data, dataIndex){
                        $(row).attr('data-activity-id',data.id);
                        $(row).attr('data-activity-modal','App\\Models\\Driver');
                    },
                    columns: [
                        { data: '_id', visible: false },
                        { data: 'id', orderable: false, width: '5%' },
                        { data: 'name', orderable: false },
                        { data: 'client_entities', orderable: false },
                        { data: 'vehicle_entities', orderable: false },
                        { data: 'sim_entities', orderable: false },
                        { data: 'missing_fields', width: '10%', orderable: false },
                        { data: 'addons', width: '20%', orderable: false, },
                        { data: 'created_at', visible: false }, // for Sorting Purpose
                        { data: 'actions', orderable: false },
                    ],
                    order: [
                        [8, 'asc']
                    ],
                    columnDefs: [
                        {
                            targets: -1,
                            title: 'Actions',
                            responsivePriority: 1,
                            orderable: false,
                            render: (data, type, full, meta) => {
                                if (typeof data == "undefined" || !data) return '';
                                if (data.status == 0) return '';
                                return `
                                <span class="dtr-data d-block text-center">
                                    <span class="dropdown">
                                        <a href="#" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="true">
                                            <i class="la la-ellipsis-h"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">


                                            @if ($helper_service->routes->has_access('tenant.admin.drivers.edit'))
                                            <a href="${`{{ route('tenant.admin.drivers.edit', '__param') }}`.replace('__param', full.id)}" class="dropdown-item" title="Edit Driver">
                                                <i class="la la-edit"></i>
                                                Edit
                                            </a>
                                            @endif
                                            @if ($helper_service->routes->has_access('tenant.admin.drivers.viewDetails'))
                                            <a href="${`{{ route('tenant.admin.drivers.viewDetails', '__param') }}`.replace('__param', full.id)}" class="dropdown-item">
                                                <i class="la la-info"></i>
                                                Details
                                            </a>
                                            @endif

                                            @if ($helper_service->routes->has_access('tenant.admin.drivers.edit'))
                                            <a href="#" class="dropdown-item" title="Edit Driver" onclick="DRIVERS.delete('${full.id}');return false;">
                                                <i class="la la-trash"></i>
                                                Delete
                                            </a>
                                            @endif

                                            @if ($helper_service->routes->has_access('tenant.admin.drivers.passports.history.view'))
                                                <a href="${`{{ route('tenant.admin.drivers.passports.history.view', '__param') }}`.replace('__param', full.id)}" class="dropdown-item" title="Change Booking">
                                                    <i class="la flaticon-book"></i>
                                                    View Passport History
                                                </a>
                                            @endif

                                            @if ($helper_service->routes->has_access('tenant.admin.statementledger.driver.view'))
                                            <a href="${`{{ route('tenant.admin.statementledger.driver.view', '__param') }}`.replace('__param', full.id)}" class="dropdown-item">
                                                <i class="la flaticon-file-2"></i>
                                                View Driver Statement
                                            </a>
                                            @endif

                                            @if ($helper_service->routes->has_access('tenant.admin.statementledger.company.view'))
                                            <a href="${`{{ route('tenant.admin.statementledger.company.view', '__param') }}`.replace('__param', full.id)}" class="dropdown-item">
                                                <i class="la flaticon-file-2"></i>
                                                View Company Statement
                                            </a>
                                            @endif
                                        </div>
                                    </span>
                                </span>`;
                            },
                        },
                        {
                            targets: 1,
                            responsivePriority: 1,
                            orderable: true,
                            render: (data, type, full, meta) => {
                                if(!full.profile_picture) return '';
                                let image = kingriders.Config.storage_path + full.profile_picture
                                if(data < 10){
                                    data = '0'+data;
                                }
                                return `
                                    <a href="${`{{ route('tenant.admin.drivers.viewDetails', '_:param') }}`.replace('_:param', full.id)}" class="kt-media">
                                        <img src="${image}" alt="image">
                                    </a>
                                `;
                            },
                        },
                        {
                            targets: 2,
                            title: 'Name',
                            orderable: false,
                            render: (data, type, full, meta) => {
                                return `<a href="{{ route('tenant.admin.drivers.viewDetails', '_:param') }}">KL ${full.id} | ${data}</a>`
                                .replace('_:param', full.id);
                            }
                        },
                        {
                            targets: 3,
                            orderable: false,
                            render: (data, type, full, meta) => {
                                if (typeof data === "undefined" || data.length == 0) return '';
                                return `
                                    <ul class="list-group list-group-flush">
                                        ${data.map(entity => {
                                            return `<li class="list-group-item p-0">
                                                <a href="${"{{route('tenant.admin.clients.entities.view', '_:param')}}".replace('_:param', entity.client.id)}">${entity.client.name}</a>
                                            </li>`
                                        }).join('')}
                                    </ul>
                                `;
                            }
                        },
                        {
                            targets: 4,
                            orderable: false,
                            render: (data, type, full, meta) => {
                                if (typeof data === "undefined" || data.length == 0) return '';
                                return `
                                    <ul class="list-group list-group-flush">
                                        ${data.map(entity => {
                                                return `<li class="list-group-item p-0">
                                                    <a href="${"{{route('tenant.admin.vehicles.entities.view', '_:param')}}".replace('_:param', entity.vehicle.id)}">${entity.vehicle.plate}</a>
                                                </li>`
                                        }).join('')}
                                    </ul>
                                `;
                            }
                        },
                        {
                            targets: 5,
                            orderable: false,
                            render: (data, type, full, meta) => {
                                if (typeof data === "undefined" || data.length == 0) return '';
                                return `
                                    <ul class="list-group list-group-flush">
                                        ${data.map(entity => {
                                                return `<li class="list-group-item p-0">
                                                    <a href="${"{{route('tenant.admin.sims.entities.view', '_:param')}}".replace('_:param', entity.sim.id)}">${entity.sim.number}</a>
                                                </li>`
                                        }).join('')}
                                    </ul>
                                `;
                            }
                        },
                        {
                            targets: 6,
                            data: 'missing_fields', // Missing Fields
                            defaultContent: '',
                            render: (data, type, row, meta) => {
                                if (typeof data === "undefined" || data.length == 0) return '';

                                let html = data.map(function(item) {
                                    return `
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div>
                                                <span class="text-danger">*</span>
                                                <small>${item.text}</small>
                                            </div>
                                            ${item.restricted ? `<small class="kt-font-bold kt-font-danger ml-1">Required</small>` : ''}
                                        </div>
                                    `;
                                }).join('');

                                /* If data is more than 1, we will show it in accordion */
                                if (data.length > 1) {
                                    return `
                                        <div class="text-wrap" >
                                            <div class="accordion accordion-outline" id="missingField${meta.row}" style="min-width: 163px;">
                                                <div class="card">
                                                    <div class="card-header" id="headingOne${meta.row}">
                                                        <div class="card-title collapsed px-0 pl-3 py-2" data-toggle="collapse" data-target="#collapseOne${meta.row}" aria-expanded="true">
                                                            <span class="badge badge-danger small">${data.length}</span>
                                                        </div>
                                                    </div>
                                                    <div id="collapseOne${meta.row}" class="card-body-wrapper collapse" aria-labelledby="headingOne${meta.row}" data-parent="#missingField${meta.row}">
                                                        <div class="card-body px-2 small text-nowrap font-weight-bold">
                                                            ${html}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    `;

                                } else {
                                    /* Show it simply */
                                    return `<div class="text-wrap font-weight-bold">${html}</div>`;

                                }
                            }
                        },
                        {
                            targets: 7,
                            title: 'Addons',
                            render: (data, type, row, meta) => {
                                if (typeof data === "undefined" || data.length == 0) return '';
                                let status = false;
                                if (typeof row.status === "undefined" || row.status.length == 0) status = false;
                                else if(row.status === 'initiated') status = `<span class="badge badge-danger small text-capitalize">${row.status}</span>`;
                                else if(row.status === 'pending_to_start') status = `<span class="badge badge-info small text-capitalize">Pending</span>`;
                                else if(row.status === 'completed') status = `<span class="badge badge-success small text-capitalize">${row.status}</span>`;
                                else status = `<span class="badge badge-warning text-capitalize">${row.status}</span>`;
                                let html = data.map(function(item) {
                                    let x = '';
                                    if(item.status){
                                        if(item.status === 'inprogress'){
                                            x = `${item.current_stage}`;
                                        }
                                        else if(item.status === 'pending_to_start'){
                                            x = `Pending`;
                                        }
                                        else{
                                            x = `${item.status}`;
                                        }
                                    }
                                    return `<li class="list-group-item">
                                                <div class="d-flex justify-content-start">
                                                    <span class="border-right px-2 small text-capitalize">${item.setting.title}</span>
                                                    <span class="border-right px-2 small text-success text-capitalize${x == '' ? ' d-none':''}">${x}</span>
                                                    <span class="px-2 text-capitalize small text-warning">${moment(item.updated_at).fromNow()}</span>
                                                </div>
                                            </li>`;
                                }).join('');

                                /* If data is more than 1, we will show it in accordion */
                                if (data.length > 1) {
                                    return `
                                        <div class="text-wrap" >
                                            <div class="accordion accordion-outline" id="missingField${meta.row + meta.col}">
                                                <div class="card">
                                                    <div class="card-header" id="headingOne${meta.row + meta.col}">
                                                        <div class="card-title p-2 px-4 collapsed" data-toggle="collapse" data-target="#collapseOne${meta.row + meta.col}" aria-expanded="true">
                                                            ${status === false ? `<span class="badge badge-danger small">${data.length}</span>`: status}
                                                        </div>
                                                    </div>
                                                    <div id="collapseOne${meta.row + meta.col}" class="card-body-wrapper collapse" aria-labelledby="headingOne${meta.row + meta.col}" data-parent="#missingField${meta.row + meta.col}">
                                                        <div class="card-body o-pad text-nowrap">
                                                            <ul class="list-group list-group-flush">
                                                            ${html}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    `;

                                } else {
                                    /* Show it simply */
                                    return `<div class="text-wrap">${html}</div>`;

                                }
                            }
                        }
                    ],
                });

            };

            /* page settings */
            return {
                table: $('#datatable'),
                datatable: null,
                init: function() {
                    init_table();
                },
                delete(param) {
                    swal.fire({
                        position: 'center',
                        type: 'info',
                        showCancelButton: true,
                        title: 'Confirm Deletion',
                        html: ``,
                    }).then(res => {
                        if (res.value) {
                            let res = $.ajax(`{{ route('tenant.admin.drivers.delete', '_param') }}`.replace('_param',
                                param)).then(res => {
                                if (res.status === 204) {
                                    let table = $('#datatable').DataTable();
                                    table.row(`#${param}`).remove().draw();
                                }
                                swal.fire({
                                    position: 'center',
                                    type: res.status === 204 ? 'success' : 'error',
                                    title: 'User Deletion',
                                    html: `${res.message}`
                                })
                            });
                        }
                    });
                },
            };
        }();

        $(function() {


            DRIVERS.init();
        });
    </script>
@endsection
