@extends('Tenant.layouts.app')

@section('page_title')
{{ $investor->name }} manages to
@endsection
@section('content')
    <!--begin::Portlet-->
    <div class="kt-portlet" id="kt-portlet__change_invesstor_manager" kr-ajax-content>

        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">{{ $investor->name }} manages to</h3>
            </div>
            <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
        </div>
        <!--begin::Form-->
        <form class="kt-form" action="{{ route('tenant.admin.investors.change_manager', $investor->id) }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="kt-portlet__body">

                <div class="form-group">
                    <label>Manages To:</label>
                    <select class="form-control kr-select2" multiple data-source="CHANGE_INVESTOR_MANAGER.statusOptions()" name="manager_ids[]">
                        <option></option>
                    </select>
                    <span class="text-muted form-text"> <b>{{ $investor->name }}</b> can view selected investors data too </span>
                </div>
            </div>

            <div class="kt-portlet__foot">
                <div class="kt-form__actions kt-form__actions--right">
                    <button type="submit" class="btn btn-primary">Save</button>
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
        var CHANGE_INVESTOR_MANAGER = {
            statusOptions: function() {
                return {!! $investorOptions !!};
            },
        };
    </script>
@endsection
