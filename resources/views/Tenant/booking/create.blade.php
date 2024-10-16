@extends('Tenant.layouts.app')

@section('page_title')
@isset($config->booking) #{{ $config->booking->id }} Edit @else Create @endisset Booking
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet mt-5" id="kt-portlet__create-booking" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">@isset($config->booking) #{{ $config->booking->id }} Edit @else Create @endisset Booking</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>

    <button type="button" hidden data-create-investor kr-ajax-size="50%" kr-ajax-modalclosed="BOOKING_MODULE.investor_module.modal_closed" kr-ajax-submit="BOOKING_MODULE.investor_module.form_submit" kr-ajax-contentloaded="BOOKING_MODULE.investor_module.form_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.investors.add')}}" class="btn btn-info btn-elevate btn-square">
        <i class="flaticon2-plus-1"></i>
        Create Investor
    </button>

    <button type="button" hidden data-create-vehicletype kr-ajax-size="30%" kr-ajax-modalclosed="BOOKING_MODULE.vehicletype_module.modal_closed" kr-ajax-submit="BOOKING_MODULE.vehicletype_module.form_submit" kr-ajax-contentloaded="BOOKING_MODULE.vehicletype_module.form_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.vehicletypes.add')}}" class="btn btn-info btn-elevate btn-square">
        <i class="flaticon2-plus-1"></i>
        Create Vehicle Type
    </button>

    <!--begin::Form-->
    <form class="kt-form" enctype="multipart/form-data" data-add="{{route('tenant.admin.bookings.add')}}" data-edit="{{route('tenant.admin.bookings.edit')}}"  action="{{route('tenant.admin.bookings.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <input type="hidden" name="preview" value="0">

            <div class="form-group">
                <label>Investor <span class="text-danger">*<span></label>
                <select class="form-control kr-select2" data-dynamic data-source="BOOKING_MODULE.investors()" name="investor_id" required>
                    <option></option>
                </select>
                @error('investor_id')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>

            <div class="form-group">
                <label>Vehicle Type <span class="text-danger">*<span></label>
                <select class="form-control kr-select2" data-dynamic data-source="BOOKING_MODULE.vehicle_types()" name="vehicletype_id" required>
                    <option></option>
                </select>
                @error('vehicletype_id')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>

            <div class="form-group">
                <label>Account <span class="text-danger">*<span> </label>
                <div >
                    @include('Accounts.widgets.account_selector', ['dropdown' => true, 'name' => 'account_id', 'selected' => old('account_id')])
                </div>
                @error('account_id')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>

            <div class="form-group">
                <label>Initial Deposit Amount (AED) </label>
                <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{old('amount')}}">
                @error('amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>

            <div class="form-group">
                <label> Estimated Delivery Date <span class="text-danger">*<span> </label>
                <input type="text" required readonly name="delivery_date" data-state="date" class="kr-datepicker form-control @error('delivery_date') is-invalid @enderror" data-default="{{old('delivery_date')}}">
                @error('delivery_date')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter delivery date</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Other Details</label>
                <textarea class="form-control" rows="3" name="notes">{{old('notes')}}</textarea>
                @error('notes')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter details about this</span>
                @enderror

            </div>

        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="submit" class="btn btn-brand">Preview</button>
            </div>

        </div>

    </form>

    <!--end::Form-->

    <div class="modal fade" id="previewModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalBackdropStatic" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
                            <h6>Investor</h6>
                        </div>
                        <div class="col-md-8">
                            <h5 data-preview="investor_id"></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Vehicle Type</h6>
                        </div>
                        <div class="col-md-8">
                            <h5 data-preview="vehicletype_id"></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Initial Deposit Amount (AED)</h6>
                        </div>
                        <div class="col-md-8">
                            <h5 data-preview="amount"></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Deposit Account</h6>
                        </div>
                        <div class="col-md-8">
                            <h5 data-preview="account_title"></h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Other Detail</h6>
                        </div>
                        <div class="col-md-8">
                            <h5>
                                <pre data-preview="notes"></pre>
                            </h5>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-brand" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-brand btnSubmitBooking">Submit Booking</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--end::Portlet-->




@endsection

@section('foot')
    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">


    var BOOKING_MODULE={
        investors:function(){
            return {!! $investors !!};
        },
        vehicle_types:function(){
            return {!! $vehicle_types !!};
        },
        @if(old('preview', 0) == 1)
        preview:{
            investor_id: "{{ old('investor_id', '') }}",
            vehicletype_id: "{{ old('vehicletype_id', '') }}",
            account_title: "{{ old('account_title', '') }}",
            amount: "{{ old('amount', '') }}",
            delivery_date: "{{ old('delivery_date', '') }}",
            notes: `{{ old('notes', '') }}`,
        },
        @endif
        container:'#kt-portlet__create-booking',
        Utils:{

            reset_page:function(){
                @if (old('preview', 0) == 0)
                $(BOOKING_MODULE.container + ' form [name=booking_id]').remove();

                /* clear the items */
                $(BOOKING_MODULE.container + ' [name="vehicletype_id"]').val(null).trigger('change.select2');
                $(BOOKING_MODULE.container + ' [name="investor_id"]').val(null).trigger('change.select2');
                $(BOOKING_MODULE.container + ' [name="amount"]').val(null);
                $(BOOKING_MODULE.container + ' [name="delivery_date"]').val(null);
                $(BOOKING_MODULE.container + ' [name="notes"]').val(null);
                @endif
            },
            load_page:function(modal){

                /* Load the data in page (this funtion is using in view job page) */

                /* Update url */
                var MODAL = $(BOOKING_MODULE.container).parents('.modal');
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/bookings')}}/"+modal.id+"/edit",
                        title:'Edit Booking | Administrator'
                    });
                }

                /* need to check if data is suitable for edit, (not in creating process) */
                if(modal.actions.status==1){
                    /* check if page if loaded in modal */
                    var MODAL = $(BOOKING_MODULE.container).parents('.modal');
                    if(MODAL.length){
                        MODAL.modal('show');
                    }

                    /* change the action of form to edit */
                    $(BOOKING_MODULE.container+' form [name=booking_id]').remove();
                    $(BOOKING_MODULE.container+' form').attr('action', $(BOOKING_MODULE.container+' form').attr('data-edit'))
                    .prepend('<input type="hidden" name="booking_id" value="'+modal.id+'" />');


                    /* load other data */
                    @if (!$errors->any() && old('preview', 0) == 0)

                    $(BOOKING_MODULE.container+' form [kr-accounts-dropdown-wrapper]').attr('kr-accounts-selected', modal.account_id);

                    $(BOOKING_MODULE.container+' form [name="vehicletype_id"]').val(modal.vehicle_type_id).trigger('change.select2');
                    $(BOOKING_MODULE.container+' form [name="investor_id"]').val(modal.investor_id).trigger('change.select2');
                    $(BOOKING_MODULE.container+' form [name="amount"]').val(modal.initial_amount);
                    $(BOOKING_MODULE.container+' form [name="notes"]').val(modal.notes);

                    $(BOOKING_MODULE.container + ' form [name="delivery_date"]').datepicker('update', new Date(modal.date));
                    @endif


                }
                else{
                    /* cannot laod the job now */
                    swal.fire({
                        position: 'center',
                        type: 'error',
                        title: 'Cannot load booking',
                        html: 'booking is processing.. Please retry after some time',
                    });
                }
            },
        },

        investor_module: {
            modal_closed:function(){
                /* modal was closed without adding data, we need to remove the tags */
                $(BOOKING_MODULE.container+' select[name=investor_id] option[data-select2-tag="true"]').remove();
                $(BOOKING_MODULE.container+' select[name=investor_id]').val(null).trigger('change.select2');
            },
            form_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){
                    /* request is not completed yet, we have form data available */
                    var data = {
                        id: response.id,
                        text: response.name,
                        selected: true
                    };

                    var newOption = new Option(data.text, data.id, false, true);
                    $(BOOKING_MODULE.container+' [name="investor_id"]').append(newOption).trigger('change.select2');
                    newOption.setAttribute('data-ref', linker);

                }
                else if(state=="error"){
                    /* remove option from select */

                    $(BOOKING_MODULE.container+' select[name=investor_id] option[data-select2-tag="true"]').remove();
                    var opt = $(BOOKING_MODULE.container+' [name=investor_id] [data-ref="'+linker+'"]');
                    if(opt.length){
                        opt.remove();
                    }
                    $(BOOKING_MODULE.container+' select[name=investor_id]').val(null).trigger('change.select2');
                }
                else{
                    /* request might be completed and we have response from server */
                    var opt = $(BOOKING_MODULE.container+' [name=investor_id] [data-ref="'+linker+'"]');
                    if(opt.length){
                        var newData = {
                            id: response.id,
                            text: response.name,
                            selected: true
                        };

                        var oldData = JSON.parse(JSON.stringify(BOOKING_MODULE.investors()));
                        BOOKING_MODULE.investors = function(){return [...oldData, newData]}

                        opt.val(newData.id).removeAttr('data-ref');
                        opt.text(newData.text);

                        kingriders.Plugins.update_select2(document.querySelector(BOOKING_MODULE.container+' [name="investor_id"]'));
                    }

                    $(BOOKING_MODULE.container+' select[name=investor_id]').trigger('change');

                }
            },
            form_loaded:function(){
                if(typeof INVESTOR_MODULE !== "undefined"){
                    INVESTOR_MODULE.Utils.reset_page();

                    /* add the name */
                    var name = $(BOOKING_MODULE.container+' [name=investor_id] [data-select2-tag]:last-child').val();
                    $(INVESTOR_MODULE.container).find('[name="name"]').val(name).trigger('change');
                    setTimeout(function(){$(INVESTOR_MODULE.container).find('[name="name"]').focus();},100);
                }
            }
        },

        vehicletype_module: {
            modal_closed:function(){
                /* modal was closed without adding data, we need to remove the tags */
                $(BOOKING_MODULE.container+' select[name=vehicletype_id] option[data-select2-tag="true"]').remove();
                $(BOOKING_MODULE.container+' select[name=vehicletype_id]').val(null).trigger('change.select2');
            },
            form_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){
                    /* request is not completed yet, we have form data available */
                    var data = {
                        id: linker,
                        text: response.make + ' ' + response.model + ' ' + response.cc
                    };

                    var newOption = new Option(data.text, data.id, false, true);
                    $(BOOKING_MODULE.container+' [name="vehicletype_id"]').append(newOption).trigger('change.select2');
                    newOption.setAttribute('data-ref', linker);

                }
                else if(state=="error"){
                    /* remove option from select */


                    $(BOOKING_MODULE.container+' select[name=vehicletype_id] option[data-select2-tag="true"]').remove();
                    var opt = $(BOOKING_MODULE.container+' [name=vehicletype_id] [data-ref="'+linker+'"]');
                    if(opt.length){
                        opt.remove();
                    }
                    $(BOOKING_MODULE.container+' select[name=vehicletype_id]').val(null).trigger('change.select2');
                }
                else{
                    /* request might be completed and we have response from server */
                    var opt = $(BOOKING_MODULE.container+' [name=vehicletype_id] [data-ref="'+linker+'"]');
                    if(opt.length){
                        var newData = {
                            id: response._id,
                            text: response.make + ' ' + response.model + ' ' + response.cc,
                            selected: true
                        };

                        var oldData = JSON.parse(JSON.stringify(BOOKING_MODULE.vehicle_types()));
                        BOOKING_MODULE.vehicle_types = function(){return [...oldData, newData]}

                        opt.val(newData.id).removeAttr('data-ref');
                        opt.text(newData.text);

                        kingriders.Plugins.update_select2(document.querySelector(BOOKING_MODULE.container+' [name="vehicletype_id"]'));
                    }

                    $(BOOKING_MODULE.container+' select[name=vehicletype_id]').trigger('change');

                }
            },
            form_loaded:function(){
                if(typeof VEHICLETYPE_MODULE !== "undefined"){
                    VEHICLETYPE_MODULE.Utils.reset_page();

                    /* add the name */
                    var name = $(BOOKING_MODULE.container+' [name=vehicletype_id] [data-select2-tag]:last-child').val();
                    $(VEHICLETYPE_MODULE.container).find('[name="make"]').val(name).trigger('change');
                    setTimeout(function(){$(VEHICLETYPE_MODULE.container).find('[name="make"]').focus();},100);
                }
            }
        }
    };



    $(function(){

        $(BOOKING_MODULE.container+' [name=investor_id]').on('change', function(){
            var self = $(this);
            var selected = self.find(':selected');
            // chances are, new tag is added
            if (typeof selected.attr('data-select2-tag') !== "undefined" && selected.attr('data-select2-tag') == 'true'){

                /* we need to show form to create this record */
                var btn = $(BOOKING_MODULE.container+' [data-create-investor]');
                if(btn.length){
                    btn.trigger('click');
                }
            }
        });

        $(BOOKING_MODULE.container+' [name=vehicletype_id]').on('change', function(){
            var self = $(this);
            var selected = self.find(':selected');
            // chances are, new tag is added
            if (typeof selected.attr('data-select2-tag') !== "undefined" && selected.attr('data-select2-tag') == 'true'){

                /* we need to show form to create this record */
                var btn = $(BOOKING_MODULE.container+' [data-create-vehicletype]');
                if(btn.length){
                    btn.trigger('click');
                }
            }
        });

        $(BOOKING_MODULE.container+' .btnSubmitBooking').on('click', function(){
            $(BOOKING_MODULE.container+' form [name=preview]').remove();

            $(BOOKING_MODULE.container+' form').trigger('submit')

        });


        if(BOOKING_MODULE.preview){
            var modal = $('#previewModal');
            Object.keys(BOOKING_MODULE.preview).forEach(function(key){
                var value = BOOKING_MODULE.preview[key];

                if(key === "investor_id")value = $(BOOKING_MODULE.container+' [name=investor_id] [value="'+value+'"]').text();
                if(key === "vehicletype_id")value = $(BOOKING_MODULE.container+' [name=vehicletype_id] [value="'+value+'"]').text();


                modal.find('[data-preview="'+key+'"]').html(value);
            });
            modal.modal('show');
        }


        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            $(BOOKING_MODULE.container+' form').attr('action', $(BOOKING_MODULE.container+' form').attr('data-add')).find('[name=booking_id]').remove();
            if(typeof BOOKING_MODULE !== "undefined")BOOKING_MODULE.Utils.reset_page();
        }

        /* Check if page has config, do accordingly */
        @isset($config)
        /* This will help us in loading page as edit & view */
        @isset($config->booking)
        var _DataLoaded = {!! $config->booking !!};
        BOOKING_MODULE.Utils.load_page(_DataLoaded);
        @endisset

        @endisset

    });
    </script>
@endsection

