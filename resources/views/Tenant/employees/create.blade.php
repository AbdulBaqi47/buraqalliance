@extends('Tenant.layouts.app')

@section('page_title')
    Create Employee
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-employee" kr-ajax-content>


    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Employee</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" data-add="{{route('tenant.admin.employee.add')}}" data-edit="{{route('tenant.admin.employee.edit')}}" action="{{route('tenant.admin.employee.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">
            <div class="form-group mb-2">
                <label>Full Name:</label>
                <input type="text" autocomplete="off" name="name" required class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" value="{{old('name')}}">
                @error('name')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group mb-2">
                <label>Email:</label>
                <input type="email" autocomplete="off" name="email" required class="form-control @error('email') is-invalid @enderror" placeholder="Enter email" value="{{old('email')}}">
                @error('name')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">This will be used for sign-in</span>
                @enderror
            </div>
            <div class="form-group mb-2">
                <label>Type:</label>
                <select class="form-control kr-select2" name="user_type" required>
                    <option value="employee" selected>Employee</option>
                    <option value="labour">Labour</option>
                </select>
                @error('user_type')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please choose type, Employee or Labour</span>
                @enderror

            </div>
            <div class="form-group mb-2">
                <label>Designation:</label>
                <select class="form-control kr-select2" data-dynamic data-source="EMPLOYEES_MODULE.designations()" name="designation" required>
                    <option></option>
                </select>
                @error('designation')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please enter designation</span>
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

            <div class="kt-separator kt-separator--border-dashed kt-separator--space-xs"></div>

            <div class="row">
                <div class="col-sm-7">
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="number" step="0.01" autocomplete="off" name="salary" required class="form-control @error('salary') is-invalid @enderror" value="{{old('salary')}}">
                        @error('salary')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group hide_on_add mb-0">
                        <label>Active</label>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="switch">
                                    <input type="checkbox" name="is_active">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mb-2">
                <div class="accounts_container">
                    <label>Accounts:</label>
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0 float-right">
                        <input type="checkbox" name="grant_all_accounts"> All Accounts
                        <span></span>
                    </label>
                    <select class="form-control kr-select2" data-placeholder="Select Accounts" name="employee_accounts[]" multiple>
                        @foreach ($accounts as $account)
                            <option value="{{$account->id}}">{{$account->title}}</option>
                        @endforeach
                    </select>
                    @error('employee_accounts')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @else
                    <span class="form-text text-muted">Select account you want to grant access to user</span>
                    @enderror
                </div>

            </div>

        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid py-2">
            <div class="kt-form__actions kt-form__actions--right">
                <button type="button" class="btn btn-brand btnSubmit px-5">Save</button>
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
    var EMPLOYEES_MODULE = function(){

        return {
            emails:{!! $all_emails !!},
            check_uniq:function(email, exclude=null){
                var is_uniq = EMPLOYEES_MODULE.emails.findIndex(function(x){ return x==email && (exclude?x!=exclude:true) }) === -1;
                return is_uniq;
            },
            _desg:{!! $desg !!},
            designations:function(){
                /* check key according to user_type */
                var user_type = $('#kt-portlet__create-employee [name=user_type]').val();
                var sugs = EMPLOYEES_MODULE._desg[user_type];
                if(typeof sugs !== "undefined")return sugs;
                return [];
            },
            Utils:{
                reset_page:function(){
                    /* hide some fields on edit, like passwords */
                    $('#kt-portlet__create-employee form').removeClass('form--edit').removeClass('form--add')
                    .addClass('form--add');

                    /* remove all error messages */
                    $('#kt-portlet__create-employee .invalid-feedback').remove();
                    $('#kt-portlet__create-employee .is-invalid').removeClass('is-invalid');

                    $('#kt-portlet__create-employee .hide_on_add').hide();
                    $('#kt-portlet__create-employee .hide_on_edit').show();

                    $('#kt-portlet__create-employee form [name=user_id]').remove();

                    /* clear the items */

                    $('#kt-portlet__create-employee [name="name"]').val(null);
                    $('#kt-portlet__create-employee [name="email"]').val(null).removeAttr('org-email');
                    $('#kt-portlet__create-employee [name="is_active"]').prop('checked', false).trigger('change');
                    $('#kt-portlet__create-employee [name="password"]').val(null);
                    $('#kt-portlet__create-employee [name="password_confirmation"]').val(null);
                    $('#kt-portlet__create-employee [name="salary"]').val(null);

                    $('#kt-portlet__create-employee [name="user_type"]').val(null).trigger('change.select2');
                    $('#kt-portlet__create-employee [name="designation"]').val(null).trigger('change.select2');

                    /* remove all account selection */
                    $('#kt-portlet__create-employee .accounts_container select').val(null).trigger("change");
                },
                load_page:function(data){

                    /* Load the job in page (this funtion is using in view job page) */

                    /* Update url */
                    var MODAL = $('#kt-portlet__create-employee').parents('.modal');
                    if(MODAL.length){
                        kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                            modal:MODAL,
                            url:"{{url('admin/employees')}}/"+data._id+"/edit",
                            title:'Edit Employee | Administrator'
                        });
                    }

                    /* remove all error messages */
                    $('#kt-portlet__create-employee .invalid-feedback').remove();
                    $('#kt-portlet__create-employee .is-invalid').removeClass('is-invalid');

                    $('#kt-portlet__create-employee .hide_on_edit,#kt-portlet__create-employee .hide_on_add').show();

                    /* need to check if job is suitable for edit, (not in creating process) */
                    if(data.actions.status==1){
                        $('#kt-portlet__create-employee form').removeClass('form--edit').removeClass('form--add')
                        .addClass('form--edit');

                        /* check if page if loaded in modal */
                        var MODAL = $('#kt-portlet__create-employee').parents('.modal');
                        if(MODAL.length){
                            MODAL.modal('show');
                        }

                        /* change the action of form to edit */
                        $('#kt-portlet__create-employee form [name=user_id]').remove();
                        $('#kt-portlet__create-employee form').attr('action', $('#kt-portlet__create-employee form').attr('data-edit'))
                        .prepend('<input type="hidden" name="user_id" value="'+data._id+'" />');


                        /* load other data */
                        $('#kt-portlet__create-employee [name="name"]').val(data.name);
                        $('#kt-portlet__create-employee [name="email"]').val(data.email).attr('org-email', data.email);
                        var is_active=false;
                        if(data.status == 1)is_active=true;

                        var basic_salary = null;
                        if(typeof data.props !== "undefined" && typeof data.props.basic_salary !== "undefined")basic_salary=parseFloat(data.props.basic_salary)||0;
                        $('#kt-portlet__create-employee [name="is_active"]').prop('checked', is_active).trigger('change');
                        $('#kt-portlet__create-employee [name="salary"]').val(basic_salary);

                        /* find array of current user type */
                        var _d = EMPLOYEES_MODULE._desg[data.user_type];

                        if(typeof _d !== "undefined"){
                            /* need to check if designation found  */
                            var _f = _d.findIndex(function(x){return x==data.designation})>-1;
                            if(!_f){
                                /* seems designation not found if current user type, we need to append it */
                                _d.push(data.designation);
                            }
                        }

                        /* update the usertype and desg */
                        $('#kt-portlet__create-employee [name="user_type"]').val(data.user_type).trigger('change');
                        $('#kt-portlet__create-employee [name="designation"]').val(data.designation).trigger('change.select2');

                        /* update account selection */
                        var all_accounts = false;
                        if(typeof data.props !== "undefined" && typeof data.props.all_accounts !== "undefined")all_accounts=data.props.all_accounts;

                        $('#kt-portlet__create-employee .accounts_container select').val(null).trigger("change.select2");
                        if(!all_accounts){
                            /* we need to select the options */
                            var accounts = data.accounts;
                            if(accounts.length){
                                var accounts_values=accounts.map(function(x){return x._id});
                                $('#kt-portlet__create-employee .accounts_container select').val(accounts_values).trigger("change.select2");
                            }
                        }

                        $('#kt-portlet__create-employee .accounts_container [name=grant_all_accounts]').prop('checked', all_accounts).trigger('change');


                        /* hide some fields on edit, like passwords */
                        $('#kt-portlet__create-employee .hide_on_edit').hide();
                        $('#kt-portlet__create-employee .hide_on_add').show();
                    }
                    else{
                        /* cannot laod the job now */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: 'Cannot load employee',
                            html: 'employee is processing.. Please retry after some time',
                        });
                    }
                    kingriders.Utils.isDebug() && console.log('loaded_data', data);
                },
            }
        };
    }();
    $(function(){
        $('#kt-portlet__create-employee .btnSubmit').on('click', function(){
            /* Do some validation */
            var is_valid=true;
            var form = $('#kt-portlet__create-employee form');

            /* remove all error messages */
            $('#kt-portlet__create-employee .invalid-feedback').remove();
            $('#kt-portlet__create-employee .is-invalid').removeClass('is-invalid');

            /* Fetch values */
            var name = $('#kt-portlet__create-employee [name=name]').val();
            var email = $('#kt-portlet__create-employee [name=email]').val();
            var org_email = $('#kt-portlet__create-employee [name=email]').attr('org-email');
            var pass = $('#kt-portlet__create-employee [name=password]').val();
            var c_pass = $('#kt-portlet__create-employee [name=password_confirmation]').val();
            var pass_change = $('#kt-portlet__create-employee [name=change_password]').is(':checked');

            var designation = $('#kt-portlet__create-employee [name=designation]').val();
            var user_type = $('#kt-portlet__create-employee [name=user_type]').val();


            /* remove extra whitespaces */
            name && (name = name.trim());
            email && (email = email.trim());
            designation && (designation = designation.trim());
            user_type && (user_type = user_type.trim());

            /* Check for empty input */
            if(!name){
                is_valid=false;
                $('#kt-portlet__create-employee [name=name]').addClass('is-invalid').after(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }
            if(!designation){
                is_valid=false;
                $('#kt-portlet__create-employee [name=designation]').parent().append(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }
            if(!user_type){
                is_valid=false;
                $('#kt-portlet__create-employee [name=user_type]').parent().append(`
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>This field is required.</strong>
                    </span>
                `);
            }
            if(!email){
                is_valid=false;
                $('#kt-portlet__create-employee [name=email]').addClass('is-invalid').after(`
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
                    if(!EMPLOYEES_MODULE.check_uniq(email)){
                        email_found=true;
                    }
                }
                else{
                    if(!EMPLOYEES_MODULE.check_uniq(email, org_email)){
                        email_found=true;
                    }
                }

                if(email_found){
                    is_valid=false;
                    $('#kt-portlet__create-employee [name=email]').addClass('is-invalid').after(`
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
                    $('#kt-portlet__create-employee [name=password]').addClass('is-invalid').after(`
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
                        $('#kt-portlet__create-employee [name=password]').addClass('is-invalid').after(`
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>The password must be at least 8 characters.</strong>
                            </span>
                        `);
                    }
                    else{
                        /* Check for password confirmation */
                        if(pass!==c_pass){
                            is_valid=false;
                            $('#kt-portlet__create-employee [name=password]').addClass('is-invalid').after(`
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
                $('#kt-portlet__create-employee form').trigger('submit');
            }

        });

        /* for conveinience purpose */
        $('#kt-portlet__create-employee input:not([type=hidden])').on('keypress', function(e){
            if(e.keyCode === 13){
                /* Enter key is pressed */
                $('#kt-portlet__create-employee .btnSubmit').trigger('click');
            }
        });

        /* for employee accounts module */
        $('#kt-portlet__create-employee [name=grant_all_accounts]').on('change', function(){
            var is_checked = $(this).is(':checked');

            if(is_checked){
                /* remove all account selection */
                $('#kt-portlet__create-employee .accounts_container select').val(null).trigger("change");

                /* hide the account selector */
                $('#kt-portlet__create-employee .accounts_container select').prop("disabled", true);

            }
            else{
                /* show account selector */
                $('#kt-portlet__create-employee .accounts_container select').prop("disabled", false);
            }
        });

        $('#kt-portlet__create-employee [name="user_type"]').on('change', function(e){
            /* update the designation dropdown accordingly  */
            var desgElem = $('#kt-portlet__create-employee [name="designation"]');
            kingriders.Plugins.update_select2(desgElem[0]);
            desgElem.val(null).trigger('change.select2');
        });


        $('#kt-portlet__create-employee [name="change_password"]').on('change', function(e){
            if($(this).is(':checked'))$('#kt-portlet__create-employee .changep').show();
            else $('#kt-portlet__create-employee .changep').hide();
        });


        if(typeof KINGVIEW !== "undefined"){
            /* Seems page was loaded in OnAir, reset page */
            $('#kt-portlet__create-employee form').attr('action', $('#kt-portlet__create-employee form').attr('data-add')).find('[name=user_id]').remove();
            EMPLOYEES_MODULE.Utils.reset_page();
        }

        /* Check if page has config, do accordingly */
        @isset($config)
        /* This will help us in loading page as edit & view */
        @isset($config->user)
        var _DataLoaded = {!! $config->user !!};
        EMPLOYEES_MODULE.Utils.load_page(_DataLoaded);
        @endisset

        @endisset


    });

</script>
@endsection

