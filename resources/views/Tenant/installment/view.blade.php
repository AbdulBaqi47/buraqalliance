@extends('Tenant.layouts.app')

@section('page_title')
    Installments
@endsection
@section('head')
<style>
    @media (min-width: 768px){
        .modal.krajax-modal .modal-dialog {
            max-width: 30% !important;
        }
    }
</style>
@endsection
@section('content')

<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Installments</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Charge Date</th>
                    <th>Pay Date</th>
                    <th>Charge Amount</th>
                    <th>Pay Amount</th>
                    <th>Source</th>
                    <th>Account</th>
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
    var INSTALLMENTS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = INSTALLMENTS.table;

            // begin first table
            INSTALLMENTS.datatable = table.DataTable({
                responsive: true,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax: {
                    url: "{{route('tenant.admin.installments.data')}}",
                    data: {
                        type:  '{{$type}}'
                    }
                },
                rowId:'id',
                columns: [
                    {data: 'id'},
                    {data: 'charge_date'},
                    {data: 'pay_date'},
                    {data: 'charge_amount'},
                    {data: 'pay_amount'},
                    {data: 'source_id'},
                    {data: 'account_id'},
                    {data: 'status'},
                    {data: 'created_at', visible:false},
                    {data: 'actions', orderable: false},
                ],
                order: [[1, 'asc']],
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
                                            @if ($helper_service->routes->has_access('tenant.admin.installment.edit'))
                                            <a href='#' kr-ajax-block-page-when-processing kr-ajax-submit="INSTALLMENTS.editCallback" kr-ajax-contentloaded="Function()" kr-ajax="${`{{ route('tenant.admin.installment.edit', '__param') }}`.replace('__param', full.id)}" class="dropdown-item" title="Edit Installment">
                                                <i class="la la-edit"></i>
                                                Edit
                                            </a>
                                            @endif
                                            @if ($helper_service->routes->has_access('tenant.admin.installment.delete'))
                                            <a href="#" class="dropdown-item" title="Delete Installment" onclick="INSTALLMENTS.delete('${full.id}');return false;">
                                                <i class="la la-trash"></i>
                                                Delete
                                            </a>
                                            @endif
                                        </div>
                                    </span>
                                </span>`;
                        },
                    },
                    {
                        targets: 5,
                        render(data, type, full, meta){
                            let modal_name = '';
                            let source_id = full.source_id;

                            if(full.source_model === 'App\\Models\\Vehicle'){
                                if(full.source.vehicle_booking_id){
                                    modal_name = 'B#';
                                    source_id = full.source.vehicle_booking_id;
                                }else{
                                    modal_name = 'V#';
                                }
                            }
                            else if(full.source_model === 'App\\Models\\Addon'){
                                modal_name = 'Addon';
                            }
                            else if(full.source_model === 'App\\Models\\Driver'){
                                modal_name = 'Driver';
                            }
                            console.log(full.source);
                            return `${modal_name} ${source_id}`;
                        },
                    },
                    {
                        targets: 6,
                        render(data, type, full, meta){
                            return `<a href="{{route('module.accounts.transactions.view','__param')}}">${full.account.title}</a>`.replace('__param',full.account_id);
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
            editCallback(){
                INSTALLMENTS.datatable.ajax.reload()
            },
            delete(param){
                swal.fire({
                        position: 'center',
                        type: 'info',
                        showCancelButton: true,
                        title: 'Confirm Deletion',
                        html: ``,
                    }).then(res => {
                        if (res.value) {
                            let res = $.ajax(`{{ route('tenant.admin.installment.delete', '_param') }}`.replace('_param',
                                param)).then(res => {
                                swal.fire({
                                    position: 'center',
                                    type: res.message === true ? 'success' : 'error',
                                    title: 'Installment Deletion',
                                }).then(() => {
                                    INSTALLMENTS.datatable.ajax.reload();
                                })
                            });
                        }
                    });
            }
        };
    }();

    $(function(){
        INSTALLMENTS.init();
    });
</script>


@endsection
