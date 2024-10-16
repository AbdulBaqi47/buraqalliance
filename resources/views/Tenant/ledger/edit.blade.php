@extends('Tenant.layouts.app')

@section('page_title')
    Edit Ledger
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__edit-ledger" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Edit Ledger</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{route('tenant.admin.ledger.edit')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="effect-all-container" style="display: none;">

                <div class="form-group">
                    <div class="kt-checkbox-list">
                        <label class="kt-checkbox kt-checkbox--brand">
                            <input type="checkbox" name="effect_all"> Affect related entries?
                            <span></span>
                        </label>
                    </div>
                    <span class="form-text text-muted">Do you want to effect entries relation to this amount? like entries from vehicle statements</span>
                </div>

            </div>

            <div class="form-group">
                <label>Date <span class="text-danger">*<span></label>
                <input type="text" required readonly name="date" data-state="date" class="kr-datepicker form-control @error('date') is-invalid @enderror" value="{{old('date')}}">
                @error('date')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter date</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Month <span class="text-danger">*<span></label>
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
                <label>Amount:</label>
                <input type="number" step="0.001" class="form-control" name="amount" placeholder="Enter Amount" value="">
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


    var LEDGER_EDIT_MODULE={
        ledger:null,
        container:'#kt-portlet__edit-ledger',
        Utils:{

            reset_page:function(){
                /* change the action of form to edit */
                $('#kt-portlet__edit-ledger form [name=ledger_id]').remove();
                if(LEDGER_EDIT_MODULE.ledger){
                    $('#kt-portlet__edit-ledger form').prepend('<input type="hidden" name="ledger_id" value="'+LEDGER_EDIT_MODULE.ledger.id+'" />');

                    $('#kt-portlet__edit-ledger form .effect-all-container').show().find('[type="checkbox"]').prop('checked', true);
                    if(LEDGER_EDIT_MODULE.ledger.tag === "transaction_ledger" || LEDGER_EDIT_MODULE.ledger.tag === "client_income"){

                        $('#kt-portlet__edit-ledger form .effect-all-container').hide().find('[type="checkbox"]').prop('checked', false);

                    }

                    $('#kt-portlet__edit-ledger [name="date"]').datepicker('update', moment(LEDGER_EDIT_MODULE.ledger.date).format('MMMM DD, YYYY'));
                    $('#kt-portlet__edit-ledger [name="month"]').datepicker('update', moment(LEDGER_EDIT_MODULE.ledger.month).format('MMM YYYY'));
                    $('#kt-portlet__edit-ledger [name="amount"]').val(LEDGER_EDIT_MODULE.ledger.amount);
                }
                else{

                    /* clear the items */

                    $('#kt-portlet__edit-ledger [name="date"]').val(null);
                    $('#kt-portlet__edit-ledger [name="month"]').val(null);
                    $('#kt-portlet__edit-ledger [name="amount"]').val(null);

                }

            }
        }
    };


    $(function(){

        /* Check if page has config, do accordingly */
        @isset($config)
        /* This will help us in loading page as edit & view */
        @isset($config->ledger)
        var _DataLoaded = {!! $config->ledger !!};
        LEDGER_EDIT_MODULE.ledger = _DataLoaded;
        @endisset

        @endisset

        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            LEDGER_EDIT_MODULE.Utils.reset_page();
        }


    });
    </script>
@endsection

