@php
    $negativeAccess = $helper_service->helper->negativeBalanceData();
@endphp

@extends('Tenant.layouts.app')

@section('page_title')
    Transfer
@endsection

@section('head')
<style kr-ajax-head>
    /*Fix Select2 Container*/
    .kr-input-group >
    .select2-container > .selection > .select2-selection--single {
        border-radius: 0;
    }
</style>
@endsection

@section('content')


<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-transfer" kr-ajax-content>


    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Transfer Amount</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{route('module.accounts.transfer.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <div class="alert-text">
                        <h4 class="alert-heading">Errors!</h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            @endif
            <div class="form-group">
                <label>Transfer From:</label>
                <div class="d-flex justify-content-between kr-input-group">
                    <select name="from" data-width="75%" required class="form-control kr-select2 @error('from') is-invalid @enderror" onchange="TRANSFER_MODULE.Utils.update_balance(this);">

                        {{-- ---------------------------- --}}
                        {{-- Loop through each department --}}
                        {{-- ---------------------------- --}}

                        @foreach ($self_accounts as $depname => $accounts)

                        @php
                            $dep = $depname;
                            if($depname=='bank')$dep='Bank Accounts';
                            if($depname=='cih')$dep='Cash in Hand Accounts';
                        @endphp
                        <optgroup label="{{$dep}}">
                            {{-- -------------------------- --}}
                            {{-- Loop through each accounts --}}
                            {{-- -------------------------- --}}
                            @foreach ($accounts as $account)
                            <option value="{{$account->id}}">{{$account->title}}</option>
                            @endforeach
                        </optgroup>

                        @endforeach
                    </select>
                    <input type="number" class="form-control rounded-0 w-25 h-auto py-0 inp-balance" readonly value="0">


                </div>




            </div>
            <div class="form-group">
                <label>Transfer To:</label>
                <div class="d-flex justify-content-between kr-input-group">
                    <select name="to" data-width="75%" required class="form-control kr-select2 @error('to') is-invalid @enderror" onchange="TRANSFER_MODULE.Utils.update_balance(this);">
                        <option selected disabled></option>
                        {{-- ---------------------------- --}}
                        {{-- Loop through each department --}}
                        {{-- ---------------------------- --}}

                        @foreach ($all_accounts as $depname => $accounts)

                        @php

                            $dep = $depname;
                            if($depname=='bank')$dep='Bank Accounts';
                            if($depname=='cih')$dep='Cash in Hand Accounts';
                        @endphp
                        <optgroup label="{{$dep}}">
                            {{-- -------------------------- --}}
                            {{-- Loop through each accounts --}}
                            {{-- -------------------------- --}}
                            @foreach ($accounts as $account)
                            <option value="{{$account->id}}">{{$account->title}}</option>
                            @endforeach
                        </optgroup>

                        @endforeach
                    </select>
                    <input type="number" class="form-control rounded-0 w-25 h-auto py-0 inp-balance" readonly value="0">
                </div>

            </div>

            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="0.001" required name="amount" class="form-control h-auto rounded-0 @error('amount') is-invalid @enderror" oninput="TRANSFER_MODULE.Utils.handle_input(this);" value="{{old('amount')}}">


            </div>

            <div class="form-group">
                <label>Date:</label>
                <input type="text" required readonly name="date" data-default="" data-name="date" data-state="date" class="rounded-0 kr-datepicker form-control @error('date') is-invalid @enderror" value="{{old('date')}}">

            </div>

            <div class="form-group">
                <label>Description: (Optional)</label>
                <textarea class="form-control" rows="3" name="description"></textarea>
                @error('description')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                @enderror
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger error-alert m-0 mt-3 rounded-0" role="alert" style="display: none;">
                        <div class="alert-text"></div>
                    </div>
                </div>
            </div>


        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid py-2">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="button" class="btn btn-brand" onclick="TRANSFER_MODULE.Utils.handle_submit(this); return false;">Save</button>
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

        });

        if(typeof TRANSFER_MODULE==="undefined"){
            var TRANSFER_MODULE={
                Utils:{

                    reset_page:function(model){

                        var container = model;

                        container.find('[name="from"]').trigger('change');
                        container.find('[name="to"]').trigger('change');

                    },
                    update_balance:function(self){

                        var container = $(self).parents('.krajax-modal');

                        /* It seems page ins't loaded on modal */
                        if(container.length==0)container = $('#kt-portlet__create-transfer');

                        var account_id = self.value;

                        // Check if Both Values Are Same Then Add Alert And Disable Submit
                        let btn = $('#kt-portlet__create-transfer form button');
                        let to_val = $('#kt-portlet__create-transfer form [name=to] option:selected').val();
                        let from_val = $('#kt-portlet__create-transfer form [name=from] option:selected').val();
                        let alert = $('.alert-text');
                        let alert_parent = $('[role=alert]');
                        if(to_val === from_val){
                            btn.prop('disabled', true);
                            alert.html('Transfer To Same Account is Not Allowed').show()
                            alert_parent.show()
                        }else{
                            btn.prop('disabled', false);
                            alert.html('').hide()
                            alert_parent.hide()
                        }

                        /* Find account */
                        $(self).parents('.kr-input-group').find('.inp-balance').val(0);
                        var account=TRANSFER_MODULE.accounts.find(function (x){ return x._id===account_id });
                        if(typeof account !== "undefined" && account){
                            var balance = account.balance;

                            $(self).parents('.kr-input-group').find('.inp-balance').val(balance);
                        }

                        TRANSFER_MODULE.Utils.handle_input(container.find('[name="amount"]')[0]);
                    },
                    handle_input:function(input){



                        var container = $(input).parents('.krajax-modal');

                        /* It seems page ins't loaded on modal */
                        if(container.length==0)container = $('#kt-portlet__create-transfer');

                        var account_from = container.find('[name="from"]').val();

                        /* Find account */
                        var account=TRANSFER_MODULE.accounts.find(function (x){ return x._id===account_from });
                        if(typeof account !== "undefined" && account){

                            //  ------------------------
                            //  Negative Balance Access
                            //  ------------------------
                            var validateBalance = function(){
                                var balance = account.balance;

                                /* validate balance */
                                var amount = parseFloat(container.find('[name="amount"]').val())||0;

                                var amountChanged=null;
                                if(amount>balance)amountChanged=balance;
                                else if(amount<0)amountChanged=0;


                                if(amountChanged!=null)container.find('[name="amount"]').val(amountChanged);

                            }


                            @unless ($helper_service->helper->isSuperUser() || $negativeAccess->all === true)


                                // Check if selected account has access
                                // only then we need to apply amount restrictions
                                let granted_account_ids = @json($negativeAccess->ids);

                                if(!granted_account_ids.includes(account._id)){
                                    validateBalance();
                                }


                            @endunless

                        }



                    },
                    handle_submit:function(self){
                        var container = $(self).parents('.krajax-modal');

                        /* It seems page ins't loaded on modal */
                        if(container.length==0)container = $('#kt-portlet__create-transfer');

                        var form = container.find('form');

                        /* Basic validation */
                        var amount = parseFloat(form.find('[name="amount"]').val())||0;

                        var is_valid = true;
                        if(amount<=0){
                            container.find('.error-alert').show().find('.alert-text').show().html("Amount must be greater than zero.");
                            is_valid = false;
                        }

                        /* check if same account selected */
                        var account_from = container.find('[name="from"]').val();
                        var account_to = container.find('[name="to"]').val();

                        if(account_from==account_to){
                            container.find('.error-alert').show().find('.alert-text').show().html("Cannot transfer to same account.");
                            is_valid = false;
                        }

                        if(is_valid){
                            form.trigger('submit');
                        }
                    }
                },
            };
        }


        /* Update accounts */
        TRANSFER_MODULE.accounts = {!! $accountsData !!};
    </script>
@endsection

