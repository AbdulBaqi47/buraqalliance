@extends('Tenant.layouts.app')

@section('page_title')
    Create @if($type === 'driver') Driver @elseif($type === 'staff') Staff @else Vehicle @endif
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__create-addonexpense" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create @if($type === 'driver') Driver @elseif($type === 'staff') Staff @else Vehicle @endif Addon Expense</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form " enctype="multipart/form-data" data-add="{{route('tenant.admin.addons.expense.add', $type)}}" data-edit="{{route('tenant.admin.addons.expense.edit')}}" action="{{route('tenant.admin.addons.expense.add', $type)}}" method="POST">
        @csrf
        <input type="hidden" name="source_type" value="{{$type}}">
        <input type="hidden" name="display_title">

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
                <label>Addon:</label>
                <select class="form-control kr-select2" data-source="ADDON_EXPENSE_MODULE.addons()" name="addon" required>
                    <option></option>
                </select>
                @error('type')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please choose addon</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Type:</label>
                <select class="form-control kr-select2" data-source="ADDON_EXPENSE_MODULE.types()" name="type" required>
                    <option></option>
                </select>
                <div class="alert alert-warning alert-bold m-0 mt-2 invalid-feedback-type" style="display: none" role="alert">
                    <div class="alert-text">Expense already exists with this title!</div>
                </div>
                @error('type')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @else
                <span class="form-text text-muted">Please choose category</span>
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
                <span class="form-text text-muted">Please enter details about this expense</span>
                @enderror

            </div>

            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="0.001" kr-accounts-input required name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{old('amount')}}">
                @error('amount')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>

            <div class="form-group">
                <div class="kt-checkbox-inline mt-1">
                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                        <input type="checkbox" name="from_source"> Charge From @if($type === 'driver') Driver @elseif($type === 'staff') Staff @else Vehicle @endif?
                        <span></span>
                    </label>
                </div>

                <div class="chargeable-field" style="display:none;">
                    <input type="number" step="0.001" name="charge_amount" class="form-control @error('charge_amount') is-invalid @enderror" value="{{old('charge_amount')}}">
                    @error('charge_amount')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @else
                    <span class="form-text text-muted">Please enter total charge amount</span>
                    @enderror
                    <div id="warning" class="alert alert-warning fade mt-2" role="alert">
                        <div class="alert-text">Charge Amount is Less Than Actual Amount!</div>
                    </div>
                </div>

            </div>



            <div class="form-group">
                <label>Attachment:</label>
                <div class="kt-uppy kr-uppy uppy-invoice_tax_img" uppy-size="20" uppy-max="1" uppy-min="1" uppy-label="Add Attachment" uppy-input="attachment"></div>
                <span class="form-text text-muted">Max file size is 20MB and max number of files is 1.</span>
            </div>

        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    @include('Accounts.widgets.account_selector')
                </div>
                <div class="d-flex">
                    <div class="kt-form__actions kt-form__actions--right">
                        <button type="submit" class="btn btn-brand">Save</button>
                    </div>
                </div>
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
    $(function(){
        $(ADDON_EXPENSE_MODULE.container+' [name=charge_amount]').on('input',(e) => {
            let thisEl = $(e.target);
            let warningEl = $('#warning');
            let amountEl = $(ADDON_EXPENSE_MODULE.container+' [name=amount]');
            // let submitBtn = $('[type=submit]');
            let amount = Number(amountEl.val());
            let charge_amount = Number(thisEl.val());
            // Reset Old Values
            warningEl.removeClass('show');
            // submitBtn.prop('disabled', false);
            if(charge_amount < amount){
                warningEl.addClass('show');
                // submitBtn.prop('disabled', true);
            }
        });

        $(ADDON_EXPENSE_MODULE.container+' [name=from_source]').on('change', function(){
            var is_checked = $(this).is(':checked');

            if(is_checked){
                /* hide the other fields */
                $(ADDON_EXPENSE_MODULE.container + ' .chargeable-field').show().find('input').prop('required', true);
            }
            else{
                /* show other fields */
                $(ADDON_EXPENSE_MODULE.container + ' .chargeable-field').hide().find('input').prop('required', false);
            }
        });

        $(ADDON_EXPENSE_MODULE.container+' [name=addon]').on('change', function(){
            $(ADDON_EXPENSE_MODULE.container+' .invalid-feedback-type').hide();

            // Change types source
            kingriders.Plugins.update_select2(document.querySelector(ADDON_EXPENSE_MODULE.container+' [name=type]'));

            $(ADDON_EXPENSE_MODULE.container+' [name=display_title]').val(null);
        });

        $(ADDON_EXPENSE_MODULE.container+' [name=type]').on('change', function(){
            // Fetch current type, Select "Charge to {source}" based on it
            var value = this.value;

            $(ADDON_EXPENSE_MODULE.container+' .invalid-feedback-type').hide();
            $(ADDON_EXPENSE_MODULE.container+' [name=display_title]').val(null);

            var types = ADDON_EXPENSE_MODULE.findTypes();
            if(types.length > 0){
                var type = types.find(function(t){return t.title.trim().toLowerCase() == value.trim().toLowerCase()});
                if(!!type){
                    $(ADDON_EXPENSE_MODULE.container+' [name=from_source]').prop('checked', type.charge).trigger('change');

                    if(type.hasOwnProperty('display_title') && type.display_title != ''){

                        $(ADDON_EXPENSE_MODULE.container+' [name=display_title]').val(type.display_title);
                    }
                    else{
                        $(ADDON_EXPENSE_MODULE.container+' [name=display_title]').val(type.title);

                    }

                    // pre-load amount
                    if(typeof type.amount !== "undefined"){
                        if(type.amount && type.amount > 0){
                            $(ADDON_EXPENSE_MODULE.container+' [name=amount]').val(type.amount)[0].trigger('input');
                        }
                        else{
                            $(ADDON_EXPENSE_MODULE.container+' [name=amount]').val(null);

                        }

                    }


                    // Need to check if this addon is not charged already
                    var addon = ADDON_EXPENSE_MODULE.findAddon();
                    if(addon && addon.hasOwnProperty('expenses') && addon.expenses.some(x=>x.type.trim().toLowerCase() == type.title.trim().toLowerCase())){
                        // this addon it already charged, show some warning

                        $(ADDON_EXPENSE_MODULE.container+' .invalid-feedback-type').show();
                    }
                }
            }

        });
    });

    var ADDON_EXPENSE_MODULE={
        all_addons:{!! $all_addons !!},

        addons(){
            return {!! $addons !!};
        },
        types(){
            var types = this.findTypes()
            .map(function(type, index){
                return {
                    id: type.title,
                    text: type.title
                }
            });

            return [{id: '', text: 'Select an option'}, ...types];
        },

        findTypes(){

            // - Find select addon
            // - in setting -> get types

            var selectedAddon = this.findAddon();
            if(selectedAddon){

                // if settings are overrided, use that
                if(selectedAddon.hasOwnProperty('override_types') && !!selectedAddon.override_types){
                    return selectedAddon.override_types;
                }

                if(selectedAddon.setting.hasOwnProperty('types')){
                    return selectedAddon.setting.types;
                }
            }

            return [];

        },

        findAddon(){

            var selectedAddonId = $(this.container+' [name=addon]').val();
            var addon  = this.all_addons.find(x=>x.id == selectedAddonId);
            if(typeof addon !== "undefined" && addon) return addon;

            return null;

        },
        container:'#kt-portlet__create-addonexpense',
        Utils:{

            reset_page:function(){

                /* clear the items */
                $(ADDON_EXPENSE_MODULE.container+' form [name=given_date]').datepicker('update', new Date(Date.now()).format('mmmm dd, yyyy'));
                $(ADDON_EXPENSE_MODULE.container+' form [name="month"]').datepicker('update', new Date(Date.now()).format('mmm yyyy'));
                $(ADDON_EXPENSE_MODULE.container+' form [name="type"]').val(null).trigger('change.select2');
                $(ADDON_EXPENSE_MODULE.container+' form [name="description"]').val(null);
                $(ADDON_EXPENSE_MODULE.container+' form [name="amount"]').val(null).trigger('change');
                $(ADDON_EXPENSE_MODULE.container+' form [name="from_source"]').prop('checked', false).trigger('change');
                $(ADDON_EXPENSE_MODULE.container+' form [name="charge_amount"]').val(null);

                var uppy = kingriders.Plugins.uppy.getInstance($(ADDON_EXPENSE_MODULE.container+' form .uppy-invoice_tax_img').attr('id'));
                if(uppy){
                    /* remove all files */
                    $('.kt-uppy__list').html('');
                    $('input[type=file].kr-uppy-file').remove();

                    uppy.reset();
                }

            },
            load_page:function(client){
                return; /* NEED TO SET */
                /* Load the job in page (this funtion is using in view job page) */

                /* need to check if job is suitable for edit, (not in creating process) */
                if(client.actions.status==1){
                    /* check if page if loaded in modal */
                    var MODAL = $('#kt-portlet__create-client').parents('.modal');
                    if(MODAL.length){
                        MODAL.modal('show');
                    }

                    /* change the action of form to edit */
                    $('#kt-portlet__create-client form [name=client_id]').remove();
                    $('#kt-portlet__create-client form').attr('action', $('#kt-portlet__create-client form').attr('data-edit'))
                    .prepend('<input type="hidden" name="client_id" value="'+client.id+'" />');


                    /* load other data like bike,client */
                    $('#kt-portlet__create-client [name="name"]').val(client.name);
                    $('#kt-portlet__create-client [name="email"]').val(client.email);
                    var is_walking=false;
                    if(client.walking_customer == 1)is_walking=true;
                    $('#kt-portlet__create-client [name="walking_customer"]').prop('checked', is_walking).trigger('change');
                    $('#kt-portlet__create-client [name="phone"]').val(client.phone);
                    $('#kt-portlet__create-client [name="trn"]').val(client.trn);
                    $('#kt-portlet__create-client [name="address"]').val(client.address);
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
    </script>
@endsection

