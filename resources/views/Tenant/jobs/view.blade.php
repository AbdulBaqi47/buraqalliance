@extends('Tenant.layouts.app')

@section('page_title')
    Invoices
@endsection
@section('head')
<link href=" https://printjs-4de6.kxcdn.com/print.min.css" rel="stylesheet">

<style>
    .invoice-number__text{
        line-height: 19px;
        opacity: .8;
        font-size: 11px;
    }
    .invoice__bill-to{
        display: block;
        font-size: 10px;
        color: #999;
        line-height: 11px;
    }
    #datatable [data-viewbtnhide]{
        opacity: .8;
        position: relative;
        pointer-events: none;
    }
    #datatable [data-viewbtnhide]::before {
        content: "";
        box-sizing: border-box;
        position: absolute;
        top: 50%;
        left: 40%;
        border-radius: 50%;
        border: 2px solid #5d78ff;
        border-right-color: transparent;
        animation: kt-spinner .5s linear infinite;
        width: 15px;
        height: 15px;
        margin-top: -7px;
    }

    .record-filter__container .select2-container .select2-selection.select2-selection--single{
        border-radius: 0px;
        border: 1px solid #5578eb;
    }
</style>
@endsection
@section('content')

<!--begin::Portlet-->


<div class="kt-portlet mt-5">

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Invoices</h3>
        </div>
        <div class="kt-portlet__head-label w-50">

            <div class="record-filter__container d-flex w-100">
                <div class="record-filter__item w-100">
                    <small class="text-uppercase">Select Month:</small>
                    <select class="form-control kr-select2" id="filter_date">
                        <option value="0" selected>Last 150 records</option>
                        @for ($i = 0; $i <= 12; $i++)
                            @php
                                $month = Carbon\Carbon::now()->startOfMonth()->addMonth(-$i);
                            @endphp
                            <option value="{{$month->format('Y-m-d')}}">{{$month->format('F Y')}}</option>
                        @endfor
                    </select>
                </div>

                <div class="record-filter__item w-100 ml-2">
                    <small class="text-uppercase">Select Client:</small>
                    <select class="form-control kr-select2" style="margin-left:10px" id="filter_client">
                        <option value="0" selected>All Clients</option>
                        @foreach ($clients as $client)
                            <option value="{{$client->id}}">{{$client->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
        <div class="kt-portlet__head-toolbar">
            <div>
                <select class="kr-bootstrapselect filter-selectorpicker mr-2" onchange="JOBS.events.filter_rows(this);" data-style="border-info bootstrap-selector-noborder" data-width="120">
                    <option selected value="">All</option>
                    <option value="In Progress">In Progress</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Completed">Completed</option>
                </select>

                @if ($helper_service->routes->has_access('tenant.admin.jobs.add'))
                <button type="button" kr-ajax-autohide="0" kr-ajax-modal-type="full" data-backdrop="static" kr-ajax-modalclosed="JOBS.events.modal_closed" kr-ajax-modalclosing="JOBS.events.modal_closing" kr-ajax-submit="JOBS.createjob_submit" kr-ajax-contentloaded="JOBS.jobmodal_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.jobs.add')}}" class="btn btn-info btn-elevate btn-square">
                    <i class="flaticon2-plus-1"></i>
                    Create Invoice
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="kt-portlet__body">

        <table class="table table-striped- table-bordered table-hover table-checkable" id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bike</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

    </div>
</div>

<!--end::Portlet-->




@endsection


@section('foot')


{{------------------------------------------------------------------------------
                            SCRIPTS (use in current page)
--------------------------------------------------------------------------------}}


<script type="text/javascript">
    var JOBS = function(){

        const FilterDateEl = document.getElementById('filter_date');
        const FilterClientEl = document.getElementById('filter_client');

        /* Event: Filter changed */
        $(FilterDateEl).on('change', function(){
            JOBS.Filter.DOM_to_url();

            /* reload table */
            JOBS.datatable.ajax.reload();
        });
        $(FilterClientEl).on('change', function(){
            JOBS.Filter.DOM_to_url();

            /* reload table */
            JOBS.datatable.ajax.reload();
        });


        /* Initialize the datatables */
        var init_table=function(){
            var table = JOBS.table;

            /* Show loading */
            KTApp.blockPage({
                type: 'v2',
                state: 'primary',
                message:"Processing..."
            });


            // begin first table
            JOBS.datatable = table.DataTable({
                responsive: true,
                searchDelay: 100,
                pageLength: 50,
                destroy:true,
                language: {
                    loadingRecords: '&nbsp;',
                    processing: ''
                },
                processing: true,
                serverSide: false,
                ajax: {
                    url:"{{route('tenant.admin.jobs.data')}}",
                    data:function(d){
                        /* Filter Data */
                        d.filter_date = FilterDateEl.value;
                        d.filter_client = FilterClientEl.value;
                    }
                },
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false, searchable: false},
                    {data: 'bike', orderable: false, width: "18%"},
                    {data: 'client.name', orderable: false, width: "18%"},
                    {data: 'date', orderable: false, width: "12%"},
                    {data: 'status', orderable: false, width: "12%"},
                    {data: 'total', orderable: false, width: "13%"},
                    {data: 'total', orderable: false, width: "10%"},
                    {data: 'created_at', visible:false, searchable: false},
                    {data: 'actions', width: "10%", searchable: false},
                ],
                order: [[7, 'desc']],
                columnDefs: [
                    {
                        targets: 1,
                        title: 'Bike',
                        orderable: false,
                        render: function(bike, type, full, meta) {
                            var result = '';

                            if(typeof bike.manufacturer !== "undefined" && bike.manufacturer )result+=bike.manufacturer;
                            if(typeof bike.cc !== "undefined" && bike.cc)result+=' '+bike.cc+'CC';
                            if(typeof bike.model !== "undefined" && bike.model)result+=(result!=''?' | '+bike.model:bike.model);

                            return '<b>'+bike.plate+'</b><span class="invoice-number__text d-block mt-n1">'+result+'</span>';
                        },
                    },
                    {
                        targets: 2,
                        orderable: false,
                        render: function(client_name, type, full, meta) {

                            if(typeof full.subclient !== "undefined" && full.subclient ){
                                var charged_to_rider = full.services.findIndex(function(x){ return x.ds_ref && x.ds_ref !== '';  }) > -1;
                                return client_name+'<span class="invoice__bill-to '+(charged_to_rider ? 'text-success' : '')+'"><b>Bill To: </b>'+full.subclient.name+'</span>'
                            }

                            return client_name;
                        },
                    },
                    {
                        targets: 4,
                        render: function(data, type, full, meta) {
                            // kingriders.Utils.isDebug() && console.log('data',data,type,full,meta);
                            var status = {
                                "open": {'title': 'Open', 'class': ' kt-badge--primary'},
                                "in_progress": {'title': 'In Progress', 'class': ' kt-badge--warning'},
                                "on_hold": {'title': 'On Hold', 'class': ' kt-badge--danger'},
                                "complete": {'title': 'Completed', 'class': ' kt-badge--success'},
                            };
                            if (typeof status[data] === 'undefined') {
                                return data;
                            }
                            return '<span class="kt-badge ' + status[data].class + ' kt-badge--inline kt-badge--pill">' + status[data].title + '</span>';
                        },
                    },
                    {
                        targets: 5,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            /* check if invoice generated, show invoice number too */
                            var extraHtml = '';

                            if(typeof full.invoice !== "undefined" && full.invoice){
                                var invoice_id = full.invoice.id;
                                if(full.invoice.payments){
                                    paid = full.invoice.payments.reduce(function(total,item){return total+(parseFloat(item.amount)||0)},0);
                                }
                                if(paid>0){
                                    var balance = (data-paid).toRound(2);
                                    if(balance>0){
                                    extraHtml=`
                                        <small class="d-block mb-n2 mt-1">
                                            <b>Paid: </b> ${paid}
                                        </small>
                                        <small>
                                            <b>Remaining: </b> ${balance}
                                        </small>`;
                                    }
                                }

                                return '<span class="d-flex justify-content-between">' + data + '<span class="invoice-number__text">#'+invoice_id+'</span></span>'+extraHtml;
                            }

                            return data;
                        },
                    },
                    {
                        targets: 6,
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var payment_status = 0;

                            if(typeof full.invoice !== "undefined" && full.invoice){
                                var invoice_id = full.invoice.id;
                                var paid=0;
                                if(full.invoice.payments){
                                    paid = full.invoice.payments.reduce(function(total,item){return total+(parseFloat(item.amount)||0)},0);
                                }
                                if(paid>0){
                                    var balance = (full.total-paid).toRound(2);
                                    if(balance==0)payment_status=1;
                                }
                            }

                            /* Check for manually paid */
                            if(typeof full.manually_paid !== "undefined" && full.manually_paid === true){
                                payment_status=2;
                            }

                            var status = {
                                0: {'title': 'Pending', 'class': ' kt-badge--metal'},
                                1: {'title': 'Paid', 'class': ' kt-badge--success'},
                                2: {'title': 'Manually Paid', 'class': ' kt-badge--dark'},
                            };
                            return '<p class="m-0 text-center"><span class="kt-badge ' + status[payment_status].class + ' kt-badge--inline kt-badge--pill">' + status[payment_status].title + '</span></p>';
                        },
                    },
                    {
                        targets: -1,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            if(typeof data == "undefined" || !data)return '';
                            if(data.status==0) return '';

                            var is_manually_paid = false;

                            /* Check for manually paid */
                            if(typeof full.manually_paid !== "undefined" && full.manually_paid === true){
                                is_manually_paid=true;
                            }

                            /* Dont show paid button on paid invoices */
                            var payment_status = 0;

                            if(typeof full.invoice !== "undefined" && full.invoice){

                                if(full.invoice.payments && full.invoice.payments.length>0){
                                    var payments = full.invoice.payments.filter(function(x){return x.manually_paid===false});

                                    if(payments.length>0)payment_status=1;
                                }
                            }

                            return `
                            @if ($helper_service->routes->has_access('tenant.admin.jobs.edit'))
                            <a href="#" ${data.status==2?'data-viewbtnhide':''} class="btn btn-outline-dark btn-elevate btn-square btn-sm py-0 px-2" title="Edit Job" onclick="JOBS.edit_job(this);return false;">
                                <i class="flaticon-eye"></i>
                                View
                            </a>
                            @endif
                            @if ($helper_service->helper->isSuperUser())

                            <a href="#" class="btn btn-outline-warning btn-elevate btn-square btn-sm py-0 px-2" title="Print Job" onclick="JOBS.events.print_invoice(this);return false;">
                                <i class="flaticon2-print"></i>
                                Print
                            </a>

                            @endif
                            @if ($helper_service->routes->has_access('tenant.admin.jobs.manually_paid'))
                            ${full.status==="complete" && payment_status==0?
                            `
                                <div class="kt-checkbox-inline m-0">
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                                        <input type="checkbox" ${is_manually_paid?'checked':''} name="manually_paid" onchange="JOBS.events.manually_paid(this);"> Paid
                                        <span></span>
                                    </label>
                                </div>
                            `
                            :''}
                            @endif
                            `;
                        },
                    },
                ],
            })

            .off('processing.dt')

            .on( 'processing.dt', function ( e, settings, processing ) {
                if(processing){
                    KTApp.blockPage({
                        type: 'v2',
                        state: 'primary',
                        message:"Processing..."
                    });
                }
                else KTApp.unblockPage();
            } );

        };

        /* page settings */
        return {
            table:$('#datatable'),
            datatable:null,
            init:function(){
                setTimeout(function(){
                    init_table();
                }, 10);
            },
            createjob_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){
                    /* request is not completed yet, we have form data available */

                    /* need to check if edit */
                    var is_edit=false;
                    if(typeof response.job_id !== "undefined")is_edit=true;

                    /* we need to create a row dynamically and add to datatables */
                    var tempid = JOBS.table.find('tbody tr').length;
                    if(is_edit)tempid=response.job_id;
                    var client=null;
                    if(typeof JOB_MODULE !== "undefined"){
                        var bikeFound = JOB_MODULE.bikes.find(function(bike){return bike.id==response.plate});
                        if(typeof bikeFound !== "undefined")client=bikeFound.client;
                        else client={name:'',id:0};
                    };
                    var rowObj=response;
                    rowObj._id=tempid;
                    rowObj.bike={plate:response.plate};
                    rowObj.client={name:client.name, id:client.id};
                    rowObj.actions={status:0};
                    if(!is_edit)rowObj.status="open";
                    rowObj.created_at=moment.utc().format();
                    rowObj.updated_at=moment.utc().format();

                    if(is_edit){
                        var row=JOBS.datatable.row('#'+tempid);
                        /* before updating the row, we should save the current data, so we can roll back */
                        var rowElem = row.node();
                        rowElem.setAttribute('data-row', JSON.stringify(row.data()));

                        row.data(rowObj).invalidate();
                        var rowNode = row.node();

                        /* Check if child is shown, hide it, (Will help when row is updated) */
                        if (row.child.isShown()) {
                            row.child.hide();
                            $( rowNode ).removeClass('parent');
                        }
                    }
                    else{
                        var rowNode = JOBS.datatable
                        .row.add( rowObj )
                        .draw()
                        .node();

                    }

                    if(typeof JOB_MODULE !== "undefined"){
                        /* clear constant data (helps in detect change) */
                        JOB_MODULE.Utils.detect_change.data=kingriders.Utils.formdata_to_object(new FormData($(JOB_MODULE.Utils.detect_change.form)[0]));
                    }


                    /* add the linker to row and change the color */
                    $( rowNode )
                    .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                    .attr('data-temp', linker);
                }
                else if(state=="error"){
                    /* it seems server respond with some errors, we need to delete the newly added row */

                    /* corresponding row */
                    var rowNode = JOBS.table.find('tbody tr[data-temp="'+linker+'"]');

                    /* check if row data found, we need to rollback the data. otherwise, jsut remove the row */

                    if(rowNode[0].hasAttribute('data-row')){
                        var orgData = JSON.parse(rowNode.attr('data-row'));
                        JOBS.datatable.row(rowNode[0]).data(orgData).invalidate();
                    }
                    else{
                        /* remove from datatables */
                        JOBS.datatable.row(rowNode[0]).remove();

                        /* remove from DOM */
                        rowNode.remove();
                    }

                    /* remove the cache data */
                    rowNode.removeAttr('data-row');

                    rowNode
                    .removeAttr('data-temp')
                    .removeAttr('style');

                    /* check if job modal is open, we need to load the job there */
                    var MODAL = $('#kt-portlet__create-job').parents('.modal');
                    if(MODAL.length){
                        if(MODAL.is(':visible')){
                            /* check if modal is for edit */
                            /* unblock the modal */
                            KTApp.unblock('#kt-portlet__create-job');

                            MODAL.modal('hide');
                        }
                    }
                }
                else{
                    /* request might be completed and we have response from server */

                    /* corresponding row */
                    var rowNode = JOBS.table.find('tbody tr[data-temp="'+linker+'"]');

                    if(response.status==2){
                        /* remove row from datatables added by 'beforeSend' state */
                        if(rowNode[0].hasAttribute('data-row')){
                            var orgData = JSON.parse(rowNode.attr('data-row'));
                            JOBS.datatable.row(rowNode[0]).data(orgData).invalidate();
                        }
                        else{
                            /* remove from datatables */
                            JOBS.datatable.row(rowNode[0]).remove();

                            /* remove from DOM */
                            rowNode.remove();
                        }
                    }
                    else{
                        /* update row */
                        JOBS.datatable.row(rowNode[0]).data(response.job).invalidate();
                    }


                    /* Remove Receive Payment kr ajax modal, so new data can be fetched */
                    var btnReceive = $('#kt-portlet__create-job .btnReceivePayment');
                    if(btnReceive.length){
                        btnReceive.each(function(i,elem){
                            if(this.hasAttribute('kr-ajax-ref')){
                                var index = this.getAttribute('kr-ajax-ref');
                                kingriders.Plugins.KR_AJAX.resetModal(index);
                            }
                        });
                    }



                    /* remove the cache data */
                    rowNode.removeAttr('data-row');

                    /* remove the effect after some time */
                    setTimeout(function(){
                        rowNode
                        .removeAttr('data-temp')
                        .removeAttr('style');
                    },2000);

                    if(typeof JOB_MODULE !== "undefined"){
                        /* clear constant data (helps in detect change) */
                        JOB_MODULE.Utils.detect_change.data=kingriders.Utils.formdata_to_object(new FormData($(JOB_MODULE.Utils.detect_change.form)[0]));

                        /* update the sugs (so any inventory would be updated) */
                        JOB_MODULE.typeahead._sugs = response.sugs;
                    }

                    var MODAL = $('#kt-portlet__create-job').parents('.modal');

                    /* check if any open job found */
                    if(response.status==2){
                        /* means returned job is open job find by system, we shoould show some warning */

                        /* show toast of success (on adding) */
                        toastr.warning("An open job already exist against this bike", "Job switched..");
                    }
                    else if(response.status==1){
                        /* show toast of success (on adding) */
                        if(MODAL.length && MODAL.is(':visible')){
                            /* Show toast only when its not editing */
                            if($('#kt-portlet__create-job [name=job_id]').length==0){
                                if(typeof JOB_MODULE !== "undefined")JOB_MODULE.alerts.make("<strong>Success!</strong> Job is created successfully.");
                                else toastr.success("Job is created successfully", "Success!");
                            }
                        }
                    }

                    /* check if job modal is open, we need to load the job there */
                    if(MODAL.length && MODAL.is(':visible')){

                        /* load the saved job */
                        if(typeof JOB_MODULE !== "undefined")JOB_MODULE.Utils.load_job(response.job);

                        /* unblock the modal */
                        KTApp.unblock('#kt-portlet__create-job');
                    }

                }

                /* Re-apply the filter */
                $('.filter-selectorpicker').trigger('change');

                kingriders.Utils.isDebug() && console.log('response', e);

            },
            edit_job:function(self){
                var rowNode = $(self).parents('tr');
                if (rowNode.hasClass('child')) {//Check if the current row is a child row
                    rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                }
                var rowData = JOBS.datatable.row(rowNode).data();

                /* Update url */
                var MODAL = $('#kt-portlet__create-job').parents('.modal');
                if(MODAL.length){
                    kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                        modal:MODAL,
                        url:"{{url('admin/jobs')}}/"+rowData._id+"/edit",
                        title:'Edit Invoice | Administrator'
                    });
                }


                if(typeof JOB_MODULE !== "undefined")JOB_MODULE.Utils.load_job(rowData);
            },
            jobmodal_loaded:function(){

                /* unhide 'View' btns after some time to wait for plugins to load */
                setTimeout(function(){
                    var rowNodes = JOBS.datatable.rows().nodes().toArray();
                    $(rowNodes).find('[data-viewbtnhide]').removeAttr('data-viewbtnhide');
                },300);

                $('#kt-portlet__create-job form').attr('action', $('#kt-portlet__create-job form').attr('data-add'));
                if(typeof JOB_MODULE !== "undefined")JOB_MODULE.Utils.reset_page();
            },
            events:{
                allow_closing:false,
                modal_closing:function(e){

                    /* subroutine to allow closing once user approve for it */
                    if(JOBS.events.allow_closing){
                        JOBS.events.allow_closing=false;
                        return;
                    }

                    if(typeof JOB_MODULE !== "undefined"){
                        if(JOB_MODULE.Utils.detect_change.check()){
                            e.preventDefault(); // prevent modal from closing and ask user for confirmation
                            var modal = this;

                            /* confirm user for before closing */
                            swal.fire({
                                title: 'Are you sure?',
                                position: 'center',
                                type: 'warning',
                                text: "Do you want to leave without saving?",
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',
                                showCancelButton: true,
                                reverseButtons: true
                            }).then((result) => {

                                if (result.value) {
                                    JOBS.events.allow_closing=true;
                                    $(modal).modal('hide');
                                }
                            });
                        }
                    }

                },
                modal_closed:function(e){
                    /* Reset url */
                    var MODAL = $(this);
                    if(MODAL.length){
                        kingriders.Plugins.KR_AJAX.ModalOnAir.update({
                            modal:MODAL,
                            url:"{{url('admin/jobs/add')}}",
                            title:'Create Invoice | Administrator'
                        });
                    }
                },
                filter_rows:function(selfElem){
                    var val = $.fn.dataTable.util.escapeRegex(
                        selfElem.value
                    );

                    JOBS.datatable
                    .columns(4) /* Status column */
                    .search( val ? '^'+val+'$' : '', true, false ).draw();
                },
                /* FOR ADMIN */
                print_invoice:function(self){
                    if(typeof JOB_MODULE !== "undefined"){

                        var rowNode = $(self).parents('tr');
                        if (rowNode.hasClass('child')) {//Check if the current row is a child row
                            rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                        }
                        var job = JOBS.datatable.row(rowNode).data();
                        JOB_MODULE.invoice.print(job);

                    }

                },
                manually_paid:function(self){
                    var rowNode = $(self).parents('tr');
                    if (rowNode.hasClass('child')) {//Check if the current row is a child row
                        rowNode = rowNode.prev();//If it is, then point to the row before it (its 'parent')
                    }
                    var rowData = JOBS.datatable.row(rowNode).data();
                    var is_checked = self.checked;

                    console.log('mp', rowData, is_checked);

                    /* Send POST and mark this job as paid manually */

                    var url = "{{ route('tenant.admin.jobs.manually_paid') }}";
                    $.ajax({
                        url : url,
                        headers:{'X-NOFETCH':''}, /* don't allow fetch accounts */
                        type : 'POST',
                        data: {
                            job_id: rowData._id,
                            checked: is_checked?1:0,
                        },
                        beforeSend: function() {
                            $(self).prop('disabled', true);
                        },
                        complete: function(){
                            $(self).prop('disabled', false);
                        }
                    })
                    .done(function(response){
                        /* Update job manually_paid key */
                        if(response.status===true){
                            rowData.manually_paid = is_checked;
                            rowData.actions.status=1;
                            response.invoice && (rowData.invoice = response.invoice);
                            JOBS.datatable.row(rowNode[0]).data(rowData).invalidate();
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {

                        /* this will handle & show errors */
                        kingriders.Plugins.KR_AJAX.showErrors(jqXHR);
                    });
                }
            },

            Filter:{
                /* Updates url */
                DOM_to_url:function(){
                    /* Filter Data */
                    var filter_date = FilterDateEl.selectedIndex;
                    var filter_client = FilterClientEl.selectedIndex;

                    /* make data to append on url */
                    var data = {
                        d:filter_date,
                        c:filter_client,
                    }

                    /* update URL */
                    kingriders.Utils.buildQueryString(data);

                    /* Refresh OnAir base_url */
                    kingriders.Plugins.KR_AJAX.ModalOnAir._refreshBaseUrl();
                },

                /* Updates DOM */
                url_to_DOM:function(){

                    var q = this.Utils.fetchQuery();

                    if(q.dateIndex)FilterDateEl.selectedIndex = q.dateIndex;
                    if(q.clientIndex)FilterClientEl.selectedIndex = q.clientIndex;

                    /* Trigger change */
                    $(FilterDateEl).trigger('change.select2');
                    $(FilterClientEl).trigger('change.select2');

                },

                Utils:{
                    /* Fetch q from url */
                    fetchQuery:function(){
                        /* we will fetch month and employee id from url */
                        var dateIndex = parseInt(kingriders.Utils.fetchQueryString("d"))||null;
                        var clientIndex = parseInt(kingriders.Utils.fetchQueryString("c"))||null;

                        return {dateIndex, clientIndex};
                    },


                }
            }
        };
    }();

    $(function(){

        /* Will update selectors if needed */
        JOBS.Filter.url_to_DOM();

        JOBS.init();
    });
</script>


@endsection
