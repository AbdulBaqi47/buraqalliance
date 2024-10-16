@extends('Tenant.layouts.app')

@section('page_title')
    Edit Addon
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__edit-addon" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Edit Addon
                <small>{{ $addon->id }} / {{ $addon->setting->title }}</small>
            </h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{route('tenant.admin.addons.single.edit', $addon->id)}}" method="POST">
        @csrf
        <div class="kt-portlet__body">
            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="1" class="form-control" name="amount" required placeholder="Enter Amount" value="{{$addon->price}}">
            </div>
            @if (!isset($addon->source_id))
                <input type="hidden" name="vehicle_type">
                <div class="form-group">
                    <label>Source:</label>
                    <select class="form-control kr-select2" data-source="ADDON_EDIT_MODULE.sources()" name="source_id">
                        <option></option>

                    </select>
                </div>
            @endif
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


    var ADDON_EDIT_MODULE={
        ledger:null,
        all_sources: {!! $sources !!},
        sources(){
            return ADDON_EDIT_MODULE.all_sources;
        },
        container:'#kt-portlet__edit-addon'
    };

    $(function(){
        @if($addon->source_type === 'vehicle')
            /* Change of source [Vehicle only] Add type field */
            $(ADDON_EDIT_MODULE.container+' [name="source_id"]').on('change', function(e){
                // debugger;
                var text = $(this).find(':selected').text();
                var parent = this.closest('tr');
                var vehicleTypeEl = $(ADDON_EDIT_MODULE.container+' [name="vehicle_type"]');
                if(vehicleTypeEl.length > 0){
                    // If we found "B#" in value, it is booking else vehicle
                    // Since all bookings are prefixed with B# and vehicles are with V#
                    // If any vehicle not assigned to any booking means TMP vehicle
                    // then else case will be executed since they has no prefixed with them
                    vehicleTypeEl.val( text.indexOf('B#') > -1 ? 'booking' : 'vehicle' );
                }
            });
        @endif
    })
    </script>
@endsection

