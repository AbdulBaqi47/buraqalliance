@extends('Tenant.layouts.app')

@section('page_title')
    Create Account
@endsection

@section('content')


<!--begin::Portlet-->
<div class="kt-portlet" id="kr-portlet__create-account" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Create Account</h3>
        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>
    <!--begin::Form-->
    <form class="kt-form" action="{{route('module.accounts.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__body">
            <div class="form-group">
                <label>Department:</label>
                <select class="form-control kr-select2" data-dynamic data-source="ACCOUNTS_MODULE.departments()" name="department" required>
                    <option></option>
                    
                </select>
            </div>
            <div class="kt-separator kt-separator--border-dashed kt-separator--space-xs"></div>
            <div class="form-group">
                <label>Type:</label>
                <select required class="form-control kr-select2" name="type">
                    <option value="main" data-for="bank,cih">Main</option>
                    <option value="tax" data-for="bank">Tax</option>
                    <option value="supplementary" data-for="bank">Supplementary</option>
                    <option value="beneficiary" data-for="bank">Beneficiary</option>
                    <option value="normal" data-for="cih">Normal</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Title:</label>
                <input type="text" autocomplete="off" name="title" required class="form-control @error('title') is-invalid @enderror" placeholder="Enter title" value="{{old('title')}}">
                @error('title')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                
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
    <script kr-ajax-head>
        var ACCOUNTS_MODULE=function(){

            return {
                accounts:{!! $accounts !!},
                departments:function(){
                    var temp=ACCOUNTS_MODULE.accounts.map(function(x){return x.department=='bank'?{id:'bank', text:'Bank'}:(x.department=='cih'?{id:'cih', text:'Cash in Hand'}:{id:x.department,text:x.department})});
                    /* Check if main and cih departments not found, we need to add it */
                    var mainF = ACCOUNTS_MODULE.accounts.findIndex(function(x){return x.department=='bank'})>-1;
                    if(!mainF)temp.push({id:'bank', text:'Bank'});

                    var cihF = ACCOUNTS_MODULE.accounts.findIndex(function(x){return x.department=='cih'})>-1;
                    if(!cihF)temp.push({id:'cih', text:'Cash in Hand'});


                    return temp.filter(function(item, index, self){
                        return index == self.findIndex(function(y){
                            return y.text.toLowerCase()==item.text.toLowerCase();
                        });
                    });
                },
                container:'#kr-portlet__create-account',
                Utils:{

                    reset_page:function(){

                        /* clear the items */

                        $(ACCOUNTS_MODULE.container).find('[name="type"]').val(null).trigger('change.select2');

                        var departmentElem=$(ACCOUNTS_MODULE.container).find('[name="department"]')[0];

                        /* update department select2 */
                        kingriders.Plugins.update_select2(departmentElem);

                        departmentElem.selectedIndex=0;
                        $(ACCOUNTS_MODULE.container).find('[name="department"]').trigger('change');

                        $(ACCOUNTS_MODULE.container).find('[name="title"]').val(null);

                        
                    },
                }
            };

        }();


        $(function(){
            /* listen department changes */
            $(ACCOUNTS_MODULE.container).find('form [name=department]').on('change', function(){
                var dep = $(this).val();

                /* find types to show */

                /* show all options */
                $(ACCOUNTS_MODULE.container)
                .find('form [name=type] option[data-for]')
                .prop('disabled', false);

                /* filters option based on department */
                $(ACCOUNTS_MODULE.container)
                .find('form [name=type] option[data-for]:not([data-for*="'+dep+'"])')
                .prop('disabled', true)
                .parents('select')
                .val(null)
                .trigger('change.select2');

                /* hide select element if all are disabled */
                $(ACCOUNTS_MODULE.container).find('form [name=type]').prop('required', true).parents('.form-group').show();
                if($(ACCOUNTS_MODULE.container).find('form [name=type] option:not(:disabled)').length==0){
                    /* all options are hidden */
                    $(ACCOUNTS_MODULE.container).find('form [name=type]').prop('required', false).parents('.form-group').hide();
                }

            });
        });
    </script>


    {{------------------------------------------------------------------------------
                                    STYLES (use in current page)
    --------------------------------------------------------------------------------}}
    <style kr-ajax-head>
        .select2-container--default .select2-results__option[aria-disabled=true] {
            display: none;
        }
    </style>

@endsection

