@extends('Tenant.layouts.app')

@section('page_title')
    Create Addon
@endsection
@section('head')

    <style kr-ajax-head>
        .job-content__item-container{
            min-height: 200px;
        }
        #kt-portlet__create-addon table.datatable thead{
            background: #f9f9f9;
        }
        #kt-portlet__create-addon table.datatable thead th:nth-of-type(2) {
            width:25%;
        }
        #kt-portlet__create-addon table.datatable thead th:nth-of-type(3) {
            width:15%;
        }
        #kt-portlet__create-addon table.datatable thead th:nth-of-type(4) {
            width:60%;
        }
        #kt-portlet__create-addon table.datatable > tbody tr td {
            vertical-align:middle;
            text-align:center;
        }
        /* row__is-invalid, row__is-processing, row__is-completed */
        #kt-portlet__create-addon tr.row__is-invalid{
            background: #ffd7d759;
            box-shadow: inset 0.5px 0.5px 3px 0px #e22b2b;
        }
        tr.row__is-processing {
            background: #f5f5f559;
            box-shadow: inset 0.5px 0.5px 3px 0px #b5b1b1;
            cursor: wait;
        }
        tr.row__is-processing input,
        tr.row__is-processing textarea{
            background-color: #f9f9f9 !important;
            pointer-events: none;
        }
        tr.row__is-completed {
            background: #d9ffd759;
            box-shadow: inset 0.5px 0.5px 3px 0px #36e22b;
        }
        .swal-custom--overflow {
           overflow-y: auto !important;
        }

        html.swal2-shown,
        body.swal2-shown {
            overflow: hidden !important;
        }
        .kr-btn__group .btn.active{
            color: #fff !important;
            background-color: #607bff !important;
            border-color: #4060ff !important;
        }
        .select2--animation{
            box-shadow: 0px 0px 5px 0px #1edd3f;
            -webkit-transition: box-shadow 300ms cubic-bezier(0, 0, 0.34, 1.36);
            -ms-transition: box-shadow 300ms cubic-bezier(0, 0, 0.34, 1.36);
            transition: box-shadow 300ms cubic-bezier(0, 0, 0.34, 1.36);
        }
        #kt-portlet__create-addon table.datatable thead th{
            padding: 5px 8px;
            font-weight: bold;
        }
        #kt-portlet__create-addon table.datatable > tbody td{
            padding: 5px 5px;
        }
        #kt-portlet__create-addon .job-content input,
        #kt-portlet__create-addon .job-content table.datatable > tbody textarea{
            padding: 4px 7px;
            height: auto;
        }
        #kt-portlet__create-addon table.datatable > tbody .btndelete{
            height: 1.5rem;
            width: 1.5rem;
        }
        #kt-portlet__create-addon .kt-portlet__head{
            min-height: 45px;
        }
        .low-opacity{
            opacity: .4;
        }
        .low-opacity input,
        .low-opacity input::placeholder {
            color: transparent;
        }
        #kt-portlet__create-addon .btnform:hover,
        #kt-portlet__create-addon .btnform:focus{
            background-color: initial !important;
        }

        #kt-portlet__create-addon .setting-child table thead{
            background: #f9f9f9;
        }
        #kt-portlet__create-addon .setting-child table thead th:nth-of-type(2) {
            width:40%;
        }
        #kt-portlet__create-addon .setting-child table thead th:nth-of-type(3) {
            width:30%;
        }
        #kt-portlet__create-addon .setting-child table thead th:nth-of-type(4) {
            width:20%;
        }
        #kt-portlet__create-addon .setting-child table thead th:nth-of-type(5) {
            width:10%;
        }



    </style>
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet m-0 pb-2" id="kt-portlet__create-addon" kr-ajax-content>
    <form class="kt-form" enctype="multipart/form-data" data-add="{{route('tenant.admin.addons.add')}}" action="{{route('tenant.admin.addons.add')}}" method="POST">
        @csrf

        <input type="hidden" name="source_type" value="{{ request()->get('type', 'driver') }}">

        <div class="kt-portlet__head d-block d-sm-flex">
            <div class="kt-portlet__head-label my-2 my-sm-0">
                <h3 class="kt-portlet__head-title">Create Addon</h3>
            </div>
            <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
        </div>
        <!--begin::Form-->

        <div class="kt-portlet__body py-2 px-4">

            <div class="job-content row">
                <div class="job-content__item col-md-12">
                    <div class="job-content__item-container border">
                        <table class="table border-0 table-bordered table-hover table-checkable datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th> {{request()->get('type', 'driver') === "driver" ? "Driver" : "Vehicle"}} </th>
                                    <th>Additional Fields</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>


                    </div>
                    <div class="job-content__item-footer border border-top-0 py-2 px-3">
                        <button type="button" class="btn btn-outline-info btn-elevate btn-square btn-sm btnadd_row" onclick="ADDONS_MODULE.append_row();ADDONS_MODULE.calculate_subtotal();">
                            <i class="flaticon2-plus-1"></i>
                            Add Row
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger error-alert m-0 mt-3" role="alert" style="display: none;">
                        <div class="alert-text"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid py-2">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button type="submit" class="btn btn-brand">Save</button>

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
                                    HANDLEBARS TEMPLAATES
    --------------------------------------------------------------------------------}}

    {{-- ADD ROW TEMPLATE --}}
    @include('Tenant.addons.handlebars_templates.add_row')

    {{-- ADD CHILD ROW TEMPLATE --}}
    @include('Tenant.addons.handlebars_templates.add_child_row')

    {{-- ADD SETTING CHILD ROW TEMPLATE --}}
    @include('Tenant.addons.handlebars_templates.add_setting_child_row')


    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">

        var ADDONS_MODULE = function(){

            var table = $(this.container+' .datatable');

            var calculate_subtotal = function(){

            }

            var update_indices=function(){

                // Parent table indices
                $(ADDONS_MODULE.container+' .datatable > tbody > tr:not(.child):not(.setting-child)')
                .each(function(rowIndex, elem){

                    /* update names */
                    $(this).find('[data-name]').attr('name', function(index, attr){
                        var name = $(this).attr('data-name');
                        $(this).attr('name', 'items['+rowIndex+']['+name+']');
                    });

                    /* update SR # */
                    $(this).find('.srno').text(rowIndex+1);
                });

                // Setting table indices
                $(ADDONS_MODULE.container+' .datatable > tbody > tr:not(.child):not(.setting-child)')
                .each(function(rowIndex, elem){

                    $(elem).nextAll('.setting-child').eq(0).find('.setting-table > tbody > tr')
                    .each(function(childRowIndex, elem){

                        /* update names */
                        $(this).find('[data-name]').attr('name', function(index, attr){
                            var name = $(this).attr('data-name');
                            $(this).attr('name', 'items['+rowIndex+'][overrides]['+childRowIndex+']['+name+']');
                        });

                        /* update SR # */
                        $(this).find('.srno').text(childRowIndex+1);
                    });
                });
            }
            return {
                all_addons: {!! $addons !!},
                container: '#kt-portlet__create-addon',
                calculate_subtotal:function(is_minimal=false){
                    /* clear the errors */
                    ADDONS_MODULE.errors.clear();

                    /* check for error rows (which have no part selected) */
                    ADDONS_MODULE.validate_rows();

                    if(!is_minimal) update_indices();

                    /* calculate the amount through each loop*/
                    calculate_subtotal();

                },

                types: function(id = null){
                    var types = {!! $types !!};
                    if(id){
                        // Search in types
                        return types.find(function(item){return item.id === id});
                    }
                    return types;
                },
                sources: function(){
                    return {!! $sources !!};
                },

                Utils:{

                    reset_page:function(force=false){
                        /* clear the page if only job id found */
                        if($(ADDONS_MODULE.container+' [name=partinvoice_id]').length || force){
                            $(ADDONS_MODULE.container+' form [name=partinvoice_id]').remove();

                            /* clear the items */
                            $(ADDONS_MODULE.container+' .datatable > tbody tr').remove();

                            /* append blacnk row */
                            ADDONS_MODULE.append_row();

                            /* recalculate data (values zero) */
                            ADDONS_MODULE.calculate_subtotal();
                        }

                        $(ADDONS_MODULE.container)
                        .removeClass('kr-page__edit').removeClass('kr-page__add')
                        .addClass('kr-page__add');

                    },
                    load_page:function(invoice){

                        return '';
                    },

                },
                append_row:function(item=null){
                    var insertAfter=null;
                    if(typeof arguments[1]!=="undefined" && arguments[1])insertAfter=arguments[1];
                    var template = $('#handlebars-addrow').html();
                    // Compile the template data into a function
                    var templateScript = Handlebars.compile(template);

                    var index = $(ADDONS_MODULE.container+' .datatable > tbody > tr:not(.child):not(.setting-child)').length;
                    var context = {
                        index:index,
                        append:false
                    }
                    if(item){
                        context.item=item;
                        context.append=true;
                    }


                    var html = templateScript(context);

                    if(insertAfter) insertAfter.after(html);
                    else $(ADDONS_MODULE.container+' .datatable > tbody').append(html);


                    if(!item){
                        /* call the plugin of autosize */
                        //autosize($(ADDONS_MODULE.container+' textarea'));

                    }


                    // to refresh select2
                    kingriders.Plugins.refresh_plugins();


                },
                append_child_row:function(e, self){
                    if(e) e.preventDefault();

                    var rowNode = $(self).parents('tr');
                    if(self.hasAttribute('data-child-rendered')){

                        // Just hide/show child row

                        if(self.hasAttribute('data-child-show')){
                            // child is already shown, hide it
                            rowNode.nextAll('.child').eq(0).hide();
                            self.removeAttribute('data-child-show');

                            self.innerHTML = `<i class="fa fa-caret-right"></i> Show`;

                        }
                        else{
                            // child is hidden, show it
                            rowNode.nextAll('.child').eq(0).show();
                            self.setAttribute('data-child-show', '1');

                            self.innerHTML = `<i class="fa fa-caret-down"></i> Hide`;
                        }


                        return;

                    }

                    var type_id = rowNode.find('[data-name="type_id"]').val();
                    var item = ADDONS_MODULE.types(type_id);

                    var template = $('#handlebars-addchildrow').html();
                    // Compile the template data into a function
                    var templateScript = Handlebars.compile(template);

                    var index = $(ADDONS_MODULE.container+' .datatable > tbody > tr:not(.child):not(.setting-child)').index(rowNode);
                    var metadata = structuredClone(item.metadata);

                    metadata.categories = metadata.categories.map(function(item){
                        item.field_type = item.field_type ?? "text";
                        var field_values = item.field_values ?? "";
                        field_values = field_values.split(',').map(x=>x.trim());

                        item.field_values = field_values;

                        return item;
                    });

                    var context = {
                        index:index,
                        item:metadata
                    }


                    var html = templateScript(context);

                    rowNode.after(html);
                    rowNode.nextAll('.child').eq(0).hide();

                    // change text of button
                    self.setAttribute('data-child-rendered', '1');
                },

                handleOverrideSetting:function(e, self){
                    if(e) e.preventDefault();

                    var rowNode = $(self).parents('tr');
                    if(self.hasAttribute('data-setting-child-rendered')){

                        // Just hide/show setting-child row

                        if(self.hasAttribute('data-setting-child-show')){
                            // setting-child is already shown, hide it
                            rowNode.nextAll('.setting-child').eq(0).hide();
                            self.removeAttribute('data-setting-child-show');

                        }
                        else{
                            // setting-child is hidden, show it
                            rowNode.nextAll('.setting-child').eq(0).show();
                            self.setAttribute('data-setting-child-show', '1');
                        }


                        return;

                    }

                    var type_id = rowNode.find('[data-name="type_id"]').val();
                    var item = ADDONS_MODULE.types(type_id);

                    var template = $('#handlebars-addsettingchildrow').html();
                    // Compile the template data into a function
                    var templateScript = Handlebars.compile(template);

                    var index = $(ADDONS_MODULE.container+' .datatable > tbody > tr:not(.child):not(.setting-child)').index(rowNode);
                    var metadata = structuredClone(item.metadata)


                    metadata.types = metadata.types.map(function(item){

                        // If display title not found, make it title
                        if(!item.display_title) item.display_title = item.title;

                        return item;
                    });

                    var context = {
                        index:index,
                        item:metadata,
                        append:true
                    }


                    var html = templateScript(context);

                    rowNode.after(html);
                    rowNode.nextAll('.setting-child').eq(0).hide();

                    // change text of button
                    self.setAttribute('data-setting-child-rendered', '1');
                },

                setting_module: {

                    delete_row:function(self){
                        $(self).closest('tr').remove();

                        var p = $(self).closest('.setting-table');

                        /* check if no rows present */
                        if(p.find('tbody tr').length==0){
                            /* append a blank row */
                            ADDONS_MODULE.setting_module.append_row();
                        }

                        ADDONS_MODULE.calculate_subtotal();

                    },

                    append_row: function(e, self){

                        var tr = $(self).closest('tr');
                        var table = tr.find('table');

                        var template = $('#handlebars-addsettingchildrow').html();
                        // Compile the template data into a function
                        var templateScript = Handlebars.compile(template);

                        var root_index = tr.data('index');
                        var index = table.find(' tbody tr').length;
                        var context = {
                            root_index:root_index,
                            index: index,
                            item:null,
                            append:false
                        }

                        var html = templateScript(context);

                        table.find('tbody').append(html);
                    }

                },

                delete_row:function(self){
                    $(self).parents('tr').remove();

                    /* check if no rows present */
                    if($(ADDONS_MODULE.container+' .datatable > tbody tr').length==0){
                        /* append a blank row */
                        ADDONS_MODULE.append_row();
                    }

                    this.calculate_subtotal();

                },
                validate_rows:function(){

                    var is_validate=true;

                    /* reset rows (remove error class) */
                    $(ADDONS_MODULE.container+' .datatable > tbody > tr').removeClass('row__is-invalid');

                    /* loop through all rows and detect if part is selected */
                    $(ADDONS_MODULE.container+' .datatable > tbody > tr').each(function(index, el){
                        var is_valid=true;

                        if($(this).hasClass('child')){

                            var innerIndex = $(this).data('index');

                            // Validate Textbox / Radio / Checkbox
                            var els = $(this).find('[data-required]');
                            els.each(function(){
                                var val = this.value;
                                var name = this.getAttribute('placeholder');
                                if($(this).is('label')){

                                    // Either checkbox or radio
                                    var forInput = this.getAttribute('for');

                                    if(!$('[name="'+forInput+'"]').is(':checked')){
                                        is_valid=false;

                                        ADDONS_MODULE.errors.make(name+" is required at additional details of row #"+(innerIndex+1));
                                    }
                                }
                                else{

                                    val && (val=val.trim());

                                    if(!val){
                                        is_valid=false;

                                        ADDONS_MODULE.errors.make(name+" is required at additional details of row #"+(innerIndex+1));
                                    }
                                }
                            });


                        }
                        else if($(this).hasClass('setting-child')){

                            if($(this).is(':visible')){

                                var innerIndex = $(this).data('index');

                                var els = $(this).find('[data-required]');
                                els.each(function(){
                                    var val = this.value;
                                    var name = this.getAttribute('data-title');
                                    val && (val=val.trim());

                                    if(!val){
                                        is_valid=false;

                                        ADDONS_MODULE.errors.make(name+" is required at overrides of row #"+(innerIndex+1));
                                    }
                                });

                            }

                        }
                        else{

                            var innerIndex = $(ADDONS_MODULE.container+' .datatable > tbody > tr:not(.child):not(.setting-child)').index(this);

                            var type_id = $(this).find('[data-name="type_id"]').val();
                            type_id && (type_id=type_id.trim());
                            if(!type_id)is_valid=false;

                            var price = $(this).find('[data-name="price"]').val();
                            price && (price=price.trim());
                            if(!price)is_valid=false;

                            var sourceEl = this.querySelector('[data-name="source_id"]');
                            var source_id = sourceEl.value;
                            source_id && (source_id=source_id.trim());
                            if(!source_id && sourceEl.hasAttribute('data-required')){
                                is_valid=false;

                                ADDONS_MODULE.errors.make("Driver is required at row #"+(innerIndex+1));
                            }

                            if(is_valid){
                                // Check if duplicate addon
                                var addonExists = ADDONS_MODULE.all_addons.some(function(addon){
                                    return addon.setting_id == type_id && addon.source_id == source_id;
                                });
                                if(addonExists){
                                    is_valid=false;

                                    ADDONS_MODULE.errors.make("Addon already exists - at row #"+(innerIndex+1));
                                }
                            }
                        }


                        if(!is_valid){
                            /* add error class to this row */
                            $(this).addClass('row__is-invalid');

                            /* global validation, if any rows found invalid, */
                            is_validate=false;
                        }
                    });

                    return is_validate;
                },
                errors:{
                    clear:function(){
                        $(ADDONS_MODULE.container+' .error-alert').hide().find('.alert-text').html('');
                    },
                    make:function(html){
                        $(ADDONS_MODULE.container+' .error-alert').show().find('.alert-text').html(html);
                    }
                },
                save_progress:function(){

                    var is_valid=true;

                    /* clear the errors */
                    ADDONS_MODULE.errors.clear();

                    /* total must be greater than 0 */
                    if(!ADDONS_MODULE.validate_rows()){

                        is_valid=false;
                    }

                    if(is_valid){

                        $(ADDONS_MODULE.container+' form').trigger('submit');
                    }
                    else{
                        return; /* terminate the process */
                    }


                    if($(ADDONS_MODULE.container+' [name=addon_id]').length){
                        // edit page

                        /* hide the modal (if found) */
                        var MODAL = $(ADDONS_MODULE.container+'').parents('.modal');
                        if(MODAL.length){
                            MODAL.modal('hide');
                        }
                    }
                    else{
                        // add page

                        /* show toast of success (on adding) */
                        toastr.info("Addons are created successfully", "Success!");

                        /* reset the page */
                        ADDONS_MODULE.Utils.reset_page(true);
                    }

                },
            };
        }();

        $(function(){
            ADDONS_MODULE.append_row();

            ADDONS_MODULE.calculate_subtotal();


            @if(request()->get('type', 'driver') === 'vehicle')
                /* Change of source [Vehicle only] Add type field */
                $(ADDONS_MODULE.container+' form .datatable').on('change', '[data-name="source_id"]', function(e){
                    var text = $(this).find(':selected').text();
                    var parent = this.closest('tr');
                    var vehicleTypeEl = parent.querySelector('[data-name="vehicle_type"]');
                    if(vehicleTypeEl){
                        // If we found "B#" in value, it is booking else vehicle
                        // Since all bookings are prefixed with B# and vehicles are with V#
                        // If any vehicle not assigned to any booking means TMP vehicle
                        // then else case will be executed since they has no prefixed with them
                        vehicleTypeEl.value = text.indexOf('B#') > -1 ? 'booking' : 'vehicle'
                    }
                });
            @endif

            /* do some validation (Save Progress) */
            $(ADDONS_MODULE.container+' form :submit').on('click', function(e){
                e.preventDefault();

                ADDONS_MODULE.save_progress();

            });

            /* preload the kr-ajax module (only if laoded in modal) */
            var MODAL = $(ADDONS_MODULE.container+'').parents('.modal');
            if(MODAL.length){
                setTimeout(function(){
                    $(ADDONS_MODULE.container+' '+kingriders.Plugins.Selectors.kr_ajax_preload).each(function(i, elem){
                        /* initiate the ajax */
                        $(this).trigger('click.krevent', {
                            preload:true
                        });
                    });
                },100);
            }


            $(ADDONS_MODULE.container+' .datatable').on('keypress','input, textarea', function(e){
                if(e.ctrlKey && (e.keyCode == 10 || e.keyCode == 13)){
                    /* ctrl + enter pressed, show new part form */

                    /* we need to show form to create this record */
                    var btn = $(ADDONS_MODULE.container+' [data-create-part]');
                    if(btn.length){
                        /* set the active element as this */
                        ADDONS_MODULE.part_module.active_from=this;

                        /* click the btn to open the form */
                        btn.trigger('click');
                    }
                }
                else if(e.keyCode === 13){
                    /* Enter key is pressed */
                    e.preventDefault(); // Ensure it is only this code that runs

                    /* append a blank row */
                    ADDONS_MODULE.append_row(null,$(this).parents('tr'));

                    /* recalculate data (values zero) */
                    ADDONS_MODULE.calculate_subtotal();

                    /* focus on 1st elem on new row */
                    $(this).parents('tr').next().find('textarea').focus();
                }
            });

            $(ADDONS_MODULE.container+' .datatable').on('keydown','input, textarea', function(e){

                if(e.shiftKey && e.keyCode==38){ /* Move up */
                    /* shift+tab is pressed */
                    e.preventDefault();

                    var elem=this;
                    var alies = elem.getAttribute('data-name');
                    var tr = $(elem).closest('tr');
                    var prevRow = tr.prev();
                    /* check if row not found */
                    if(prevRow.length==0){
                        /*seems ths is the 1st row and there wasn't any row before it, just forward it to last */
                        prevRow = tr.parents('tbody').find('tr:last-child');
                    }
                    prevRow.find('[data-name="'+alies+'"]').focus();
                }
                else if(e.shiftKey && e.keyCode==40){ /* Move down */
                    /* tab key is pressed */
                    e.preventDefault();

                    var elem=this;
                    var alies = elem.getAttribute('data-name');
                    var tr = $(elem).closest('tr');
                    var nextRow = tr.next();
                    /* check if row not found */
                    if(nextRow.length==0){
                        /*seems ths is the last row and there wasn't any row below it, just forward it to 1st row */
                        nextRow = tr.parents('tbody').find('tr:first-child');
                    }
                    nextRow.find('[data-name="'+alies+'"]').focus();
                }
                else if(e.shiftKey && e.keyCode==37){/* Move left */
                    e.preventDefault();

                    var elem=this;
                    var alies = elem.getAttribute('data-name');
                    var td = $(elem).closest('td');
                    var prevCell = td.prev();
                    /* check if row not found */
                    if(prevCell.length==0 || prevCell.find('input:not(.tt-hint),textarea:not(.tt-hint)').length==0){
                        /*seems ths is the last row and there wasn't any row below it, just forward it to 1st row */
                        prevCell = td.parents('tr').find('input:not(.tt-hint),textarea:not(.tt-hint)').eq(-1).parents('td');
                    }
                    prevCell.find('input:not(.tt-hint),textarea:not(.tt-hint)').focus();
                }
                else if(e.shiftKey && e.keyCode==39){/* Move right */
                    e.preventDefault();

                    var elem=this;
                    var alies = elem.getAttribute('data-name');
                    var td = $(elem).closest('td');
                    var nextCell = td.next();

                    /* check if row not found */
                    if(nextCell.length==0 || nextCell.find('input:not(.tt-hint),textarea:not(.tt-hint)').length==0){
                        /*seems ths is the last row and there wasn't any row below it, just forward it to 1st row */
                        nextCell = td.parents('tr').find('input:not(.tt-hint),textarea:not(.tt-hint)').eq(0).parents('td');
                    }
                    nextCell.find('input:not(.tt-hint),textarea:not(.tt-hint)').focus();
                }
                else if(e.keyCode === 46){
                    /* delete key is pressed */
                    e.preventDefault(); // Ensure it is only this code that runs

                    var nextRow = $(this).parents('tr').next();
                    var deleteBtn = $(this).parents('tr').find('.btndelete')[0];

                    /* delete this */
                    ADDONS_MODULE.delete_row(deleteBtn);

                    /* focus on 1st elem on new row */
                    if(nextRow.length) nextRow.find('textarea').focus();
                    else $(ADDONS_MODULE.container+' .datatable > tbody tr:last-child textarea').focus();


                }
            });

            $(ADDONS_MODULE.container+' .datatable').on('change','select[data-name="type_id"]', function(e){
                var value = this.value;
                var data = ADDONS_MODULE.types(value);

                var rowNode = $(this).parents('tr');
                rowNode.find('[data-name="source_id"]').removeAttr('data-required').val(null).trigger('change.select2');
                rowNode.find('[data-name="price"]').val(null);
                rowNode.find('.additional-data-container').html('');
                rowNode.find('.override-container [type="checkbox"]').prop('disabled', true);
                if(rowNode.nextAll('.child').eq(0).length > 0){
                    rowNode.nextAll('.child').eq(0).remove();
                }
                if(rowNode.nextAll('.setting-child').eq(0).length > 0){
                    rowNode.nextAll('.setting-child').eq(0).remove();
                }


                if(!!data && !!data.metadata){
                    if(data.metadata.amount > 0){

                        rowNode.find('[data-name="price"]').val(data.metadata.amount).trigger('input');

                        /* add animation */
                        rowNode.find('[data-name="price"]').addClass('krselect2--animation');

                        setTimeout(function () {
                            rowNode.find('[data-name="price"]').removeClass('krselect2--animation');
                        }, 2500);
                    }

                    if(!!data.metadata.source_required){
                        rowNode.find('[data-name="source_id"]').attr('data-required', 1);
                    }

                    if(!!data.metadata.categories && data.metadata.categories.length > 0){
                        rowNode.find('.additional-data-container').html('<a href="#" onclick="ADDONS_MODULE.append_child_row(event, this); ADDONS_MODULE.calculate_subtotal(true);"><i class="fa fa-caret-right"></i> Show</a>')

                        var el = rowNode.find('.additional-data-container a');
                        ADDONS_MODULE.append_child_row(null, el[0]);
                    }

                    if(!!data.metadata.types && data.metadata.types.length > 0){
                        rowNode.find('.override-container [type="checkbox"]').prop('disabled', false);
                        var el = rowNode.find('.override-container [type="checkbox"]');
                        ADDONS_MODULE.handleOverrideSetting(null, el[0]);
                    }
                }

            });


            if(typeof KINGVIEW !== "undefined"){
                /* Seems page was loaded in OnAir, reset page */
                $(ADDONS_MODULE.container+' form').attr('action', $(ADDONS_MODULE.container+' form').attr('data-add'));
                if(typeof ADDONS_MODULE !== "undefined")ADDONS_MODULE.Utils.reset_page();
            }

        });
    </script>
@endsection
