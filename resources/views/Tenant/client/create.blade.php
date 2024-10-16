@extends('Tenant.layouts.app')

@section('page_title')
    Create Client
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-client" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Client</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" data-add="{{route('tenant.admin.clients.add')}}" data-edit="{{route('tenant.admin.clients.edit')}}"  action="{{route('tenant.admin.clients.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group mb-2">
                <label>Name: <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" value="{{old('name')}}">
                @error('name')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please client full name</span>
                @enderror
            </div>


            <div class="form-group mb-2 non-walkingfields">
                <label>Email address: <span class="text-danger">*<span></label>
                <input type="email" required autocomplete="off" class="form-control" name="email" placeholder="Enter email" value="">
                <span class="form-text text-muted">Enter unique email address</span>
            </div>


            <div class="form-group mb-2 non-walkingfields">
                <label>Source: <span class="text-danger">*<span></label>
                <select class="form-control kr-select2" name="source">
                    <option value=""></option>
                    <option value="driver">Aggregator</option>
                    <option value="vehicle">Supplier</option>
                </select>
                <span class="form-text text-muted">Who will be assign to this client</span>
            </div>

            <div class="form-group mb-2 non-walkingfields widgetfields">
                <label>Monthly Rent:</label>
                <input type="text" autocomplete="off" class="form-control" name="monthly_rent" placeholder="Enter Monthly Rent Amount" value="">
                <span class="form-text text-muted">Please enter monthly rent amount</span>
            </div>

            <div class="form-group  mb-2 non-walkingfields widgetfields">
                <label>Contract Start Date:</label>
                <input type="text" required  name="start_date" data-name="start_date" data-state="date" class="rounded-0 kr-datepicker form-control">
            </div>

            <div class="form-group  mb-2 non-walkingfields widgetfields">
                <label>Contract End Date:</label>
                <input type="text" required  name="end_date" data-name="end_date" data-state="date" class="rounded-0 kr-datepicker form-control">

            </div>

            <div class="form-group mb-2 non-walkingfields">
                <label>VAT TRN Number:</label>
                <input type="text" autocomplete="off" class="form-control" name="trn" placeholder="Enter VAT TRN Number" value="">
                <span class="form-text text-muted">Please enter VAT TRN Number</span>
            </div>

            <div class="form-group mb-2 non-walkingfields">
                <label>Address:</label>
                <textarea name="address" autocomplete="off" class="form-control" cols="30" rows="3" placeholder="Enter full address" ></textarea>
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
    if(typeof CLIENT_MODULE === "undefined"){

        var CLIENT_MODULE={
            container:'[id="kt-portlet__create-client"]:visible',
            Utils:{

                reset_page:function(){

                    $(CLIENT_MODULE.container+' form [name=client_id]').remove();

                    /* clear the items */
                    $(CLIENT_MODULE.container+' [name="name"]').val(null);
                    $(CLIENT_MODULE.container+' [name="email"]').val(null);
                    $(CLIENT_MODULE.container+' [name="source"]').val(null).prop('disabled', false).trigger('change.select2');
                    $(CLIENT_MODULE.container+' [name="monthly_rent"]').val(null);
                    $(CLIENT_MODULE.container+' [name="start_date"]').val(null);
                    $(CLIENT_MODULE.container+' [name="end_date"]').val(null);
                    $(CLIENT_MODULE.container+' [name="trn"]').val(null);
                    $(CLIENT_MODULE.container+' [name="address"]').val(null);
                },
                load_page:function(client){

                    /* Load the job in page (this funtion is using in view job page) */

                    /* Update url */
                    var MODAL = $('#kt-portlet__create-client').parents('.modal');
                    if(MODAL.length){
                        kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                            modal:MODAL,
                            url:"{{url('admin/clients')}}/"+client.id+"/edit",
                            title:'Edit Client | Administrator'
                        });
                    }

                    /* need to check if job is suitable for edit, (not in creating process) */
                    if(client.actions.status==1){
                        /* check if page if loaded in modal */
                        var MODAL = $('[id="kt-portlet__create-client"]').eq(0).parents('.modal');
                        if(MODAL.length){
                            MODAL.modal('show');
                        }

                        /* change the action of form to edit */
                        $(CLIENT_MODULE.container+' form [name=client_id]').remove();
                        $(CLIENT_MODULE.container+' form').attr('action', $(CLIENT_MODULE.container+' form').attr('data-edit'))
                        .prepend('<input type="hidden" name="client_id" value="'+client.id+'" />');


                        /* load other data like bike,client */
                        $(CLIENT_MODULE.container+' [name="name"]').val(client.name);
                        $(CLIENT_MODULE.container+' [name="email"]').val(client.email);
                        $(CLIENT_MODULE.container+' [name="trn"]').val(client.trn);
                        $(CLIENT_MODULE.container+' [name="source"]').val(client.source).prop('disabled', true).trigger('change.select2');
                        $(CLIENT_MODULE.container+' [name="source"]').trigger('change');
                        $(CLIENT_MODULE.container+' [name="monthly_rent"]').val(client.monthly_rent);
                        $(CLIENT_MODULE.container+' [name="start_date"]').val(client.start_date);
                        $(CLIENT_MODULE.container+' [name="end_date"]').val(client.end_date);
                        $(CLIENT_MODULE.container+' [name="address"]').val(client.address);
                    }
                    else{
                        /* cannot laod the job now */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: 'Cannot load client',
                            html: 'client is processing.. Please retry after some time',
                        });
                    }
                    kingriders.Utils.isDebug() && console.log('loaded_client', client);
                },
            }
        };


        $(function(){
            $('.widgetfields').hide();
            $('#kt-portlet__create-client [name=source]').on('change', function() {
                var source = $(this).find(':selected').val();
                /* Need to check if the selected option is Vehicle, then show some extra fields */
                if (source === 'vehicle') {
                    $('#kt-portlet__create-client [name=start_date]').attr('required', true);
                    $('#kt-portlet__create-client [name=end_date]').attr('required', true);
                    $('.widgetfields').show();
                } else {
                    $('#kt-portlet__create-client [name=start_date]').attr('required', false);
                    $('#kt-portlet__create-client [name=end_date]').attr('required', false);
                    // If source is not Vehicle, hide specific fields
                    $('.widgetfields').hide();
                }
            });



            if(typeof KINGVIEW !== "undefined"){
                /* Seems page was loaded in OnAir, reset page */
                $(CLIENT_MODULE.container+' form').attr('action', $(CLIENT_MODULE.container+' form').attr('data-add')).find('[name=client_id]').remove();
                CLIENT_MODULE.Utils.reset_page();
            }

            /* Check if page has config, do accordingly */
            @isset($config)
            /* This will help us in loading page as edit & view */
            @isset($config->client)
            var _DataLoaded = {!! $config->client !!};
            CLIENT_MODULE.Utils.load_page(_DataLoaded);
            @endisset

            @endisset


        });
    }
    </script>
@endsection

