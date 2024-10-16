@extends('Tenant.layouts.app')

@section('page_title')
    Create Cheque
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-cheque" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Cheque</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{route('tenant.admin.cheques.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group mb-2">
                <label>Beneficiary Account: <span class="text-danger">*<span></label>
                <select class="form-control kr-select2" data-source="CHEQUE_MODULE.beneficiaries()" name="beneficiary_id">
                    <option></option>
                </select>
                <span class="form-text text-muted">i.e. QAT global Positioning System</span>
            </div>

            <div class="form-group mb-2">
                <div class="kt-checkbox-inline mt-1">
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                        <input type="checkbox" name="guarantee"> Is Guarantee
                        <span></span>
                    </label>
                </div>
            </div>

            <div class="form-group mb-2">
                <label>Amount <span class="text-danger">*<span></label>
                <input type="number" step="0.001" required name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{old('amount')}}">
                @error('amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-2">
                <label>Date <span class="text-danger">*<span></label>
                <input type="text" required readonly name="date" data-state="date" class="kr-datepicker form-control @error('date') is-invalid @enderror" data-default="{{old('date')}}">
                @error('date')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>


            <div class="form-group">
                <label>Notes</label>
                <textarea class="form-control" rows="3" name="notes"></textarea>
                @error('notes')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter details about this cheque</span>
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
    /* We are laoding this page 2 times, so we need to include this code 1 time only */
    if(typeof CHEQUE_MODULE === "undefined"){

        var CHEQUE_MODULE={
            beneficiaries: function(){
                return @json($accounts);
            },
            container:'[id="kt-portlet__create-cheque"]:visible',
            Utils:{

                reset_page:function(){

                    $(CHEQUE_MODULE.container+' form [name=cheque_id]').remove();

                    /* clear the items */
                    $(CHEQUE_MODULE.container+' [name="date"]').val(null);
                    $(CHEQUE_MODULE.container+' [name="amount"]').val(null);
                },
            }
        };


        $(function(){



            if(typeof KINGVIEW !== "undefined"){
                /* Seems page was loaded in OnAir, reset page */
                $(CHEQUE_MODULE.container+' form').attr('action', $(CHEQUE_MODULE.container+' form').attr('data-add')).find('[name=cheque_id]').remove();
                CHEQUE_MODULE.Utils.reset_page();
            }


        });
    }
    </script>
@endsection

