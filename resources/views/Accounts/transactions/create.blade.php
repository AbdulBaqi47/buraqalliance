@extends('Tenant.layouts.app')

@section('page_title')
    Add Transaction
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-transaction" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Add Transaction</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" enctype="multipart/form-data" data-add="{{route('module.accounts.transactions.add', $account_id)}}" data-edit="{{route('module.accounts.transactions.add', $account_id)}}"  action="{{route('module.accounts.transactions.add', $account_id)}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group">
                <label>Description:</label>
                <textarea class="form-control" rows="3" name="description"></textarea>
                @error('description')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter details about this transaction</span>
                @enderror
                
            </div>

            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="0.001" autocomplete="off" required name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{old('amount')}}">
                @error('amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                
            </div>
        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="submit" class="btn btn-brand">Save</button>
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
    

        var TRANSACTION_MODULE={
            container:'#kt-portlet__create-transaction',
            Utils:{

                reset_page:function(){
                    
                    /* clear the items */
                
                    $(TRANSACTION_MODULE.container+' form [name="description"]').val(null);
                    $(TRANSACTION_MODULE.container+' form [name="amount"]').val(null).trigger('change');
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


        $(function(){

            autosize($(TRANSACTION_MODULE.container+' form [name="description"]'));
        });
    </script>
@endsection

