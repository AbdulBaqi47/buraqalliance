@extends('Tenant.layouts.app')

@section('page_title')
    Edit Transaction
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__edit-transaction" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Edit Transaction</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{route('module.accounts.transactions.edit', $id)}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group from_account" hidden>
                <label>From Account <span class="text-danger">*</span> </label>
                <div >
                    @include('Accounts.widgets.account_selector', ['dropdown' => true, 'name' => 'from_account'])
                </div>
            </div>

            <div class="form-group to_account" hidden>
                <label>To Account <span class="text-danger">*</span> </label>
                <div >
                    @include('Accounts.widgets.account_selector', ['dropdown' => true, 'name' => 'to_account'])
                </div>
            </div>

            <div class="form-group">
                <label>Date:</label>
                <input type="text" required readonly name="date" data-name="date" data-state="date" class="rounded-0 kr-datepicker form-control @error('date') is-invalid @enderror" value="{{old('date')}}">
            </div>

            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="0.001" class="form-control" name="amount" placeholder="Enter Amount" value="">
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea class="form-control" rows="3" name="description">{{old('description')}}</textarea>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger error-alert m-0 mt-3 rounded-0 alert-edt-trx" role="alert" style="display: none;">
                        <div class="alert-text alert-text-edt-trx"></div>
                    </div>
                </div>
            </div>

        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid py-2">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="submit" class="btn btn-brand">Update</button>
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


    var TRANSACTION_EDIT_MODULE={
        transaction:null,
        container:'#kt-portlet__edit-transaction',
        Utils:{
            reset_page:function(){
                let from_account_el = $(TRANSACTION_EDIT_MODULE.container + ' [name=from_account]')
                let to_account_el = $(TRANSACTION_EDIT_MODULE.container + ' [name=to_account]')
                let transaction = TRANSACTION_EDIT_MODULE.transaction?.links?.find(item => item.subject_type === "App\\Accounts\\Models\\Account_transaction");
                $(TRANSACTION_EDIT_MODULE.container + ' .from_account').attr('hidden', TRANSACTION_EDIT_MODULE.transaction?.tag !== "transfer");
                $(TRANSACTION_EDIT_MODULE.container + ' .to_account').attr('hidden', TRANSACTION_EDIT_MODULE.transaction?.tag !== "transfer");
                from_account_el.attr('disabled', TRANSACTION_EDIT_MODULE.transaction?.tag !== "transfer")
                to_account_el.attr('disabled', TRANSACTION_EDIT_MODULE.transaction?.tag !== "transfer")
                if(!!transaction){
                    if(TRANSACTION_EDIT_MODULE.transaction.type === 'dr'){
                        // if Transaction type is Debit
                        // cash is sent from this account
                        from_account_el.val(TRANSACTION_EDIT_MODULE.transaction.account_id).trigger('change')
                        to_account_el.val(transaction.source.account_id).trigger('change')
                    }else{
                        // if Transaction type is Credit
                        // Cash is Received in this account
                        from_account_el.val(transaction.source.account_id).trigger('change')
                        to_account_el.val(TRANSACTION_EDIT_MODULE.transaction.account_id).trigger('change')
                    }
                }
                /* change the action of form to edit */
                $('#kt-portlet__edit-transaction form [name=transaction_id]').remove();
                if(TRANSACTION_EDIT_MODULE.transaction){
                    $('#kt-portlet__edit-transaction form').prepend('<input type="hidden" name="transaction_id" value="'+TRANSACTION_EDIT_MODULE.transaction.id+'" />');

                    /* clear the items */
                    $('#kt-portlet__edit-transaction [name="amount"]').val(TRANSACTION_EDIT_MODULE.transaction.amount);
                    var date = new Date(TRANSACTION_EDIT_MODULE.transaction.time).format('mmmm dd, yyyy');
                    $('#kt-portlet__edit-transaction [name="date"]').attr('data-default', date).datepicker('update', date);
                    $('#kt-portlet__edit-transaction [name="description"]').val(TRANSACTION_EDIT_MODULE.transaction.description);
                }
            }
        }
    };


    $(function(){

        /* Check if page has config, do accordingly */
        @isset($config)
        /* This will help us in loading page as edit & view */
        @isset($config->transaction)
        var _DataLoaded = {!! $config->transaction !!};
        TRANSACTION_EDIT_MODULE.transaction = _DataLoaded;
        @endisset

        @endisset

        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            TRANSACTION_EDIT_MODULE.Utils.reset_page();
        }
        function enableButtonHideAlerts(btn, alert, alert_parent){
            btn.prop('disabled', false);
            alert.html('').hide()
            alert_parent.hide()
        }
        function disableButtonShowAlerts(btn, alert, alert_parent, html){
            btn.prop('disabled', true);
            alert.html(html).show()
            alert_parent.show()
        }
        function doValidations(){
            let from_account_el = $(TRANSACTION_EDIT_MODULE.container + ' [name=from_account]')
            let to_account_el = $(TRANSACTION_EDIT_MODULE.container + ' [name=to_account]')
            let btn = $(TRANSACTION_EDIT_MODULE.container + ' form button');
            let alert = $(TRANSACTION_EDIT_MODULE.container + ' .alert-text-edt-trx')
            let alert_parent = $(TRANSACTION_EDIT_MODULE.container + ' .alert-edt-trx')
            if(!$(TRANSACTION_EDIT_MODULE.container + ' .from_account').attr('hidden') && !$(TRANSACTION_EDIT_MODULE.container + ' .to_account').attr('hidden')){
                if(from_account_el.val() === to_account_el.val()){
                    disableButtonShowAlerts(btn, alert, alert_parent, 'Transfer To Same Account is Not Allowed')
                }else{
                    enableButtonHideAlerts(btn, alert, alert_parent)
                }
            }else{
                enableButtonHideAlerts(btn, alert, alert_parent)
            }
        }
        $(document).on('change', TRANSACTION_EDIT_MODULE.container + ' [name=from_account]', doValidations)
        $(document).on('change', TRANSACTION_EDIT_MODULE.container + ' [name=to_account]', doValidations)
    });
    </script>
@endsection

