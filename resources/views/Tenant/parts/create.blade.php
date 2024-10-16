@extends('Tenant.layouts.app')

@section('page_title')
    Create Part
@endsection
@section('content')
    @php
        $inventory_alert_default=10;
    @endphp
<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-part" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Part</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" enctype="multipart/form-data" data-add="{{route('tenant.admin.parts.add')}}" data-edit="{{route('tenant.admin.parts.edit')}}" action="{{route('tenant.admin.parts.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">
            <div class="form-group">
                <label>Code:</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{old('code')}}">
                @error('code')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Leave it blank and code will be generated systematically</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Title:</label>
                <input type="text" required name="title" class="form-control @error('title') is-invalid @enderror" value="{{old('title')}}">
                @error('title')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea class="form-control" rows="3" name="description"></textarea>
                @error('description')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter details about this part</span>
                @enderror

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cost Price:</label>
                        <input type="number" step="0.001" required name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{old('cost_price')}}">
                        @error('cost_price')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Sale Price:</label>
                        <input type="number" step="0.001" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{old('sale_price')}}">
                        @error('sale_price')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Rack Number:</label>
                <input type="text" name="rack" class="form-control @error('rack') is-invalid @enderror" value="{{old('rack')}}">
                @error('rack')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>

            <div class="kt-separator kt-separator--border-solid kt-separator--space-sm"></div>

            <div class="form-group">
                <label>Low inventory threshold:</label>
                <input type="number" name="low_inventory_qty" class="form-control @error('low_inventory_qty') is-invalid @enderror" value="{{$inventory_alert_default}}">
                @error('low_inventory_qty')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                    <span class="text-muted">
                        Low inventory alert will be shown if quantity is below this number. Enter <strong>Zero</strong> if you don't want alert against this part
                    </span>
                @enderror

            </div>

            <div class="form-group">
                <label>Photos:</label>
                <div class="kt-uppy kr-uppy" uppy-size="5" uppy-max="5" uppy-min="1" uppy-label="Attach Photos" uppy-input="photos"></div>
                <span class="form-text text-muted">Max file size is 1MB and max number of files is 5.</span>
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


    var PART_MODULE={
        container:'#kt-portlet__create-part',
        Utils:{

            reset_page:function(){

                /* clear the items */
                $(PART_MODULE.container+' form [name="code"]').val(null).prop('readonly', false);
                $(PART_MODULE.container+' form [name="title"]').val(null);
                $(PART_MODULE.container+' form [name="description"]').val(null);
                $(PART_MODULE.container+' form [name="cost_price"]').val(null);
                $(PART_MODULE.container+' form [name="sale_price"]').val(null);
                $(PART_MODULE.container+' form [name="rack"]').val(null);
                $(PART_MODULE.container+' form [name="low_inventory_qty"]').val({{$inventory_alert_default}});

                $(PART_MODULE.container+' form .kt-uppy__list-remove').trigger('click');

                $(PART_MODULE.container+' form .kr-uppy').parents('.form-group').show();
            },
            load_page:function(part){
                /* Load the job in page (this funtion is using in view job page) */

                /* Update url */
                var MODAL = $('#kt-portlet__create-part').parents('.modal');
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/parts')}}/"+part._id+"/edit",
                        title:'Edit Part | Administrator'
                    });
                }

                /* need to check if job is suitable for edit, (not in creating process) */
                if(part.actions.status==1){
                    /* check if page if loaded in modal */
                    var MODAL = $(PART_MODULE.container).parents('.modal');
                    if(MODAL.length){
                        MODAL.modal('show');
                    }

                    /* change the action of form to edit */
                    $(PART_MODULE.container+' form [name=part_id]').remove();
                    $(PART_MODULE.container+' form').attr('action', $(PART_MODULE.container+' form').attr('data-edit'))
                    .prepend('<input type="hidden" name="part_id" value="'+part._id+'" />');


                    /* load other data like part */
                    $(PART_MODULE.container+' form [name="code"]').val(part.code).prop('readonly', true);
                    $(PART_MODULE.container+' form [name="title"]').val(part.title);
                    $(PART_MODULE.container+' form [name="description"]').val(part.description);
                    $(PART_MODULE.container+' form [name="cost_price"]').val(part.cost_price);
                    $(PART_MODULE.container+' form [name="sale_price"]').val(part.sale_price);
                    $(PART_MODULE.container+' form [name="rack"]').val(typeof part.rack === "string" ? part.rack : null);
                    $(PART_MODULE.container+' form [name="low_inventory_qty"]').val(part.low_inventory_qty);


                    $(PART_MODULE.container+' form .kr-uppy').parents('.form-group').hide();
                }
                else{
                    /* cannot laod the job now */
                    swal.fire({
                        position: 'center',
                        type: 'error',
                        title: 'Cannot load part',
                        html: 'part is processing.. Please retry after some time',
                    });
                }
                kingriders.Utils.isDebug() && console.log('loaded_part', part);
            },
        }
    };

    $(function(){
        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            $('#kt-portlet__create-part form').attr('action', $('#kt-portlet__create-part form').attr('data-add')).find('[name=part_id]').remove();
            if(typeof PART_MODULE !== "undefined")PART_MODULE.Utils.reset_page();
        }

        /* Check if page has config, do accordingly */
        @isset($config)
        /* This will help us in loading page as edit & view */
        @isset($config->part)
        var _DataLoaded = {!! $config->part !!};
        PART_MODULE.Utils.load_page(_DataLoaded);
        @endisset

        @endisset
    });
    </script>
@endsection

