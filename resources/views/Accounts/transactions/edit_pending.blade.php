@extends('Tenant.layouts.app')

@section('page_title')
    Edit Payable
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__edit-pending" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Edit Payable</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{route('module.accounts.transactions.pending.edit', $id)}}" method="POST">
        @csrf
        <div class="kt-portlet__body">
            <div class="form-group">
                <label>Cheque Beneficiary <span class="text-danger">*</span></label>
                <select class="form-control kr-select2 @error('cheque_beneficiary') is-invalid @enderror" data-dynamic data-source="TRANSACTION_EDIT_MODULE.cheque_beneficiary()" name="cheque_beneficiary" required>
                    <option></option>
                </select>
                @error('cheque_beneficiary')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <span class="form-text text-muted">Please choose beneficiary</span>
            </div>

            <div class="form-group cheque_number">
                <label>Cheque Number:</label>
                <input type="text" class="form-control" name="cheque_number" placeholder="Cheque Number" value="{{old('cheque_number') ?? $transaction->additional_details['cheque_number'] ?? null}}">
            </div>

            <div class="form-group">
                <label>Cheque Account <span class="text-danger">*</span> </label>
                <div >
                    @include('Accounts.widgets.account_selector', ['dropdown' => true, 'name' => 'account_id', 'selected' => old('account_id') ?? $transaction->account_id])
                </div>
            </div>

            <div class="form-group">
                <label>Withdrawal Date: <span class="text-danger">*</span></label>
                <input type="text" required readonly name="date" data-name="date" data-state="date" class="rounded-0 kr-datepicker form-control @error('date') is-invalid @enderror" data-default="{{old('date') ?? \Carbon\Carbon::parse($transaction->time)->format('Y-m-d')}}">
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
        container:'#kt-portlet__edit-pending',
        cheque_beneficiary(){
            return @json($cheque_beneficiary)
        },
        Utils:{

            reset_page:function(){
                /* change the action of form to edit */
                $('#kt-portlet__edit-pending form [name=transaction_id]').remove();
                if(TRANSACTION_EDIT_MODULE.transaction){
                    $('#kt-portlet__edit-pending form').prepend('<input type="hidden" name="transaction_id" value="'+TRANSACTION_EDIT_MODULE.transaction.id+'" />');

                    /* clear the items */
                    $('#kt-portlet__edit-pending [name="amount"]').val(TRANSACTION_EDIT_MODULE.transaction.amount);
                    var date = new Date(TRANSACTION_EDIT_MODULE.transaction.time).format('mmmm dd, yyyy');
                    $('#kt-portlet__edit-pending [name="date"]').attr('data-default', date).datepicker('update', date);
                    $('#kt-portlet__edit-pending [name="description"]').val(TRANSACTION_EDIT_MODULE.transaction.description);
                }
            }
        }
    };


    $(function(){

        var transaction_loaded = {!! $transaction !!};
        if(!transaction_loaded.additional_details.is_cheque){
            $('.cheque_number').hide();
        }
        $(TRANSACTION_EDIT_MODULE.container+' [name="cheque_beneficiary"]').val(`{{ old('cheque_beneficiary') ?? $transaction->additional_details['cheque_beneficiary'] ?? null }}`).trigger('change.select2');

        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            TRANSACTION_EDIT_MODULE.Utils.reset_page();
        }


    });
    </script>
@endsection

