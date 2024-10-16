@extends('Tenant.layouts.app')

@section('page_title')
@isset($config->vehicle) {{ $config->vehicle->id }} Edit @else Create {{ $type === 'vehicle' ? 'Vehicle' : 'Bike' }} @endisset
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet mt-5" id="kt-portlet__create-vehicle" kr-ajax-content>


    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">@isset($config->vehicle) Edit @else Create @endisset {{ $type === 'vehicle' ? 'Vehicle' : 'Bike' }}</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>

    <!--begin::Form-->
    <form class="kt-form" enctype="multipart/form-data" data-add="{{route('tenant.admin.vehicles.add')}}" data-edit="{{route('tenant.admin.vehicles.edit')}}"  action="{{route('tenant.admin.vehicles.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="row">
                <div class="col-md-6">

                    <div class="row border py-4 px-2 mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rental Company <span class="text-danger">*<span></label>
                                <select name="rental_company" required class="form-control kr-select2 @error('rental_company') is-invalid @enderror">
                                    <option value=""></option>
                                    @foreach($clients as $client)
                                        <option data-rent="{{$client->monthly_rent}}" value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach

                                </select>
                                @if ($errors->has('rental_company'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('rental_company') }}
                                        </strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assign Month <span class="text-danger">*<span></label>
                                <input type="text" required readonly name="rental_company_month" data-state="month" class="kr-datepicker form-control @error('rental_company_month') is-invalid @enderror" data-default="{{ old('rental_company_month') }}">
                                @if ($errors->has('rental_company_month'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('rental_company_month') }}
                                        </strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12">

                            <div class="form-group mb-0">
                                <label>Monthly Rent</label>
                                <input type="text" autocomplete="off" name="monthly_rent" class="form-control @error('monthly_rent') is-invalid @enderror" placeholder="Please Enter Monthly Rent Amount" value="{{old('monthly_rent')}}">
                                @if ($errors->has('monthly_rent'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('monthly_rent') }}
                                        </strong>
                                    </span>
                                @endif
                                <span class="form-text text-muted d-none">This will overwrite default monthly rent</span>
                            </div>

                        </div>

                    </div>


                </div>
                <div class="col-md-6">

                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Location <span class="text-danger">*<span></label>
                                @include('Tenant.includes.locations', ['selected' => old('location')])
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label>State</label>
                                <select name="state" data-template="VEHICLE_MODULE.select2Utils.formatWalking" class="form-control kr-select2 @error('state') is-invalid @enderror">
                                    <option value=""></option>
                                    <option value="ready_to_assign">Ready to assign</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @if ($errors->has('state'))
                                    <span class="invalid-response text-danger" role="alert">
                                        <strong>
                                            {{ $errors->first('state') }}
                                        </strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label>Select Type <span class="text-danger">*<span></label>
                <div class="kt-radio-inline">
                    <label class="kt-radio">
                        <input type="radio" value="bike" @if( $type === 'bike') checked @endif name="type" required> Bike
                        <span></span>
                    </label>
                    <label class="kt-radio">
                        <input type="radio" value="vehicle" @if( $type === 'vehicle') checked @endif name="type" required> Vehicle
                        <span></span>
                    </label>
                </div>
                <span class="form-text text-muted">select Vehicle or Bike</span>
            </div>

            <div class="form-group">
                <label>Model <span class="text-danger">*<span></label>
                <select class="form-control kr-select2" data-dynamic data-source="VEHICLE_MODULE.Utils.getGroup('models')" name="model" required data-placeholder="i.e. ES300h Sedan H 2.5L CVT Premier">
                    <option></option>
                </select>
                @error('model')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Year <span class="text-danger">*<span></label>
                <input type="text" required autocomplete="off" name="year" class="form-control @error('year') is-invalid @enderror" placeholder="i.e. 2023" value="{{old('year')}}">
                @error('year')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Chassis Number <span class="text-danger">*<span></label>
                <input type="text" required autocomplete="off" name="chassis_number" class="form-control @error('chassis_number') is-invalid @enderror" value="{{old('chassis_number')}}">
                @error('chassis_number')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Engine Number <span class="text-danger">*<span></label>
                <input type="text" required autocomplete="off" name="engine_number" class="form-control @error('engine_number') is-invalid @enderror" value="{{old('engine_number')}}">
                @error('engine_number')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Plate <span class="text-danger">*<span></label>
                <input type="text" required autocomplete="off" name="plate" class="form-control @error('plate') is-invalid @enderror" placeholder="Enter plate" value="{{old('plate')}}">
                @error('plate')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Enter a unique plate</span>
                @enderror
            </div>
            <div class="form-group">
                <label>Select Plate Code:</label>
                <select class="form-control kr-select2" data-dynamic data-source="VEHICLE_MODULE.Utils.getGroup('plateCodes')" name="plate_code">
                    <option></option>
                </select>
                @error('plate_code')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @else
                <span class="form-text text-muted">Enter a unique plate Code</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Color</label>
                <input type="text" autocomplete="off" name="color" class="form-control @error('color') is-invalid @enderror" placeholder="i.e. White" value="{{old('color')}}">
                @error('color')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Mulkiya Images --}}
            <div class="form-group">
                <label>Mulkiya pictures </label>
                <div class="d-flex justify-content-start">
                    <div>
                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Front Picture" uppy-input="mulkiya_picture_front"></div>
                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        @if ($errors->has('mulkiya_picture_front'))
                            <span class="invalid-response text-danger" role="alert">
                                <strong>
                                    {{ $errors->first('mulkiya_picture_front') }}
                                </strong>
                            </span>
                        @endif
                    </div>
                    <div class="ml-4">
                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Back Picture" uppy-input="mulkiya_picture_back"></div>
                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        @if ($errors->has('mulkiya_picture_back'))
                            <span class="invalid-response text-danger" role="alert">
                                <strong>
                                    {{ $errors->first('mulkiya_picture_back') }}
                                </strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Mulkiya Expiry Date</label>
                <input type="text" readonly name="mulkiya_expiry" data-state="date" class="kr-datepicker form-control @error('mulkiya_expiry') is-invalid @enderror" data-default="{{ old('mulkiya_expiry') }}">
                @if ($errors->has('mulkiya_expiry'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ $errors->first('mulkiya_expiry') }}
                        </strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label>Insurance Expiry Date</label>
                <input type="text" readonly name="insurance_expiry" data-state="date" class="kr-datepicker form-control @error('insurance_expiry') is-invalid @enderror" data-default="{{ old('insurance_expiry') }}">
                @if ($errors->has('insurance_expiry'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ $errors->first('insurance_expiry') }}
                        </strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label>Insurance Company:</label>
                <input type="text" autocomplete="off" name="insurance_company" class="form-control @error('insurance_company') is-invalid @enderror" placeholder="Enter Insurance Company" value="{{old('insurance_company')}}">
                @error('insurance_company')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Insurance Issue Date:</label>
                <input type="text" readonly name="insurance_issue_date" data-state="date" class="kr-datepicker form-control @error('insurance_issue_date') is-invalid @enderror" data-default="{{ old('insurance_issue_date') }}">
                @if ($errors->has('insurance_issue_date'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ $errors->first('insurance_issue_date') }}
                        </strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label>Insurance Paper Issue Date:</label>
                <input type="text" readonly name="insurance_paper_issue_date" data-state="date" class="kr-datepicker form-control @error('insurance_paper_issue_date') is-invalid @enderror" data-default="{{ old('insurance_paper_issue_date') }}">
                @if ($errors->has('insurance_paper_issue_date'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ $errors->first('insurance_paper_issue_date') }}
                        </strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label>Insurance Paper Expiry Date:</label>
                <input type="text" readonly name="insurance_paper_expiry_date" data-state="date" class="kr-datepicker form-control @error('insurance_paper_expiry_date') is-invalid @enderror" data-default="{{ old('insurance_paper_expiry_date') }}">
                @if ($errors->has('insurance_paper_expiry_date'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ $errors->first('insurance_paper_expiry_date') }}
                        </strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label>Insurance Paper Attachment:</label>
                <div class="d-flex justify-content-start">
                    <div>
                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Insurance Paper Attachment" uppy-input="insurance_paper_attachment"></div>
                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        @if ($errors->has('insurance_paper_attachment'))
                            <span class="invalid-response text-danger" role="alert">
                                <strong>
                                    {{ $errors->first('insurance_paper_attachment') }}
                                </strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="kt-checkbox m-1">
                    <input  name="have_bagbox" type="checkbox">Bike have Bagbox?
                    <span></span>
                </label>
            </div>

            <div class="form-group">
                <label>Branding Type:</label>
                <input type="text" autocomplete="off" name="branding_type" class="form-control @error('branding_type') is-invalid @enderror" placeholder="Enter Branding Type" value="{{old('branding_type')}}">
                @error('branding_type')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Advertisement Issue Date:</label>
                <input type="text" readonly name="advertisement_issue_date" data-state="date" class="kr-datepicker form-control @error('advertisement_issue_date') is-invalid @enderror" data-default="{{ old('advertisement_issue_date') }}">
                @if ($errors->has('advertisement_issue_date'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ $errors->first('advertisement_issue_date') }}
                        </strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label>Advertisement Expiry Date:</label>
                <input type="text" readonly name="advertisement_expiry_date" data-state="date" class="kr-datepicker form-control @error('advertisement_expiry_date') is-invalid @enderror" data-default="{{ old('advertisement_expiry_date') }}">
                @if ($errors->has('advertisement_expiry_date'))
                    <span class="invalid-response text-danger" role="alert">
                        <strong>
                            {{ $errors->first('advertisement_expiry_date') }}
                        </strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label>Advertisement Attachment:</label>
                <div class="d-flex justify-content-start">
                    <div>
                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Advertisement Attachment" uppy-input="advertisement_attachment"></div>
                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                        @if ($errors->has('advertisement_attachment'))
                            <span class="invalid-response text-danger" role="alert">
                                <strong>
                                    {{ $errors->first('advertisement_attachment') }}
                                </strong>
                            </span>
                        @endif
                    </div>
                </div>
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


    var VEHICLE_MODULE={
        container:'#kt-portlet__create-vehicle',
        Utils:{

            getGroup: function(variable){
                var models = @json($models);
                var plateCodes = @json($plateCodes);

                return variable === 'models' ? models : plateCodes;
            },

            reset_page:function(){
                $(VEHICLE_MODULE.container + ' form [name=vehicle_id]').remove();

                /* clear the items */
                $(VEHICLE_MODULE.container + ' [name="plate"]').val(null);
            },
            load_page:function(modal,_clientLoaded){
                /* Load the job in page (this funtion is using in view job page) */

                /* Update url */
                var MODAL = $(VEHICLE_MODULE.container).parents('.modal');
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/vehicles')}}/"+modal.id+"/edit",
                        title:'Edit Vehicle | Administrator'
                    });
                }

                /* need to check if job is suitable for edit, (not in creating process) */
                if(modal.actions.status==1){
                    /* check if page if loaded in modal */
                    var MODAL = $(VEHICLE_MODULE.container).parents('.modal');
                    if(MODAL.length){
                        MODAL.modal('show');
                    }

                    /* change the action of form to edit */
                    $(VEHICLE_MODULE.container+' form [name=vehicle_id]').remove();
                    $(VEHICLE_MODULE.container+' form').attr('action', $(VEHICLE_MODULE.container+' form').attr('data-edit'))
                    .prepend('<input type="hidden" name="vehicle_id" value="'+modal.id+'" />');


                    /* load other data */
                    @if (!$errors->any())
                    $(VEHICLE_MODULE.container+' [name="plate"]').val(modal.plate);
                    $(VEHICLE_MODULE.container+' [name="plate_code"]').val(modal.plate_code).trigger('change.select2');
                    $(VEHICLE_MODULE.container+' [name="type"][value="' + modal.type + '"]').prop('checked', true).trigger('change');
                    $(VEHICLE_MODULE.container+' [name="model"]').val(modal.model).trigger('change.select2');
                    $(VEHICLE_MODULE.container+' [name="year"]').val(modal.year);
                    $(VEHICLE_MODULE.container+' [name="chassis_number"]').val(modal.chassis_number);
                    $(VEHICLE_MODULE.container+' [name="engine_number"]').val(modal.engine_number);
                    $(VEHICLE_MODULE.container+' [name="insurance_company"]').val(modal.insurance_company);
                    $(VEHICLE_MODULE.container+' [name="branding_type"]').val(modal.branding_type);
                    $(VEHICLE_MODULE.container+' [name="have_bagbox"]').attr('checked',modal.have_bagbox);
                    $(VEHICLE_MODULE.container+' [name="color"]').val((modal.color.charAt(0).toUpperCase() + modal.color.substr(1)));
                    $(VEHICLE_MODULE.container+' [name="state"]').val(modal.state).trigger('change.select2');
                    $(VEHICLE_MODULE.container+' [name="location"]').val(modal.location).trigger('change.select2');

                    // load rental comapny
                    if(!!_clientLoaded){

                        $(VEHICLE_MODULE.container+' [name="rental_company"]').val(_clientLoaded.client_id).trigger('change.select2');
                        $(VEHICLE_MODULE.container+' [name="monthly_rent"]').val(_clientLoaded.monthly_rent);
                        $(VEHICLE_MODULE.container + ' [name="rental_company_month"]').datepicker('update', new Date(_clientLoaded.assign_date));

                        $(VEHICLE_MODULE.container+' [name="rental_company"], ' + VEHICLE_MODULE.container+' [name="monthly_rent"], ' + VEHICLE_MODULE.container+' [name="rental_company_month"]').prop('disabled', true);
                    }

                    $(VEHICLE_MODULE.container + ' form [name="mulkiya_expiry"]').datepicker('update', new Date(modal.mulkiya_expiry));
                    $(VEHICLE_MODULE.container + ' form [name="insurance_expiry"]').datepicker('update', new Date(modal.insurance_expiry));
                    $(VEHICLE_MODULE.container + ' form [name="insurance_issue_date"]').datepicker('update', new Date(modal.insurance_issue_date));
                    $(VEHICLE_MODULE.container + ' form [name="insurance_paper_issue_date"]').datepicker('update', new Date(modal.insurance_paper_issue_date));
                    $(VEHICLE_MODULE.container + ' form [name="insurance_paper_expiry_date"]').datepicker('update', new Date(modal.insurance_paper_expiry_date));
                    $(VEHICLE_MODULE.container + ' form [name="advertisement_issue_date"]').datepicker('update', new Date(modal.advertisement_issue_date));
                    $(VEHICLE_MODULE.container + ' form [name="advertisement_expiry_date"]').datepicker('update', new Date(modal.advertisement_expiry_date));
                    @endif

                    // Append mulkiya pics to uppy
                    if(modal.mulkiya_pictures){
                        $(VEHICLE_MODULE.container + ' form [type=submit]').prop('disabled', true).text("Loading...");
                        kingriders.Plugins.uppy.addFile($(VEHICLE_MODULE.container + ' form [uppy-input="mulkiya_picture_front"]').attr('id'), modal.mulkiya_pictures.front)
                        .then(function(){
                            kingriders.Plugins.uppy.addFile($(VEHICLE_MODULE.container + ' form [uppy-input="mulkiya_picture_back"]').attr('id'), modal.mulkiya_pictures.back)
                            .then(function(){
                                $(VEHICLE_MODULE.container + ' form [type=submit]').prop('disabled', false).text("Save");
                            });
                        });
                    }
                    // Append insurance paper attachment to uppy
                    if(modal.insurance_paper_attachment){
                        $(VEHICLE_MODULE.container + ' form [type=submit]').prop('disabled', true).text("Loading...");
                        kingriders.Plugins.uppy.addFile($(VEHICLE_MODULE.container + ' form [uppy-input="insurance_paper_attachment"]').attr('id'), modal.insurance_paper_attachment)
                        .then(function(){
                            $(VEHICLE_MODULE.container + ' form [type=submit]').prop('disabled', false).text("Save");
                        });
                    }
                    // Append insurance paper attachment to uppy
                    if(modal.advertisement_attachment){
                        $(VEHICLE_MODULE.container + ' form [type=submit]').prop('disabled', true).text("Loading...");
                        kingriders.Plugins.uppy.addFile($(VEHICLE_MODULE.container + ' form [uppy-input="advertisement_attachment"]').attr('id'), modal.advertisement_attachment)
                        .then(function(){
                            $(VEHICLE_MODULE.container + ' form [type=submit]').prop('disabled', false).text("Save");
                        });
                    }


                }
                else{
                    /* cannot laod the job now */
                    swal.fire({
                        position: 'center',
                        type: 'error',
                        title: 'Cannot load vehicle',
                        html: 'vehicle is processing.. Please retry after some time',
                    });
                }
            },
        },
        select2Utils:{
            formatWalking:function(option){
                if (!option.id) {
                    return option.text;
                }
                // var status = {
                //     "open": {'title': 'Open', 'class': ' kt-badge--primary'},
                //     "in_progress": {'title': 'In Progress', 'class': ' kt-badge--warning'},
                //     "on_hold": {'title': 'On Hold', 'class': ' kt-badge--danger'},
                //     "complete": {'title': 'Completed', 'class': ' kt-badge--success'},
                // };

                var status = {
                    "ready_to_assign": {'title': option.text, 'class': ' kt-badge--success'},
                    "maintenance": {'title': option.text, 'class': ' kt-badge--danger'},
                };
                var data = option.id;
                if (typeof status[data] === 'undefined') {
                    return data;
                }
                return $('<span class="kt-badge ' + status[data].class + ' kt-badge--inline kt-badge--pill">' + status[data].title + '</span>');
            }
        }
    };

    $(function(){
        $('#kt-portlet__create-vehicle [name=rental_company]').on('change', function() {
            var rent = $(this).find(':selected').attr('data-rent');
            $('#kt-portlet__create-vehicle [name="monthly_rent"]').attr("placeholder", "i.e. Default Rent: "+rent);
            $('#kt-portlet__create-vehicle [name="monthly_rent"]').next().removeClass('d-none');
        });

        /* preload the kr-ajax module (only if laoded in modal) */
        var MODAL = $(VEHICLE_MODULE.container).parents('.modal');
        if(MODAL.length){
            setTimeout(function(){
                $(VEHICLE_MODULE.container+' '+kingriders.Plugins.Selectors.kr_ajax_preload).each(function(i, elem){
                    /* initiate the ajax */
                    $(this).trigger('click.krevent', {
                        preload:true
                    });
                });
            },100);
        }


        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            $(VEHICLE_MODULE.container+' form').attr('action', $(VEHICLE_MODULE.container+' form').attr('data-add')).find('[name=vehicle_id]').remove();
            if(typeof VEHICLE_MODULE !== "undefined")VEHICLE_MODULE.Utils.reset_page();
        }

        /* Check if page has config, do accordingly */
        @isset($config)
        /* This will help us in loading page as edit & view */
        @isset($config->vehicle)
        var _DataLoaded = {!! $config->vehicle !!};
        var _clientLoaded = {!! $config->vehicleClient !!};
        VEHICLE_MODULE.Utils.load_page(_DataLoaded,_clientLoaded);
        @endisset

        @endisset

    });
    </script>
@endsection

