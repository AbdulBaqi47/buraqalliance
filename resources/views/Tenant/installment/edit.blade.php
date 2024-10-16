@extends('Tenant.layouts.app')

@section('page_title')
    Edit Installment
@endsection
@section('content')
    <!--begin::Portlet-->
    <div class="kt-portlet" id="kt-portlet__edit-installment" kr-ajax-content>
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">Edit Installment</h3>
            </div>
            <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
        </div>
        <!--begin::Form-->
        <form class="kt-form" enctype="multipart/form-data" action="{{ route('tenant.admin.installment.edit', $installment->id) }}"
            method="POST">
            @csrf
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>Charge Amount <span class="text-danger">*<span></label>
                    <input type="text" autocomplete="off" name="charge_amount"
                        class="form-control @error('charge_amount') is-invalid @enderror" required placeholder="Enter Charge Amount"
                        value="{{ $installment->charge_amount }}">
                </div>
                <div class="form-group">
                    <label>Pay Amount <span class="text-danger">*<span></label>
                    <input type="text" autocomplete="off" name="pay_amount"
                        class="form-control @error('pay_amount') is-invalid @enderror" required placeholder="Enter Pay Amount"
                        value="{{ $installment->pay_amount }}">
                </div>
                <div class="form-group">
                    <label>Charge Date </label>
                    <input class="kr-datepicker form-control @error('charge_date') is-invalid @enderror" type="text" required readonly name="charge_date" data-state="date" data-default="{{ $installment->charge_date }}">
                    @if ($errors->has('charge_date'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('charge_date') }}
                            </strong>
                        </span>
                    @endif

                </div>
                <div class="form-group">
                    <label>Pay Date </label>
                    <input class="kr-datepicker form-control @error('pay_date') is-invalid @enderror" type="text" required readonly name="pay_date" data-state="date" data-default="{{ $installment->pay_date }}">
                    @if ($errors->has('pay_date'))
                        <span class="invalid-response text-danger" role="alert">
                            <strong>
                                {{ $errors->first('pay_date') }}
                            </strong>
                        </span>
                    @endif

                </div>
            </div>
            <div class="kt-portlet__foot kt-portlet__foot--solid">
                <div class="kt-form__actions kt-form__actions--right">
                    <button id="submitEditButton" type="submit" class="btn btn-brand">Save</button>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>

    <!--end::Portlet-->
@endsection

@section('foot')
    {{-- ----------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    ------------------------------------------------------------------------------ --}}
    <script kr-ajax-head type="text/javascript">
        var INSTALLMENT_EDIT_MODULE = {
            container: $('#kt-portlet__edit-installment'),
        };
    </script>
@endsection
