@extends('Central.layouts.app')

@section('page_title')
    Tenants
@endsection
@section('head')

@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Tenants</h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            @if ($helper_service->routes->has_access('central.admin.tenants.add'))
            <button type="button" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-submit="TENANTS.create_submit" kr-ajax-modalclosed="TENANTS.modal_closed" kr-ajax-contentloaded="Function()" kr-ajax="{{route('central.admin.tenants.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Tenant
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
                    <th>Domain</th>
                    <th>DataBase</th>
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
    var TENANTS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = TENANTS.table;

            // begin first table
            TENANTS.datatable = table.DataTable({
                responsive: true,
                lengthMenu: [5, 10, 25, 50, 100],
                pageLength: 50,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                ajax:"{{route('central.admin.tenants.data')}}",
                rowId:'id',
                columns: [
                    {data: 'id', width: "20%"},
                    {data: 'name', width: "20%"},
                    {data: 'domain_name', width: "40%"},
                    {data: 'tenancy_db_name', width: "10%"},
                    {data: 'actions', width: "10%"},
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
                            @if ($helper_service->routes->has_access('central.admin.tenants.edit'))
                                <a href="#" class="btn btn-sm btn-outline-primary btn-icon btn-icon-md" title="Edit Tenant"  kr-ajax="{{route('central.admin.tenants.edit')}}?id=${full.id}" kr-ajax-block-page-when-processing kr-ajax-size="30%" kr-ajax-submit="TENANTS.create_submit" kr-ajax-modalclosed="TENANTS.modal_closed" kr-ajax-contentloaded="Function()">
                                    <i class="la la-pencil"></i>
                                </a>
                            @endif

                            @if ($helper_service->routes->has_access('central.admin.tenants.delete'))
                                <a href="#" class="btn btn-sm btn-outline-danger btn-icon btn-icon-md" title="Delete Tenant" onclick="TENANTS.handleDeleteClick(event, this);return false;">
                                    <i class="la la-trash"></i>
                                </a>
                            @endif
                            `;
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
                if(state=='completed'){
                    // Show alert
                    swal.fire({
                        toast: true,
                        customClass: {
                            content:'mt-0 pl-2'
                        },
                        position: 'top',
                        showConfirmButton: true,
                        timer: 5000,
                        type: 'success',
                        html: `Tenant created successfully`,
                    });

                    // Reload table
                    TENANTS.datatable.ajax.reload();
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

            handleDeleteClick: function(e, self) {
                e.preventDefault();

                var rowNode = $(self).parents('tr');
                if (rowNode.hasClass('child')) { //Check if the current row is a child row
                    rowNode = rowNode.prev(); //If it is, then point to the row before it (its 'parent')
                }

                swal.fire({
                    title: 'Are you sure?',
                    text: "Database will be deleted too. You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    showLoaderOnConfirm: true,
                    scrollbarPadding: false,
                    allowOutsideClick: function() {
                        return !swal.isLoading()
                    },
                    preConfirm: function() {
                        var transaction = TENANTS.datatable.row(rowNode).data();

                        kingriders.Utils.isDebug() && console.log('deleting', transaction);

                        var url = "{{ route('central.admin.tenants.delete', '_:param') }}".replace("_:param", transaction.id);
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
                        TENANTS.datatable.row(rowNode[0]).remove();

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

        };
    }();

    $(function(){


        TENANTS.init();
    });
</script>


@endsection
