@extends('Tenant.layouts.app')

@section('page_title')
    {{ isset($config) ? 'Edit Sim' : 'Create Sim'}}
@endsection
@section('head')
    <style kr-ajax-head>
        .iti {
            width: 100%;
        }

        /* Adjust the width of the input field to be 100% of the ITI container */
        .iti__input {
            width: 100%;
        }
    </style>
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="{{ isset($config) ? 'kt-portlet__edit-sim-'.$config->sim->id : 'kt-portlet__create-sim'}}" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">{{ isset($config) ? 'Edit Sim' : 'Create Sim'}}</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{ isset($config) ? route('tenant.admin.sims.edit', $config->sim->id) : route('tenant.admin.sims.add') }}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group mb-2 non-walkingfields">
                <label>Number: </label>
                <input type="text" autocomplete="off" name="number" class="form-control @error('number') is-invalid @enderror" placeholder="Enter Number" value="{{ isset($config) ? $config->sim->number : old('number') }}">
                <span class="text-muted">i.e. 0551234567</span>
                @if ($errors->has('number'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ str_replace('number','Phone Number',$errors->first('number')) }}
                        </strong>
                    </span>
                @endif

            </div>


            <div class="form-group mb-2 non-walkingfields">
                <label>Operator: <span class="text-danger">*<span></label>
                <select class="form-control kr-select2" name="company">
                    <option value=""></option>
                    <option {{ isset($config) ? ($config->sim->company === 'du' ? 'selected':'') : (old('company') === 'du' ? 'selected':'' ) }} value="du">DU</option>
                    <option {{ isset($config) ? ($config->sim->company === 'etisalat' ? 'selected':'') : (old('company') === 'etisalat' ? 'selected':'' ) }} value="etisalat">Etisalat</option>
                </select>
                <span class="form-text text-muted">Select Sim Operator Name</span>
            </div>

            <div class="form-group mb-2 non-walkingfields">
                <label>Type: <span class="text-danger">*<span></label>
                <select class="form-control kr-select2" name="type">
                    <option value=""></option>
                    <option {{ isset($config) ? ($config->sim->type === 'prepaid' ? 'selected':'') : (old('type') === 'prepaid' ? 'selected':'' ) }} value="prepaid">Prepaid</option>
                    <option {{ isset($config) ? ($config->sim->type === 'postpaid' ? 'selected':'') : (old('type') === 'postpaid' ? 'selected':'' ) }} value="postpaid">Postpaid</option>
                </select>
                <span class="form-text text-muted">Select Sim Type</span>
            </div>

            <div class="form-group mb-2 non-walkingfields">
                <label>Purchasing Date:</label>
                <input type="text" placeholder="Enter Purchasing Date" required readonly name="purchasing_date" data-state="date" class="kr-datepicker form-control @error('purchasing_date') is-invalid @enderror" data-default="{{ $config?->sim?->purchasing_date ?? old('purchasing_date') }}">
                @error('purchasing_date')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                    <span class="form-text text-muted">Enter Sim Purchasing Date</span>
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
    @if (isset($config))
    var SIM_EDIT_MODULE = {
        container: '#kt-portlet__edit-sim-{{$config->sim->id}}',
        reset_page(){
            $(SIM_EDIT_MODULE.container+' [name="number"]').val(null);
            $(SIM_EDIT_MODULE.container+' [name="company"]').val(null).trigger('change.select2');
            $(SIM_EDIT_MODULE.container+' [name="type"]').val(null).trigger('change.select2');
            $(SIM_EDIT_MODULE.container+' [name="purchasing_date"]').val(null);
            kingriders.Plugins.refresh_plugins();
        }
    }
    @else
    var SIM_CREATE_MODULE = {
        container: '#kt-portlet__create-sim',
        reset_page(){
            $(SIM_CREATE_MODULE.container+' [name="number"]').val(null);
            $(SIM_CREATE_MODULE.container+' [name="company"]').val(null).trigger('change.select2');
            $(SIM_CREATE_MODULE.container+' [name="type"]').val(null).trigger('change.select2');
            $(SIM_CREATE_MODULE.container+' [name="purchasing_date"]').val(null);
            kingriders.Plugins.refresh_plugins();
        }
    }
    @endif

    /* We are laoding this page 2 times, so we need to include this code 1 time only */
    $(function(){

    });
    </script>
@endsection

