@extends('Tenant.layouts.app')

@section('page_title')
    Create Investor
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-investor" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Investor</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" data-add="{{route('tenant.admin.investors.add')}}" data-edit="{{route('tenant.admin.investors.edit')}}"  action="{{route('tenant.admin.investors.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group mb-2">
                <label>Name <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" value="{{old('name')}}">
                @error('name')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>

                @enderror
            </div>

            <div class="form-group mb-2">
                <label>Phone Number <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="i.e. 0521234567" value="{{old('phone')}}">
                @error('phone')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">This field should be unique</span>
                @enderror
            </div>

            <div class="form-group mb-2">
                <label>Email <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter unique email" value="{{old('email')}}">
                @error('email')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">This field should be unique</span>
                @enderror
            </div>

            <div class="form-group mb-2">
                <label>ID # <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" required name="refid" class="form-control @error('refid') is-invalid @enderror" placeholder="Enter unique ID" value="{{old('refid')}}">
                @error('refid')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">This field should be unique</span>
                @enderror
            </div>

            <div class="kt-checkbox-inline mb-1 hide_on_add">
                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                    <input type="checkbox" name="change_password"> Change Password
                    <span></span>
                </label>
            </div>
            <div class="form-group mb-2 hide_on_edit changep">
                <label>Password:</label>
                <input type="password" autocomplete="off" name="password" required class="form-control @error('password') is-invalid @enderror" value="{{old('password')}}">
                @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group hide_on_edit changep">
                <label>Confirm Password:</label>
                <input type="password" autocomplete="off" name="password_confirmation" required class="form-control @error('password_confirmation') is-invalid @enderror" value="{{old('password_confirmation')}}">
                @error('password_confirmation')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Uplaod ID Pictures </label>
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Front Picture" uppy-input="refid_front_image"></div>
                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                    </div>
                    <div>
                        <div class="kt-uppy kr-uppy" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Back Picture" uppy-input="refid_back_image"></div>
                        <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
                    </div>
                </div>
            </div>


            <div class="form-group mb-2 non-walkingfields">
                <label>Other Details</label>
                <textarea name="notes" autocomplete="off" class="form-control" cols="30" rows="3" placeholder="" ></textarea>
            </div>
        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="buttom" class="btn btn-brand btnSubmit">Save</button>
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
    // if(typeof INVESTOR_MODULE === "undefined"){

        var INVESTOR_MODULE={
            emails:{!! $all_emails !!},
            check_uniq:function(email, exclude=null){
                var is_uniq = this.emails.findIndex(function(x){ return x==email && (exclude?x!=exclude:true) }) === -1;
                return is_uniq;
            },

            container:'[id="kt-portlet__create-investor"]:visible',
            Utils:{

                reset_page:function(){
                    /* hide some fields on edit, like passwords */
                    $(INVESTOR_MODULE.container+' form').removeClass('form--edit').removeClass('form--add')
                    .addClass('form--add');

                    /* remove all error messages */
                    $(INVESTOR_MODULE.container+' .invalid-feedback').remove();
                    $(INVESTOR_MODULE.container+' .is-invalid').removeClass('is-invalid');

                    $(INVESTOR_MODULE.container+' .hide_on_add').hide();
                    $(INVESTOR_MODULE.container+' .hide_on_edit').show();

                    $(INVESTOR_MODULE.container+' form [name=investor_id]').remove();

                    /* clear the items */
                    $(INVESTOR_MODULE.container+' [name="name"]').val(null);
                    $(INVESTOR_MODULE.container+' [name="email"]').val(null).removeAttr('org-email');
                    $(INVESTOR_MODULE.container+' [name="refid"]').val(null);
                    $(INVESTOR_MODULE.container+' [name="phone"]').val(null);
                    $(INVESTOR_MODULE.container+' [name="notes"]').val(null);

                    $(INVESTOR_MODULE.container+' [name="password"]').val(null);
                    $(INVESTOR_MODULE.container+' [name="password_confirmation"]').val(null);

                    $(INVESTOR_MODULE.container+' form .kr-uppy').each(function(){

                        var uppy = kingriders.Plugins.uppy.getInstance($(this).attr('id'));
                        if(uppy){
                            /* remove all files */
                            $(this).find('.kt-uppy__list').html('');
                            $(this).find('input[type=file].kr-uppy-file').remove();

                            uppy.reset();
                        }
                    });
                },
                load_page:function(model){

                    /* Load the data in page (this funtion is using in view page) */

                    /* Update url */
                    var MODAL = $(INVESTOR_MODULE.container).parents('.modal');
                    if(MODAL.length){
                        kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                            modal:MODAL,
                            url:"{{url('admin/investors')}}/"+model.id+"/edit",
                            title:'Edit Investor | Administrator'
                        });
                    }

                    /* need to check if job is suitable for edit, (not in creating process) */
                    if(model.actions.status==1){
                        $(INVESTOR_MODULE.container+' form').removeClass('form--edit').removeClass('form--add')
                        .addClass('form--edit');

                        /* check if page if loaded in modal */
                        var MODAL = $(INVESTOR_MODULE.container).eq(0).parents('.modal');
                        if(MODAL.length){
                            MODAL.modal('show');
                        }

                        /* change the action of form to edit */
                        $(INVESTOR_MODULE.container+' form [name=investor_id]').remove();
                        $(INVESTOR_MODULE.container+' form').attr('action', $(INVESTOR_MODULE.container+' form').attr('data-edit'))
                        .prepend('<input type="hidden" name="investor_id" value="'+model.id+'" />');


                        /* load other data */
                        $(INVESTOR_MODULE.container+' [name="name"]').val(model.name);
                        $(INVESTOR_MODULE.container+' [name="email"]').val(model.email).attr('org-email', model.email);
                        $(INVESTOR_MODULE.container+' [name="refid"]').val(model.refid);
                        $(INVESTOR_MODULE.container+' [name="phone"]').val(model.phone);
                        $(INVESTOR_MODULE.container+' [name="notes"]').val(model.notes);

                        // Append mulkiya pics to uppy
                        if(!!model.images && model.images.length > 0){
                            $(INVESTOR_MODULE.container + ' form [type=submit]').prop('disabled', true).text("Loading...");
                            var frontImg = model.images.find(x=>x.type==='front')?.src || null;
                            kingriders.Plugins.uppy.addFile($(INVESTOR_MODULE.container + ' form [uppy-input="refid_front_image"]').attr('id'), frontImg)
                            .then(function(){
                                var backImg = model.images.find(x=>x.type==='back')?.src || null;
                                kingriders.Plugins.uppy.addFile($(INVESTOR_MODULE.container + ' form [uppy-input="refid_back_image"]').attr('id'), backImg)
                                .then(function(){
                                    $(INVESTOR_MODULE.container + ' form [type=submit]').prop('disabled', false).text("Save");

                                });

                            });
                        }

                        /* hide some fields on edit, like passwords */
                        $(INVESTOR_MODULE.container+' .hide_on_edit').hide();
                        $(INVESTOR_MODULE.container+' .hide_on_add').show();
                    }
                    else{
                        /* cannot laod the job now */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: 'Cannot load investor',
                            html: 'Investor is processing.. Please retry after some time',
                        });
                    }
                    kingriders.Utils.isDebug() && console.log('loaded_investor', model);
                },
            }
        };

    // }

    $(function(){

        $(INVESTOR_MODULE.container + ' .btnSubmit').on('click', function(e){
            e.preventDefault();
            /* Do some validation */
            var is_valid=true;
            var form = $(INVESTOR_MODULE.container + ' form');

            /* remove all error messages */
            $(INVESTOR_MODULE.container + ' .invalid-feedback').remove();
            $(INVESTOR_MODULE.container + ' .is-invalid').removeClass('is-invalid');

            /* Fetch values */
            var name = $(INVESTOR_MODULE.container + ' [name=name]').val();
            var email = $(INVESTOR_MODULE.container + ' [name=email]').val();
            var org_email = $(INVESTOR_MODULE.container + ' [name=email]').attr('org-email');
            var pass = $(INVESTOR_MODULE.container + ' [name=password]').val();
            var c_pass = $(INVESTOR_MODULE.container + ' [name=password_confirmation]').val();
            var pass_change = $(INVESTOR_MODULE.container + ' [name=change_password]').is(':checked');

            var phone = $(INVESTOR_MODULE.container + ' [name=phone]').val();
            var refid = $(INVESTOR_MODULE.container + ' [name=refid]').val();


            /* remove extra whitespaces */
            name && (name = name.trim());
            email && (email = email.trim());
            phone && (phone = phone.trim());
            refid && (refid = refid.trim());

            /* Check for empty input */
            if(!name){
                is_valid=false;
                $(INVESTOR_MODULE.container + ' [name=name]').addClass('is-invalid').after(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }
            if(!phone){
                is_valid=false;
                $(INVESTOR_MODULE.container + ' [name=phone]').addClass('is-invalid').after(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }
            if(!refid){
                is_valid=false;
                $(INVESTOR_MODULE.container + ' [name=refid]').addClass('is-invalid').after(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }
            if(!email){
                is_valid=false;
                $(INVESTOR_MODULE.container + ' [name=email]').addClass('is-invalid').after(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }
            else{
                /* Check for email exists */
                var email_found = false;
                if(form.hasClass('form--add')){
                    /* check all emails */
                    if(!INVESTOR_MODULE.check_uniq(email)){
                        email_found=true;
                    }
                }
                else{
                    if(!INVESTOR_MODULE.check_uniq(email, org_email)){
                        email_found=true;
                    }
                }

                if(email_found){
                    is_valid=false;
                    $(INVESTOR_MODULE.container + ' [name=email]').addClass('is-invalid').after(`
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>This email is already taken.</strong>
                        </span>
                    `);
                }
            }

            /* if edit form, we need to check if change password is checked in order to validate password*/
            var validate_pass = false;
            if((form.hasClass('form--edit') && pass_change) || form.hasClass('form--add'))validate_pass=true;

            if(validate_pass){
                if(!pass){
                    is_valid=false;
                    $(INVESTOR_MODULE.container + ' [name=password]').addClass('is-invalid').after(`
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
                        $(INVESTOR_MODULE.container + ' [name=password]').addClass('is-invalid').after(`
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>The password must be at least 8 characters.</strong>
                            </span>
                        `);
                    }
                    else{
                        /* Check for password confirmation */
                        if(pass!==c_pass){
                            is_valid=false;
                            $(INVESTOR_MODULE.container + ' [name=password]').addClass('is-invalid').after(`
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
                $(INVESTOR_MODULE.container + ' form').trigger('submit');
            }

        });


        /* for conveinience purpose */
        $(INVESTOR_MODULE.container + ' input:not([type=hidden])').on('keypress', function(e){
            if(e.keyCode === 13){
                /* Enter key is pressed */
                $(INVESTOR_MODULE.container + ' .btnSubmit').trigger('click');
            }
        });

        $(INVESTOR_MODULE.container + ' [name="change_password"]').on('change', function(e){
            if($(this).is(':checked'))$(INVESTOR_MODULE.container + ' .changep').show();
            else $(INVESTOR_MODULE.container + ' .changep').hide();
        });

        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            $(INVESTOR_MODULE.container+' form').attr('action', $(INVESTOR_MODULE.container+' form').attr('data-add')).find('[name=investor_id]').remove();
            INVESTOR_MODULE.Utils.reset_page();
        }

        /* Check if page has config, do accordingly */
        @isset($config)
        /* This will help us in loading page as edit & view */
        @isset($config->investor)
        var _DataLoaded = {!! $config->investor !!};
        INVESTOR_MODULE.Utils.load_page(_DataLoaded);
        @endisset

        @endisset


    });
    </script>
@endsection

