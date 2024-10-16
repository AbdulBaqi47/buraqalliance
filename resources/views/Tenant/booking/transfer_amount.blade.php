@extends('Tenant.layouts.app')

@section('page_title')
    Transfer Balance
@endsection
@section('content')
    <!--begin::Portlet-->
    <div class="kt-portlet" id="kt-portlet__transfer-booking-amount" kr-ajax-content>

        <button type="button" hidden data-create-client kr-ajax-size="30%"
                kr-ajax="{{ route('tenant.admin.vehicleledger.transfer_balance', $booking->id) }}" class="btn btn-info btn-elevate btn-square">
            <i class="flaticon2-plus-1"></i>
            Transfer Balance
        </button>

        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">Transfer Balance</h3>
            </div>
            <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
        </div>
        <!--begin::Form-->
        <form class="kt-form" action="{{ route('tenant.admin.vehicleledger.transfer_balance', $booking->id) }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>Given Date:</label>
                    <input type="text" required readonly name="given_date" data-state="date" class="kr-datepicker form-control @error('given_date') is-invalid @enderror" value="{{old('given_date')}}">
                    @error('given_date')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @else
                    <span class="form-text text-muted">Please enter given date</span>
                    @enderror

                </div>

                <div class="form-group">
                    <label>Month:</label>
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
                    <label>Amount <span class="text-danger">*<span></label>
                    <input type="number" step="0.001" required name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{old('amount')}}">
                    @error('amount')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Driver:</label>
                    <div class="d-flex justify-content-between kr-input-group">
                        <select name="driver_id" class="form-control kr-select2 @error('from') is-invalid @enderror">
                            <option selected disabled></option>
                            @foreach ($booking->drivers as $driver)
                            <option value="{{$driver->id}}">{{$driver->full_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Transfer To: <span class="text-danger">*<span></label>
                    <select class="form-control kr-select2" data-source="BOOKING_OPTIONS.bookingOptions()" name="booking" required>
                        <option></option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea class="form-control" rows="3" name="description"></textarea>
                    @error('description')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @else
                    <span class="form-text text-muted">Please enter details about this expense</span>
                    @enderror

                </div>

                <div class="form-group">
                    <label>Attachment:</label>
                    <div class="kt-uppy kr-uppy uppy-invoice_tax_img" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Add Attachment" uppy-input="attachment"></div>
                    <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
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
        $(function(){
            if(typeof VEHICLE_LEDGER  !== "undefined"){
                let q = VEHICLE_LEDGER.Utils.buildQuery();
                let month = q.type === "month" ? q.value : q.value.split(',')[0];
                $('[name=month]').datepicker('update',  new Date(month).format('mmm yyyy'));
            }
        });
        var BOOKING_OPTIONS = {
            bookingOptions: function() {
                return {!! $booking_options !!};
            },
        };
    </script>
@endsection
