@extends('Tenant.layouts.app')

@section('page_title')
    Create Vehicle Type
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-vehicletype" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Vehicle Type</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" data-add="{{route('tenant.admin.vehicletypes.add')}}" data-edit="{{route('tenant.admin.vehicletypes.edit')}}"  action="{{route('tenant.admin.vehicletypes.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group mb-2">
                <label>Make <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="make" class="form-control @error('make') is-invalid @enderror" placeholder="i.e. Lexus" value="{{old('make')}}">
                @error('make')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>

                @enderror
            </div>

            <div class="form-group mb-2">
                <label>Model <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="model" class="form-control @error('model') is-invalid @enderror" placeholder="i.e. 2023" value="{{old('model')}}">
                @error('model')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-2">
                <label>Variant / CC <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="cc" class="form-control @error('cc') is-invalid @enderror" placeholder="i.e. 1800" value="{{old('cc')}}">
                @error('cc')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-2 non-walkingfields">
                <label>Other Details</label>
                <textarea name="notes" autocomplete="off" class="form-control" cols="30" rows="3" placeholder="" ></textarea>
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
    if(typeof VEHICLETYPE_MODULE === "undefined"){

        var VEHICLETYPE_MODULE={
            container:'[id="kt-portlet__create-vehicletype"]:visible',
            Utils:{

                reset_page:function(){

                    $(VEHICLETYPE_MODULE.container+' form [name=vehicletype_id]').remove();

                    /* clear the items */
                    $(VEHICLETYPE_MODULE.container+' [name="make"]').val(null);
                    $(VEHICLETYPE_MODULE.container+' [name="model"]').val(null);
                    $(VEHICLETYPE_MODULE.container+' [name="cc"]').val(null);
                    $(VEHICLETYPE_MODULE.container+' [name="notes"]').val(null);

                },
                load_page:function(model){

                    /* Load the data in page (this funtion is using in view page) */

                    /* Update url */
                    var MODAL = $(VEHICLETYPE_MODULE.container).parents('.modal');
                    if(MODAL.length){
                        kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                            modal:MODAL,
                            url:"{{url('admin/vehicletypes')}}/"+client.id+"/edit",
                            title:'Edit Vehicle Type | Administrator'
                        });
                    }

                    /* need to check if job is suitable for edit, (not in creating process) */
                    if(model.actions.status==1){
                        /* check if page if loaded in modal */
                        var MODAL = $('[id="'+VEHICLETYPE_MODULE.container+'"]').eq(0).parents('.modal');
                        if(MODAL.length){
                            MODAL.modal('show');
                        }

                        /* change the action of form to edit */
                        $(VEHICLETYPE_MODULE.container+' form [name=vehicletype_id]').remove();
                        $(VEHICLETYPE_MODULE.container+' form').attr('action', $(VEHICLETYPE_MODULE.container+' form').attr('data-edit'))
                        .prepend('<input type="hidden" name="vehicletype_id" value="'+model.id+'" />');


                        /* load other data */
                        $(VEHICLETYPE_MODULE.container+' [name="make"]').val(model.name);
                        $(VEHICLETYPE_MODULE.container+' [name="model"]').val(model.email);
                        $(VEHICLETYPE_MODULE.container+' [name="cc"]').val(model.refid);
                        $(VEHICLETYPE_MODULE.container+' [name="notes"]').val(model.phone);


                    }
                    else{
                        /* cannot laod the job now */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: 'Cannot load vehicle type',
                            html: 'Vehicle Type is processing.. Please retry after some time',
                        });
                    }
                    kingriders.Utils.isDebug() && console.log('loaded_vehicletype', model);
                },
            }
        };


        $(function(){

            if(typeof KINGVIEW !== "undefined"){
                /* Seems page was loaded in OnAir, reset page */
                $(VEHICLETYPE_MODULE.container+' form').attr('action', $(VEHICLETYPE_MODULE.container+' form').attr('data-add')).find('[name=vehicletype_id]').remove();
                VEHICLETYPE_MODULE.Utils.reset_page();
            }

            /* Check if page has config, do accordingly */
            @isset($config)
            /* This will help us in loading page as edit & view */
            @isset($config->investor)
            var _DataLoaded = {!! $config->investor !!};
            VEHICLETYPE_MODULE.Utils.load_page(_DataLoaded);
            @endisset

            @endisset


        });
    }
    </script>
@endsection

