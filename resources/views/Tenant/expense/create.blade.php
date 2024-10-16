@extends('Tenant.layouts.app')

@section('page_title')
    Create Company Expense
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-expense" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Company Expense</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" enctype="multipart/form-data" data-add="{{route('tenant.admin.expense.add')}}" data-edit="{{route('tenant.admin.expense.edit')}}"  action="{{route('tenant.admin.expense.add')}}" method="POST">
        @csrf
        <input type="hidden" name="ig_tag" value="expense">
        <div class="kt-portlet__body">
            <div class="form-group">
                <label>Given Date:</label>
                <input type="text" required readonly name="given_date" data-state="date" class="kr-datepicker form-control @error('given_date') is-invalid @enderror" value="{{old('given_date')}}">
                @error('name')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter given date</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Month:</label>
                <input type="text" required readonly name="month" data-state="month" class="kr-datepicker form-control @error('month') is-invalid @enderror" value="{{old('month')}}">
                @error('month')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter month</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Type:</label>
                <select class="form-control kr-select2" data-dynamic data-source="EXPENSE_MODULE.types()" name="type" required>
                    <option></option>
                </select>
                @error('type')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please choose category</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea class="form-control" rows="3" name="description"></textarea>
                @error('description')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter details about this expense</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="0.001" kr-accounts-input required name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{old('amount')}}">
                @error('amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>

            <div class="form-group">
                <div class="kt-checkbox-inline mt-1">
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                        <input type="checkbox" name="has_tax"> Has tax invoice?
                        <span></span>
                    </label>
                </div>

            </div>

            <div class="form-group taxable-field" style="display:none;">
                <label>Tax Amount:</label>
                <input type="number" step="0.001" name="invoice_tax_amount" class="form-control @error('invoice_tax_amount') is-invalid @enderror" value="{{old('invoice_tax_amount')}}">
                @error('invoice_tax_amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter total tax amount</span>
                @enderror
            </div>

            <div class="form-group taxable-field" style="display:none;">
                <label>Invoice id:</label>
                <input type="number" name="invoice_tax_id" class="form-control @error('invoice_tax_id') is-invalid @enderror" value="{{old('invoice_tax_id')}}">
                @error('invoice_tax_id')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter invoice id mentioned in invoice</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Attachment:</label>
                <div class="kt-uppy kr-uppy uppy-invoice_tax_img" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Add Attachment" uppy-input="invoice_tax_img"></div>
                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
            </div>


        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    @include('Accounts.widgets.account_selector')
                </div>
                <div class="d-flex">
                    <div class="kt-form__actions kt-form__actions--right">
                        <button type="submit" class="btn btn-brand">Save</button>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!--end::Form-->
</div>

<!--end::Portlet-->


@endsection

@section('foot')
    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">
    $(function(){
        $('#kt-portlet__create-expense [name=has_tax]').on('change', function(){
            var is_checked = $(this).is(':checked');

            if(is_checked){
                /* hide the other fields */
                $('#kt-portlet__create-expense').find('.taxable-field').show();
            }
            else{
                /* show other fields */
                $('#kt-portlet__create-expense').find('.taxable-field').hide();
            }
        });
    });

    var EXPENSE_MODULE={
        all_types:{!! $types !!},
        types:function(){
            return EXPENSE_MODULE.all_types;
        },
        container:'#kt-portlet__create-expense',
        Utils:{

            reset_page:function(){

                /* clear the items */
                $(EXPENSE_MODULE.container+' form [name=given_date]').datepicker('update', new Date(Date.now()).format('mmmm dd, yyyy'));
                $(EXPENSE_MODULE.container+' form [name="month"]').datepicker('update', new Date(Date.now()).format('mmm yyyy'));
                $(EXPENSE_MODULE.container+' form [name="type"]').val(null).trigger('change.select2');
                $(EXPENSE_MODULE.container+' form [name="description"]').val(null);
                $(EXPENSE_MODULE.container+' form [name="amount"]').val(null).trigger('change');
                $(EXPENSE_MODULE.container+' form [name="has_tax"]').prop('checked', false).trigger('change');
                $(EXPENSE_MODULE.container+' form [name="invoice_tax_amount"]').val(null);
                $(EXPENSE_MODULE.container+' form [name="invoice_tax_id"]').val(null);
                $(EXPENSE_MODULE.container+' form [name="invoice_tax_amount"]').val(null);

                var uppy = kingriders.Plugins.uppy.getInstance($(EXPENSE_MODULE.container+' form .uppy-invoice_tax_img').attr('id'));
                if(uppy){
                    /* remove all files */
                    $('.kt-uppy__list').html('');
                    $('input[type=file].kr-uppy-file').remove();

                    uppy.reset();
                }

            },
            load_page:function(client){
                return; /* NEED TO SET */
                /* Load the job in page (this funtion is using in view job page) */

                /* need to check if job is suitable for edit, (not in creating process) */
                if(client.actions.status==1){
                    /* check if page if loaded in modal */
                    var MODAL = $('#kt-portlet__create-client').parents('.modal');
                    if(MODAL.length){
                        MODAL.modal('show');
                    }

                    /* change the action of form to edit */
                    $('#kt-portlet__create-client form [name=client_id]').remove();
                    $('#kt-portlet__create-client form').attr('action', $('#kt-portlet__create-client form').attr('data-edit'))
                    .prepend('<input type="hidden" name="client_id" value="'+client.id+'" />');


                    /* load other data like bike,client */
                    $('#kt-portlet__create-client [name="name"]').val(client.name);
                    $('#kt-portlet__create-client [name="email"]').val(client.email);
                    var is_walking=false;
                    if(client.walking_customer == 1)is_walking=true;
                    $('#kt-portlet__create-client [name="walking_customer"]').prop('checked', is_walking).trigger('change');
                    $('#kt-portlet__create-client [name="phone"]').val(client.phone);
                    $('#kt-portlet__create-client [name="trn"]').val(client.trn);
                    $('#kt-portlet__create-client [name="address"]').val(client.address);
                }
                else{
                    /* cannot laod the job now */
                    swal.fire({
                        position: 'center',
                        type: 'error',
                        title: 'Cannot load client',
                        html: 'client is processing.. Please retry after some time',
                    });
                }
                kingriders.Utils.isDebug() && console.log('loaded_client', client);
            },
        }
    };
    </script>
@endsection

