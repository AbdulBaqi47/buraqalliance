@extends('Tenant.layouts.app')

@section('page_title')
   {{ $state === 'cr' ? "Received" : "Pay" }} {{ $transaction->id }}
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__pay-accounttransaction" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">{{ $state === 'cr' ? "Receive" : "Pay" }} Amount - {{ $transaction->id }}</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" enctype="multipart/form-data" action="{{route('accounts.transaction.pending.pay', $transaction->id)}}" method="POST">
        @csrf

        <input type="hidden" name="state" value="{{ $state }}">

        <div class="kt-portlet__body">

            @if ($state === 'cr')
            {{-- ------------------ --}}
            {{-- "Receivable Amount"--}}
            {{-- ------------------ --}}

            <div class="form-group">
                <label>Receive Date <span class="text-danger">*<span></label>
                <input type="text" required readonly name="date" data-state="date" class="kr-datepicker form-control @error('date') is-invalid @enderror" data-default="{{ Carbon\Carbon::parse($transaction->time)->format('Y-m-d') }}">
                @error('date')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Receive Account <span class="text-danger">*<span> </label>
                <div >
                    @include('Accounts.widgets.account_selector', ['dropdown' => true, 'name' => 'account_id', 'selected' => old('account_id') ?? $transaction->account_id])
                </div>
            </div>

            <div class="form-group">
                <label>Amount <span class="text-danger">*<span></label>
                <input type="number" step="0.001" required name="amount" class="form-control @error('amount') is-invalid @enderror" value="">
                @error('amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                @isset($transaction->real_amount)
                <span class="form-text text-muted">Actual Amount: <b>{{$transaction->real_amount}}</b></span>
                @endisset
            </div>


            @else

            {{-- ------------------ --}}
            {{--  "Payable Amount"  --}}
            {{-- ------------------ --}}

            <div class="form-group">
                <label>Pay Date <span class="text-danger">*<span></label>
                <input type="text" required readonly name="date" data-state="date" class="kr-datepicker form-control @error('date') is-invalid @enderror" data-default="{{ Carbon\Carbon::parse($transaction->time)->format('Y-m-d') }}">
                @error('date')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Pay Account <span class="text-danger">*<span> </label>
                <div >
                    @include('Accounts.widgets.account_selector', ['dropdown' => true, 'selected' => old('account_id') ?? $transaction->account_id])
                </div>
            </div>

            <div class="form-group">
                <label>Amount <span class="text-danger">*<span></label>
                <input type="number" kr-accounts-input step="0.001" required name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ $transaction->amount }}">
                @error('amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                @isset($transaction->real_amount)
                <span class="form-text text-muted">Actual Amount: <b>{{ $transaction->real_amount }}</b></span>
                @endisset
            </div>


            @endif


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
    $(function(){


    });

    var PAY_ACCOUNT_TRANSACTION_MODULE = {

        container:'#kt-portlet__pay-accounttransaction'

    };
    </script>
@endsection

