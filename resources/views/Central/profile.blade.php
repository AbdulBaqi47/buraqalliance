@extends('Central.layouts.app')

@section('page_title')
Profile
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet mt-5" id="kt-portlet__profile" kr-ajax-content>


    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">{{ Auth::user()->name }} Profile</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>

    <!--begin::Form-->
    <form class="kt-form form--add" enctype="multipart/form-data" action="{{route('central.admin.profile')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">

            <div class="form-group">
                <label>Name <span class="text-danger">*<span></label>
                <input type="text" autocomplete="off" name="name" required class="form-control @error('name') is-invalid @enderror" value="{{ Auth::user()->name }}">
                @error('name')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Email <span class="text-danger">*<span></label>
                <input type="text" readonly org-email="{{Auth::user()->email}}" autocomplete="off" name="email" required class="form-control @error('email') is-invalid @enderror" value="{{ Auth::user()->email }}">
                @error('email')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="kt-checkbox-inline">
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                        <input type="checkbox" name="change_password" checked> Change Password
                        <span></span>
                    </label>
                </div>
            </div>

            <div class="form-group changep">
                <label>Current Password:</label>
                <input type="password" autocomplete="off" name="current_password" required class="form-control @error('current_password') is-invalid @enderror">
                @error('current_password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group changep">
                <label>New Password:</label>
                <input type="password" autocomplete="off" name="password" required class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group changep">
                <label>Confirm New Password:</label>
                <input type="password" autocomplete="off" name="password_confirmation" required class="form-control @error('password_confirmation') is-invalid @enderror">
                @error('password_confirmation')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="button" class="btn btn-brand btnSubmit">Save</button>
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


    var USER_PROFILE={

        container:'#kt-portlet__profile',

    };

    $(function(){
        $(USER_PROFILE.container + ' [name="change_password"]').on('change', function(e){
            if($(this).is(':checked'))$(USER_PROFILE.container + ' .changep').show();
            else $(USER_PROFILE.container + ' .changep').hide();
        });

        $(USER_PROFILE.container + ' .btnSubmit').on('click', function(e){
            e.preventDefault();
            /* Do some validation */
            var is_valid=true;
            var form = $(USER_PROFILE.container + ' form');

            /* remove all error messages */
            $(USER_PROFILE.container + ' .invalid-feedback').remove();
            $(USER_PROFILE.container + ' .is-invalid').removeClass('is-invalid');

            /* Fetch values */
            var name = $(USER_PROFILE.container + ' [name=name]').val();
            var email = $(USER_PROFILE.container + ' [name=email]').val();
            var org_email = $(USER_PROFILE.container + ' [name=email]').attr('org-email');
            var pass = $(USER_PROFILE.container + ' [name=password]').val();
            var c_pass = $(USER_PROFILE.container + ' [name=password_confirmation]').val();
            var pass_change = $(USER_PROFILE.container + ' [name=change_password]').is(':checked');


            /* remove extra whitespaces */
            name && (name = name.trim());
            email && (email = email.trim());

            /* Check for empty input */
            if(!name){
                is_valid=false;
                $(USER_PROFILE.container + ' [name=name]').addClass('is-invalid').after(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }

            /* if edit form, we need to check if change password is checked in order to validate password*/
            var validate_pass = false;
            if(pass_change)validate_pass=true;

            if(validate_pass){
                if(!pass){
                    is_valid=false;
                    $(USER_PROFILE.container + ' [name=password]').addClass('is-invalid').after(`
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
                        $(USER_PROFILE.container + ' [name=password]').addClass('is-invalid').after(`
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>The password must be at least 8 characters.</strong>
                            </span>
                        `);
                    }
                    else{
                        /* Check for password confirmation */
                        if(pass!==c_pass){
                            is_valid=false;
                            $(USER_PROFILE.container + ' [name=password]').addClass('is-invalid').after(`
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
                $(USER_PROFILE.container + ' form').trigger('submit');
            }

        });

        /* for conveinience purpose */
        $(USER_PROFILE.container + ' input:not([type=hidden])').on('keypress', function(e){
            if(e.keyCode === 13){
                /* Enter key is pressed */
                $(USER_PROFILE.container + ' .btnSubmit').trigger('click');
            }
        });

    });
    </script>
@endsection

