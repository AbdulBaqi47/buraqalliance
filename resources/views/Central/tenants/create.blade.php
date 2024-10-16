@extends('Central.layouts.app')

@section('page_title')
    Create Tenant
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-tenant" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Tenant</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" data-add="{{route('central.admin.tenants.add')}}" data-edit="{{route('central.admin.tenants.edit')}}"  action="{{route('central.admin.tenants.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group mb-2">
                <label>Name: <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name" value="{{old('name')}}">
                @error('name')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">e.g. GG4</span>
                @enderror
            </div>

            <div class="form-group mb-2">
                <label>Domain: <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="domain" class="form-control @error('domain') is-invalid @enderror" placeholder="Enter domain" value="{{old('domain')}}">
                @error('domain')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">e.g. gg4.manage-fleet.com. Must contains Alphanumeric letters, no special characters allowed except (-)</span>
                @enderror
            </div>
            
            <div class="kt-separator kt-separator--space-md kt-separator--border-solid"></div>
            
            <div class="kt-section">
                <div class="kt-section__title">
                    Create Super User
                </div>
                <div class="kt-section__content">
                    
                    <div class="form-group mb-2">
                        <label>Full Name:</label>
                        <input type="text" autocomplete="off" name="su_name" required class="form-control @error('su_name') is-invalid @enderror" placeholder="Enter full name" value="{{old('su_name')}}">
                        @error('su_name')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-2">
                        <label>Email:</label>
                        <input type="email" autocomplete="off" name="su_email" required class="form-control @error('su_email') is-invalid @enderror" placeholder="Enter email" value="{{old('su_email')}}">
                        @error('su_email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @else
                        <span class="form-text text-muted">This will be used for sign-in</span>
                        @enderror
                    </div>
                    <div class="kt-checkbox-inline mb-1 hide_on_add">
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                            <input type="checkbox" name="su_change_password"> Change Password
                            <span></span>
                        </label>
                    </div>
                    <div class="form-group mb-2 hide_on_edit changep">
                        <label>Password:</label>
                        <input type="password" autocomplete="off" name="su_password" required class="form-control @error('su_password') is-invalid @enderror" value="{{old('su_password')}}">
                        @error('su_password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group hide_on_edit changep">
                        <label>Confirm Password:</label>
                        <input type="password" autocomplete="off" name="su_password_confirmation" required class="form-control @error('su_password_confirmation') is-invalid @enderror" value="{{old('su_password_confirmation')}}">
                        @error('su_password_confirmation')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                </div>
            </div>


        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="button" class="btn btn-brand btn-wide btn-lg btnSubmit">Create</button>
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

        var TENANT_MODULE={
            container:'[id="kt-portlet__create-tenant"]:visible',
            Utils:{

                reset_page:function(){

                    $(TENANT_MODULE.container+' form').removeClass('form--edit').removeClass('form--add')
                    .addClass('form--add');

                    $(TENANT_MODULE.container+' form [name=tenant_id]').remove();

                    $(TENANT_MODULE.container + ' .hide_on_add').hide();
                    $(TENANT_MODULE.container + ' .hide_on_edit').show();

                    /* clear the items */
                    $(TENANT_MODULE.container+' [name="name"]').val(null);
                    $(TENANT_MODULE.container+' [name="domain"]').val(null).prop('readonly', false);
                    
                    $(TENANT_MODULE.container + ' [name="su_name"]').val(null);
                    $(TENANT_MODULE.container + ' [name="su_email"]').val(null).removeAttr('org-email');
                    $(TENANT_MODULE.container + ' [name="su_password"]').val(null);
                    $(TENANT_MODULE.container + ' [name="su_password_confirmation"]').val(null);
                },
                load_page:function(tenant){

                    $(TENANT_MODULE.container + ' form').removeClass('form--edit').removeClass('form--add')
                    .addClass('form--edit');

                    $(TENANT_MODULE.container + ' .invalid-feedback').remove();
                    $(TENANT_MODULE.container + ' .is-invalid').removeClass('is-invalid');


                    /* change the action of form to edit */
                    $(TENANT_MODULE.container+' form [name=tenant_id]').remove();
                    $(TENANT_MODULE.container+' form').attr('action', $(TENANT_MODULE.container+' form').attr('data-edit'))
                    .prepend('<input type="hidden" name="tenant_id" value="'+tenant.id+'" />');


                    /* load other data like bike,client */
                    $(TENANT_MODULE.container+' [name="name"]').val(tenant.name);
                    $(TENANT_MODULE.container+' [name="domain"]').val(tenant.domain_name).prop('readonly', true);

                    /* load other data */
                    $(TENANT_MODULE.container + ' [name="su_name"]').val(tenant.default_user.name);
                    $(TENANT_MODULE.container + ' [name="su_email"]').val(tenant.default_user.email).attr('org-email', tenant.default_user.email);

                    $(TENANT_MODULE.container + ' .hide_on_edit').hide();
                    $(TENANT_MODULE.container + ' .hide_on_add').show();

                    kingriders.Utils.isDebug() && console.log('loaded_data', tenant);
                },
            }
        };


        $(function(){

            $(TENANT_MODULE.container + ' .btnSubmit').on('click', function(){
                
                /* Do some validation */
                var is_valid=true;
                var form = $(TENANT_MODULE.container + ' form');

                /* remove all error messages */
                $(TENANT_MODULE.container + ' .invalid-feedback').remove();
                $(TENANT_MODULE.container + ' .is-invalid').removeClass('is-invalid');

                /* Fetch values */
                var name = $(TENANT_MODULE.container + ' [name=name]').val();
                var domain = $(TENANT_MODULE.container + ' [name=domain]').val();
                var su_name = $(TENANT_MODULE.container + ' [name=su_name]').val();
                var email = $(TENANT_MODULE.container + ' [name=su_email]').val();
                var org_email = $(TENANT_MODULE.container + ' [name=su_email]').attr('org-email');
                var pass = $(TENANT_MODULE.container + ' [name=su_password]').val();
                var c_pass = $(TENANT_MODULE.container + ' [name=su_password_confirmation]').val();
                var pass_change = $(TENANT_MODULE.container + ' [name=su_change_password]').is(':checked');


                /* remove extra whitespaces */
                name && (name = name.trim());
                domain && (domain = domain.trim());
                su_name && (su_name = su_name.trim());
                email && (email = email.trim());

                /* Check for empty input */
                if(!name){
                    is_valid=false;
                    $(TENANT_MODULE.container + ' [name=name]').addClass('is-invalid').after(`
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>This field is required.</strong>
                        </span>
                    `);
                }
                if(!domain){
                    is_valid=false;
                    $(TENANT_MODULE.container + ' [name=domain]').parent().append(`
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>This field is required.</strong>
                        </span>
                    `);
                }
                if(!su_name){
                    is_valid=false;
                    $(TENANT_MODULE.container + ' [name=su_name]').parent().append(`
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>This field is required.</strong>
                        </span>
                    `);
                }
                if(!email){
                    is_valid=false;
                    $(TENANT_MODULE.container + ' [name=email]').addClass('is-invalid').after(`
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>This field is required.</strong>
                        </span>
                    `);
                }

                /* if edit form, we need to check if change password is checked in order to validate password*/
                var validate_pass = false;
                if((form.hasClass('form--edit') && pass_change) || form.hasClass('form--add'))validate_pass=true;

                if(validate_pass){
                    if(!pass){
                        is_valid=false;
                        $(TENANT_MODULE.container + ' [name=su_password]').addClass('is-invalid').after(`
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>This field is required.</strong>
                            </span>
                        `);
                    }
                    else{
                        /* Check for password basic validation (lenght) */
                        if(pass.length<8){
                            is_valid=false;
                            /* min length should be 8 character */
                            $(TENANT_MODULE.container + ' [name=su_password]').addClass('is-invalid').after(`
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>The password must be at least 8 characters.</strong>
                                </span>
                            `);
                        }
                        else{
                            /* Check for password confirmation */
                            if(pass!==c_pass){
                                is_valid=false;
                                $(TENANT_MODULE.container + ' [name=su_password]').addClass('is-invalid').after(`
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>The password confirmation does not match.</strong>
                                    </span>
                                `);
                            }
                        }

                    }
                }


                if(is_valid){
                    /* just submit the form */
                    $(TENANT_MODULE.container + ' form').trigger('submit');
                }

            });

            /* for conveinience purpose */
            $(TENANT_MODULE.container + ' input:not([type=hidden])').on('keypress', function(e){
                if(e.keyCode === 13){
                    /* Enter key is pressed */
                    $(TENANT_MODULE.container + ' .btnSubmit').trigger('click');
                }
            });

            $(TENANT_MODULE.container + ' [name="su_change_password"]').on('change', function(e){
                if($(this).is(':checked'))$(TENANT_MODULE.container + ' .changep').show();
                else $(TENANT_MODULE.container + ' .changep').hide();
            });



            /* Check if page has config, do accordingly */
            @isset($config)
                /* This will help us in loading page as edit & view */
                @isset($config->tenant)
                var _DataLoaded = {!! $config->tenant !!};
                TENANT_MODULE.Utils.load_page(_DataLoaded);
                @endisset

            @else
                $(TENANT_MODULE.container+' form').attr('action', $(TENANT_MODULE.container+' form').attr('data-add')).find('[name=tenant_id]').remove();
                TENANT_MODULE.Utils.reset_page();
            @endisset


        });

    </script>
@endsection

