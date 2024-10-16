@extends('Tenant.layouts.app')

@section('page_title')
    Create Invoice
@endsection
@section('head')

    <style kr-ajax-head>
        .job-content__item-container{
            min-height: 200px;
        }
        #kt-portlet__create-job table.datatable thead{
            background: #f9f9f9;
        }
        #kt-portlet__create-job table.datatable thead th:nth-of-type(2) {
            width:50%;
        }
        #kt-portlet__create-job table.datatable thead th:nth-of-type(3) {
            width:15%;
        }
        #kt-portlet__create-job table.datatable thead th:nth-of-type(4) {
            width:15%;
        }
        #kt-portlet__create-job table.datatable thead th:nth-of-type(5) {
            width:15%;
        }
        #kt-portlet__create-job table.datatable thead th:nth-of-type(6) {
            width:5%;
        }
        #kt-portlet__create-job table.datatable tbody tr td {
            vertical-align:middle;
            text-align:center;
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
        .typeahead .tt-suggestion.tt-selectable.tt-cursor {
            background-color:#5d78ff;
            color:#f2f2f2 !important;
        }
        .typeahead .tt-suggestion.tt-selectable.tt-cursor .tt-highlight{
            color:#fff !important;

        }

        #kt-portlet__create-job table.datatable thead th{
            padding: 5px 8px;
            font-weight: bold;
        }
        #kt-portlet__create-job table.datatable tbody td{
            padding: 5px 5px;
        }
        #kt-portlet__create-job .job-content input,
        #kt-portlet__create-job .job-content table.datatable tbody textarea{
            padding: 4px 7px;
            height: auto;
        }
        #kt-portlet__create-job table.datatable tbody .btndelete{
            height: 1.5rem;
            width: 1.5rem;
        }
        #kt-portlet__create-job .kt-portlet__head{
            min-height: 45px;
        }
        .low-opacity{
            opacity: .4;
        }
        .low-opacity input,
        .low-opacity input::placeholder {
            color: transparent;
        }

        #kt-portlet__create-job .jobstatus-badge{
            transition: none;
        }

        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--progress {
            color: #ffb822;
        }
        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--progress .kt-badge{
            background-color: #ffb822;
        }
        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--progress::after{
            content: "In Progress";
            font-size: 16px;
        }

        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--success {
            color: #1dc9b7;
        }
        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--success .kt-badge{
            background-color: #1dc9b7;
        }
        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--success::after{
            content: "Completed";
            font-size: 16px;
        }

        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--danger {
            color: #fd397a;
        }
        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--danger .kt-badge{
            background-color: #fd397a;
        }
        #kt-portlet__create-job .jobstatus-badge.jobstatus-badge--danger::after{
            content: "On Hold";
            font-size: 16px;
        }
        #kt-portlet__create-job .btnform:hover,
        #kt-portlet__create-job .btnform:focus,
        #kt-portlet__create-job .btn-printsampleinvoice:hover,
        #kt-portlet__create-job .btn-printsampleinvoice:focus{
            background-color: initial !important;
        }

        .kr-page__add .job-content__bike-times{
            display: none;
        }
        .kr-page__add .btnform{
            display: none;
        }


        #kt-portlet__create-job .typeahead .tt-menu .typeahead-table__head{
            color: #000000;
            background: #eee;
            border-color: #000;
            border-spacing: 0;
            margin: 0;
            border-bottom: 2px solid #999;
        }
        #kt-portlet__create-job .typeahead .tt-menu .typeahead-table__head tr th{
            padding:0 3px;
            border: 1px solid #ccc;
            font-weight: 500;
        }
        #kt-portlet__create-job .typeahead .tt-menu .typeahead-table__body tr td{
            border: 1px solid #ccc;
            border-bottom: none;
            padding:0 3px;
        }
        #kt-portlet__create-job .typeahead .tt-menu .typeahead-table__body:last-child{
            border-bottom: 1px solid #ccc;
        }
        #kt-portlet__create-job .typeahead .tt-menu table tr > :nth-child(1) {
            width: 110px;
        }
        #kt-portlet__create-job .typeahead .tt-menu table tr > :nth-child(2) {
            width: 395px;
        }
        #kt-portlet__create-job .typeahead .tt-menu table tr > :nth-child(3) {
            min-width: 76px;
        }

        #kt-portlet__create-job .job-content__item-discount-selection {
            width:60%;
        }
        #kt-portlet__create-job .job-content__item-tax-selection{
            width:40%;
        }
        #kt-portlet__create-job .job-content__item-discount-selection.disable_it [type="number"],
        #kt-portlet__create-job .job-content__item-discount-selection.disable_it select {
            opacity: .6;
            pointer-events: none;
            background: #eee;
        }

        #kt-portlet__create-job .job-content__item-tax-selection.disable_it [type="number"] {
            opacity: .6;
            pointer-events: none;
            background: #eee;
        }
        #kt-portlet__create-job .payments--wrapper {
            width: 130px;
            font-size: 10px;
        }
        #kt-portlet__create-job .payments--wrapper input,
        #kt-portlet__create-job .payments--wrapper .btn{
            font-size: 10px;
        }
        #kt-portlet__create-job .payments--wrapper .btn:hover {
            color: #fff !important;
        }
        #kt-portlet__create-job [data-paymentpopover]{
            cursor: pointer;
            font-size: 14px;
        }

        #kt-portlet__create-job .balance_due--wrapper h3 {
            font-size: 14px;
            margin: 0;
            font-weight: 400;
        }
        #kt-portlet__create-job .balance_due--wrapper > span {
            font-size: 30px;
            color: #08976d;
            font-weight: 500;
            letter-spacing: 1px;
        }
        #kt-portlet__create-job .kr-bootstrapselect .bootstrap-selector{
            border:1px solid #e2e5ec !important;
            border-radius:0 !important;
        }

    </style>
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet m-0 pb-2" id="kt-portlet__create-job" kr-ajax-content>
    <form class="kt-form" data-add="{{route('tenant.admin.jobs.add')}}" data-edit="{{route('tenant.admin.jobs.edit')}}" action="{{route('tenant.admin.jobs.add')}}" method="POST">
        @csrf
        <div class="kt-portlet__head d-block d-sm-flex" kr-ajax-inner-header>
            <div class="kt-portlet__head-label my-2 my-sm-0">
                <h3 class="kt-portlet__head-title">Create Invoice</h3>
            </div>
            <div class="kt-portlet__head-label my-2 my-sm-0 justify-content-center">
                <span class="badge d-inline-flex align-items-center jobstatus-badge jobstatus-badge--progress">
                    <span class="kt-badge kt-badge--dot kt-badge--xl mr-2"></span>
                </span>

                <div class="btn-group kr-btn__group job_status-wrapper d-none" style="display: none;" role="group" aria-label="Change Status" data-toggle="buttons">

                    <label class="btn btn-secondary active m-0 py-1">
                        <input type="radio" name="status" hidden value="in_progress" autocomplete="off" checked> In Progress
                    </label>

                    <label class="btn btn-secondary m-0 py-1">
                        <input type="radio" name="status" hidden value="on_hold" autocomplete="off"> On Hold
                    </label>

                    <label class="btn btn-secondary m-0 py-1">
                        <input type="radio" name="status" hidden value="complete" autocomplete="off"> Complete
                    </label>
                </div>
            </div>
            <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
        </div>
    <!--begin::Form-->

        <div class="kt-portlet__body py-2 px-4" kr-ajax-inner-content>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-success message-alert m-0 mb-3 rounded-0" role="alert" style="display: none;">
                        <div class="alert-text h6 m-0"></div>
                    </div>

                </div>
            </div>
            <button type="button" hidden data-create-bike kr-ajax-size="50%" kr-ajax-modalclosed="JOB_MODULE.bike_module.modal_closed" kr-ajax-submit="JOB_MODULE.bike_module.form_submit" kr-ajax-contentloaded="JOB_MODULE.bike_module.form_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.bikes.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Bike
            </button>
            <button type="button" hidden data-create-subclient kr-ajax-size="30%" kr-ajax-modalclosed="JOB_MODULE.Bill_to.modal_closed" kr-ajax-submit="JOB_MODULE.Bill_to.form_submit" kr-ajax-contentloaded="JOB_MODULE.Bill_to.form_loaded" kr-ajax-preload kr-ajax="{{route('tenant.admin.clients.add')}}" class="btn btn-info btn-elevate btn-square">
                <i class="flaticon2-plus-1"></i>
                Create Client
            </button>

            <div class="row">
                <div class="col-md-8">
                    <div class="job-header row">
                        <div class="job-header__item col-md-8 mb-3 mb-md-0">
                            <div class="d-flex justify-content-between mb-2">
                                Bike:
                                <div class="kt-checkbox-inline m-0">
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                                        <input type="checkbox" name="is_self"> Self bike
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <select name="plate" data-dynamic data-placeholder="Enter plate" data-createtag="JOB_MODULE.select2Utils.createTags" data-inserttag="JOB_MODULE.select2Utils.insertTags" required class="form-control kr-select2 @error('plate') is-invalid @enderror">
                                <option></option>
                                @foreach ($bikes as $bike)
                                <option data-client-iswalking="{{$bike->client->walking_customer}}" value="{{$bike->id}}">{{$bike->plate}}-{{$bike->manufacturer}}@isset($bike->cc) {{$bike->cc}}{{__('CC')}}@endisset-{{$bike->model}} | {{$bike->client->name}}</option>
                                @endforeach
                            </select>
                            @error('plate')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>.
                                </span>
                            @enderror
                            <a href="#" onclick="JOB_MODULE.bike_module.refetch_rider(); return false;">Re-fetch driver</a>
                        </div>

                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="d-flex justify-content-between mb-2">
                                Bill To:
                            </div>
                            <select name="bill_to" data-dynamic data-placeholder="Select Bill To" required class="form-control kr-select2">
                                <option></option>
                                @foreach ($sub_clients as $client)
                                <option value="{{$client->id}}" @isset($client->props)data-krid="{{$client->props['krid']}}" @endisset>{{$client->name}}</option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <mark class="card-title text-center text-dark kt-heading kt-heading--thin m-0 mb-2 d-block">Recent Invoices</mark>

                                    <div class="table-responsive">
                                        <table class="table table-sm m-0 table-recentinvoices">
                                            <thead>
                                                <tr>
                                                    <th class="p-0 py-1 pl-1">Invoice #</th>
                                                    <th class="p-0 py-1 pl-1">Date</th>
                                                    <th class="p-0 py-1 pl-1">Amount</th>
                                                    <th class="p-0 py-1 pl-1">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="text-center text-muted">No invoices found</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="alert alert-warning fade mb-3 py-2 rounded-0 warnings_caontainer" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">
                            <h4 class="alert-heading">Mileage Exceed!</h4>
                            <div>
                                Mileage exceed by: <strong></strong>
                            </div>
                            <div>
                                Last Mileage: <strong></strong>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-3">

                    @if ($helper_service->routes->has_access('tenant.admin.clients.open_invoices'))
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-info btn-default btn-square border-info btn-sm payments--belongs btnReceivePayment" kr-ajax-modal-type="full" data-backdrop="static"  kr-ajax-modalclosed="JOB_MODULE.receive_payment.modal_closed" kr-ajax-submit="JOB_MODULE.receive_payment.form_submit" kr-ajax-contentloaded="JOB_MODULE.receive_payment.form_loaded" kr-ajax="{{route('tenant.admin.clients.open_invoices')}}">
                            Receive Payment
                        </button>
                    </div>
                    @endif

                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="job-header row">

                        <div class="job-header__item col-md-6 mb-3 mb-md-0">
                            <div class="d-flex justify-content-between mb-1">
                                Driver Name:
                                <div class="kt-checkbox-inline m-0">
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0">
                                        <input type="checkbox" name="is_charge_to_driver"> Charge to Driver
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <input type="text" required name="driver_name" placeholder="Enter driver full name" class="rounded-0 form-control @error('driver_name') is-invalid @enderror" value="{{old('driver_name')}}">
                            @error('driver_name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>.
                                </span>
                            @enderror
                        </div>
                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="mb-1">Driver Phone:</div>
                            <input type="number" required name="driver_phone" placeholder="e.g 0521234567" class="rounded-0 form-control @error('driver_phone') is-invalid @enderror" value="{{old('driver_phone')}}">
                            @error('driver_phone')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>.
                                </span>
                            @enderror
                        </div>
                        <div class="job-header__item col-md-2 mb-3 mb-md-0">
                            <div class="mb-1">Mileage:</div>
                            <input type="number" required name="mileage" class="rounded-0 form-control @error('mileage') is-invalid @enderror" value="{{old('mileage')}}">
                            @error('mileage')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>.
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="job-header row">
                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="mb-1">Terms:</div>
                            <select class="kr-bootstrapselect mr-2" onchange="JOB_MODULE.events.onchange_netterms();" name="net_terms" data-style="bootstrap-selector" data-width="100%">
                                <option selected value="0">Due on receipt</option>
                                <option value="15">Net 15</option>
                                <option value="30">Net 30</option>
                                <option value="60">Net 60</option>
                            </select>
                        </div>
                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="mb-1">Invoice Date:</div>
                            <input type="text" required readonly name="date" data-state="date" class="rounded-0 kr-datepicker form-control @error('date') is-invalid @enderror" value="{{old('date')}}">

                        </div>
                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="mb-1">Due Date:</div>
                            <input type="text" required readonly name="due_date" data-state="date" class="rounded-0 kr-datepicker form-control @error('due_date') is-invalid @enderror" value="{{old('due_date')}}">

                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
            <div class="job-content row">
                <div class="job-content__item col-md-8">
                    <div class="job-content__item-container border">
                        <table class="table border-0 table-bordered table-hover table-checkable datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Rate</th>
                                    <th>Qty</th>
                                    <th>
                                        <div class="d-flex justify-content-between">
                                            <span>Total</span>
                                            <i class="flaticon-questions-circular-button kt-label-font-color-2"
                                                data-toggle="kt-tooltip"
                                                data-skin="dark"
                                                data-placement="top"
                                                title=""
                                                data-html="true"
                                                data-original-title="Add this amount in Rider Account.">
                                            </i>
                                        </div>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>


                    </div>
                    <div class="job-content__item-footer border border-top-0 py-2 px-3">
                        <button type="button" class="btn btn-outline-info btn-elevate btn-square btn-sm btnadd_row" onclick="JOB_MODULE.append_row();JOB_MODULE.calculate_subtotal();">
                            <i class="flaticon2-plus-1"></i>
                            Add Row
                        </button>
                    </div>
                </div>
                <div class="job-content__item col-md-4">

                    <div class="card rounded-0">
                        <div class="card-body py-2 px-3">

                            <div class="job-content__item-pricing">

                                <div class="job-content__item-discount-wrapper d-flex justify-content-between align-items-center">
                                    <div class="job-content__item-selection job-content__item-discount-selection disable_it">
                                        <span class="m-0 mr-2 text-muted d-flex d-lg-none align-items-center mb-1">
                                            <label class="kt-checkbox kt-checkbox--single kt-checkbox--success m-0 mr-1 px-2">
                                                <input type="checkbox" name="is_discount" >
                                                <span></span>
                                            </label>
                                            Discount:
                                        </span>
                                        <div class="input-group">
                                            <span class="m-0 mr-2 text-muted d-none d-lg-flex align-items-center">
                                                <label class="kt-checkbox kt-checkbox--single kt-checkbox--success mb-0 px-2 mr-1">
                                                    <input type="checkbox" name="is_discount" >
                                                    <span></span>
                                                </label>
                                                Discount:
                                            </span>
                                            <input type="number" oninput="JOB_MODULE.calculate_subtotal(true);" class="form-control rounded-0" placeholder="0" name="discount_value">
                                            <div class="input-group-append">
                                                <select onchange="JOB_MODULE.calculate_subtotal();" class="form-control p-0 rounded-0 h-auto" name="discount_type">
                                                    <option value="percentage">%</option>
                                                    <option value="fixed">AED</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="job-content__item-selection job-content__item-tax-selection ml-2 disable_it">
                                        <span class="m-0 mr-2 text-muted d-flex d-lg-none align-items-center mb-1">
                                            <label class="kt-checkbox kt-checkbox--single kt-checkbox--success m-0 mr-1 px-2">
                                                <input type="checkbox" name="is_tax" >
                                                <span></span>
                                            </label>
                                            Tax:
                                        </span>
                                        <div class="d-flex">
                                            <span class="m-0 mr-2 text-muted d-none d-lg-flex align-items-center">
                                                <label class="kt-checkbox kt-checkbox--single kt-checkbox--success mb-0 px-2 mr-1">
                                                    <input type="checkbox" name="is_tax" >
                                                    <span></span>
                                                </label>
                                                Tax:
                                            </span>
                                            <input type="number" class="form-control rounded-0 px-2 text-center" oninput="JOB_MODULE.calculate_subtotal(true);" name="tax_value" value="0">
                                            <div class="input-group-append">
                                                <span class="input-group-text rounded-0 py-1">%</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="kt-separator kt-separator--border-dashed kt-separator--space-xs"></div>

                                <div class="row mb-4">
                                    <div class="col-lg-8 offset-lg-4">

                                        {{-----------------------------
                                            Subtotal
                                        -----------------------------}}
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="h6 m-0">Subtotal</span>
                                            </div>
                                            <span class="h6 m-0">
                                                <input type="hidden" name="subtotal">
                                                <span class="job-content__item-subtotal-text">0.00</span>
                                            </span>
                                        </div>

                                        {{-----------------------------
                                            Tax
                                        -----------------------------}}
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="h6 m-0">Tax</span>
                                            </div>
                                            <span class="h6 m-0">
                                                <input type="hidden" name="tax_amount">
                                                <span class="job-content__item-tax-amount-label">0.00</span>
                                            </span>
                                        </div>

                                        {{-----------------------------
                                            Discount
                                        -----------------------------}}
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="h6 m-0">Discount</span>
                                            </div>
                                            <span class="h6 m-0">
                                                <input type="hidden" name="discount_amount">
                                                -<span class="discount_amount_text">0.00</span>
                                            </span>
                                        </div>

                                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-xs"></div>

                                        {{-----------------------------
                                            Total
                                        -----------------------------}}
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="h6 m-0">Total</span>
                                            </div>
                                            <span class="h6 m-0">
                                                <input type="hidden" name="total">
                                                <span class="job-content__item-total-text">0.00</span>
                                            </span>
                                        </div>

                                        {{-----------------------------
                                            Amount Paid
                                        -----------------------------}}
                                        <div class="d-flex justify-content-between text-danger payments--belongs" style="display: none !important;">
                                            <div>
                                                <span class="h6 m-0">Amount Paid</span>
                                            </div>
                                            <span class="h6 m-0">
                                                <input type="hidden" name="amount_paid">
                                                -<span class="job-content__item-amount-paid-text">0.00</span>
                                                <span
                                                class="float-right text-primary payment_records"
                                                data-toggle="popover"
                                                data-paymentpopover
                                                data-title="Payment History"
                                                data-content="">
                                                    <i class="la la-info"></i>
                                                </span>
                                            </span>
                                        </div>

                                        <div class="kt-separator kt-separator--border-solid kt-separator--space-xs mb-2"></div>

                                        {{-----------------------------
                                            Total
                                        -----------------------------}}
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <span class="h5 m-0">Balance Due</span>
                                            </div>
                                            <span class="h5 m-0">
                                                <small>AED</small>
                                                <span class="job-content__item-balance-text">0.00</span>
                                                <input type="hidden" name="balance_due">
                                            </span>
                                        </div>

                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-solid-danger error-alert m-0 mt-3 rounded-0 border-danger alert-bold" role="alert" style="display: none;">
                                <div class="alert-text"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="d-flex justify-content-start flex-wrap">
                        <div class="form-group">
                            <label>Message on invoice:</label>
                            <textarea class="form-control" cols="40" rows="3" name="invoice_notes" placeholder="This will show up on the invoice"></textarea>
                        </div>
                        <div class="form-group ml-sm-5">
                            <label>Internal Notes:</label>
                            <textarea class="form-control" cols="40" rows="3" name="internal_notes" placeholder="Enter any notes related to this invoice"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__foot kt-portlet__foot--solid py-2 border-top" kr-ajax-inner-footer>
            <div class="d-flex justify-content-between flex-column flex-md-row align-items-end align-items-md-center">
                <div class="d-flex">
                    <div class="d-flex flex-column justify-content-center">
                        <button type="button" class="btn btn-square py-2 btn-outline-danger text-danger btnform btnform--onhold" onclick="JOB_MODULE.place_on_hold();">
                            <i class="la la-hand-stop-o"></i>
                            Place on hold
                        </button>
                        <button type="button" class="btn-link btn btn-square py-0 text-info btn-printsampleinvoice" onclick="JOB_MODULE.invoice.printSampleInvoice();return false;">
                            Print sample invoice
                        </button>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="row job-content__bike-times">
                        <div class="col-md-12">
                            <div class="job-content__bike-in m-0 d-flex justify-content-start">
                                <strong class="mr-2">Bike in:</strong>
                                <span></span>
                            </div>
                            <div class="job-content__bike-out m-0 d-flex justify-content-start">
                                <strong class="mr-2">Bike out:</strong>
                                <span></span>
                                <div class="dropdown btnform--bikeout btnform--bikeoutouter">
                                    <a href="#" class="btn btn-sm btn-clean p-0" data-toggle="dropdown" aria-expanded="false">
                                        <i class="la la-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item" onclick="JOB_MODULE.bike_out(this);return false;">
                                            <i class="la la-motorcycle"></i>
                                            Update Bike out
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-brand rounded-0 px-4" data-textadd="Bike in" data-textedit="Save">Bike in</button>
                    <button type="button" class="btn btn-outline-brand rounded-0 px-5 ml-2 btnform--bikeout btnform--bikeoutinner" style="display: none;" onclick="JOB_MODULE.complete_job();return false;">Bike out</button>
                    <div class="d-flex flex-column justify-content-center">
                        <button type="button" class="btn-link btn btn-square py-0 text-info btnform btnform--print" onclick="JOB_MODULE.invoice.handleclick(this);return false;">
                            Print invoice
                        </button>
                        <button type="button" class="btn-link btn btn-square py-0 text-danger btnform btnform--unhold" onclick="JOB_MODULE.unhold_invoice();return false;">
                            Unhold invoice
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <div class="invoice__wrapper" style="display:none"></div>

    <!--end::Form-->
</div>

<!--end::Portlet-->


@endsection

@section('foot')

    <script kr-ajax-head src=" https://printjs-4de6.kxcdn.com/print.min.js" type="text/javascript"></script>
    <script kr-ajax-head src="{{asset('js/arrow-table.min.js')}}" type="text/javascript"></script>
    {{------------------------------------------------------------------------------
                                    HANDLEBARS TEMPLAATES
    --------------------------------------------------------------------------------}}

    {{-- ADD ROW TEMPLATE --}}
    @include('Tenant.jobs.handlebars_templates.add_row')

    {{-- INVOICE TEMPLATE --}}
    @include('Tenant.jobs.handlebars_templates.print_invoice')
    @include('Tenant.jobs.handlebars_templates.print_sampleinvoice')

    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">

        var MILEAGE_THRESHOLD = 2000;

        var JOB_MODULE = function(){

            var table = $('#kt-portlet__create-job .datatable');

            var calculate_subtotal = function(){
                /* variables to store*/
                var subtotal=0,
                    total=0,
                    tax=0,
                    discount=0;

                /*
                |=================================
                |       Calculating Amounts
                |=================================
                */

                /* Loop through each item and update accordingly */
                table.find('tbody tr').each(function(index,elem){
                    var rate = parseFloat($(this).find('[data-name="rate"]').val())||0;
                    var qty = parseFloat($(this).find('[data-name="qty"]').val())||0;

                    var amount = rate * qty;

                    /* Updating inner DOM for each row */
                    $(this).find('.job-items__total').text(amount.toFixed(2));

                    /* adding amount to subtotal, for calculating final amount */
                    subtotal+=amount;
                });

                total=subtotal;

                /* finding DISCOUNT (if any) */
                var discount_type = $('#kt-portlet__create-job [name=discount_type]').val();
                var discount_value = parseFloat($('#kt-portlet__create-job [name=discount_value]').val()) || 0;
                var discount_amount = 0;
                if(discount_value>0){
                    if (discount_type == 'percentage') discount_amount = (discount_value * subtotal) / 100;
                    else discount_amount = discount_value;
                }

                /* subtracting discount amount from total*/
                total-=discount_amount;

                /* finding TAX (if enabled) */
                var tax_value = parseFloat($('#kt-portlet__create-job [name=tax_value]').val()) || 0;
                var tax_amount = 0;
                if(tax_value>0){
                    tax_amount = (tax_value * subtotal) / 100;
                }

                /* adding tax amount to total*/
                total+=tax_amount;

                /* Finding paid amount */
                var total_paid = 0;
                var paymentsObj = JOB_MODULE.invoice.getPayments();
                if(paymentsObj){
                    /* check if payments found */
                    if(paymentsObj.payments && paymentsObj.payments.length){
                        total_paid=paymentsObj.total;
                    }
                }
                /* Deducting paid amount from total */
                var balance_due = (total - total_paid).toRound(3);


                /*
                |=================================
                |           Updating DOM
                |=================================
                */
                /* SUBTOTAL */
                $('#kt-portlet__create-job .job-content__item-subtotal-text').text(subtotal.toFixed(2));
                $('#kt-portlet__create-job [name=subtotal]').val(subtotal.toRound(2));
                /* DISCOUNT */
                $('#kt-portlet__create-job .discount_amount_text').text(discount_amount.toFixed(2));
                $('#kt-portlet__create-job [name=discount_amount]').val(discount_amount.toRound(2));

                /* TAX */
                $('#kt-portlet__create-job .job-content__item-tax-amount-label').text(tax_amount.toFixed(2));
                $('#kt-portlet__create-job [name=tax_amount]').val(tax_amount.toRound(2));

                /* TOTAL */
                $('#kt-portlet__create-job .job-content__item-total-text').text(total.toFixed(2));
                $('#kt-portlet__create-job [name=total]').val(total.toRound(2)).trigger('change');

                /* AMOUNT PAID */
                $('#kt-portlet__create-job .job-content__item-amount-paid-text').text(total_paid.toFixed(2));
                $('#kt-portlet__create-job [name=amount_paid]').val(total_paid.toRound(2));


                /* BALANCE DUE */
                $('#kt-portlet__create-job .job-content__item-balance-text').text(balance_due.toFixed(2));
                $('#kt-portlet__create-job [name=balance_due]').val(balance_due.toRound(2));


                if(balance_due==0){
                    /* Invoice is paid */
                    $('#kt-portlet__create-job .btnReceivePayment').hide();

                }

                /* CHeck if invoice is manually paid, need to disable the page */
                if(JOB_MODULE.loaded_job && JOB_MODULE.loaded_job.manually_paid === true){
                    DISABLEPAGE();
                }
                else{
                    ENABLEPAGE();
                }

            }

            var update_indices=function(){
                table.find('tbody tr').each(function(rowIndex, elem){

                    /* update names */
                    $(this).find('[data-name]').attr('name', function(index, attr){
                        var name = $(this).attr('data-name');
                        $(this).attr('name', 'job_items['+rowIndex+']['+name+']');
                    });

                    /* update SR # */
                    $(this).find('.srno').text(rowIndex+1);
                });
            }

            var DISABLEPAGE = function(){
                /* Disable the page */
                $('#kt-portlet__create-job input, #kt-portlet__create-job textarea, #kt-portlet__create-job button:not(.close), #kt-portlet__create-job a.btn').prop('disabled', true);
            }
            var ENABLEPAGE = function(){
                /* Enable the page */
                $('#kt-portlet__create-job input, #kt-portlet__create-job textarea, #kt-portlet__create-job button:not(.close), #kt-portlet__create-job a.btn').prop('disabled', false);
            }

            return {
                bikes:function(){
                    var bikes = {!! $bikes !!};

                    /* add latest invoice according to client */
                    // bikes.forEach(function(bike){
                    //     var client_jobs = bike.client.jobs;

                    //     /* Sort desc */

                    //     bike.client.latest_invoice = client_jobs.sort(function(a,b){
                    //         var dateA = moment(a.created_at);
                    //         var dateB = moment(b.created_at);

                    //         if(dateA.isAfter(dateB))return -1;
                    //         else if(dateA.isSame(dateB)) return 0;
                    //         return 1;
                    //     })[0];
                    // });

                    return bikes;
                }(),
                loaded_job:null,
                calculate_subtotal:function(is_minimal=false){
                    /* clear the errors */
                    JOB_MODULE.errors.clear();

                    if(!is_minimal) update_indices();

                    /* calculate the amount through each loop*/
                    calculate_subtotal();

                },
                Utils:{

                    reset_page:function(force=false){
                        /* clear the page if only job id found */
                        if($('#kt-portlet__create-job [name=job_id]').length || force){
                            $('#kt-portlet__create-job form [name=job_id]').remove();

                            /* clear the items */
                            $('#kt-portlet__create-job .datatable tbody tr').remove();
                            $('#kt-portlet__create-job [name="plate"]').val(null).trigger('change.select2');
                            $('#kt-portlet__create-job [name="net_terms"]').val(0).selectpicker('refresh');
                            $('#kt-portlet__create-job [name="date"]').datepicker('update', new Date(Date.now()).format('mmmm dd, yyyy'));
                            $('#kt-portlet__create-job [name="driver_name"]').val(null).removeAttr('data-krid');
                            $('#kt-portlet__create-job [name="is_charge_to_driver"]').prop('checked', false);
                            $('#kt-portlet__create-job [name="bill_to"]').val(null).trigger('change.select2');
                            $('#kt-portlet__create-job [name="driver_phone"]').val(null);
                            $('#kt-portlet__create-job [name="mileage"]').val(null);
                            $('#kt-portlet__create-job [name="invoice_notes"]').val(null);
                            $('#kt-portlet__create-job [name="internal_notes"]').val(null);

                            $('#kt-portlet__create-job [name="is_self"]').prop('checked', false).trigger('change');
                            $('#kt-portlet__create-job [name="discount_value"]').val(null);
                            $('#kt-portlet__create-job [name="discount_type"]').val('percentage');
                            $('#kt-portlet__create-job [name="tax_value"]').val(0);
                            var inputStatus=$('#kt-portlet__create-job .job_status-wrapper [name=status][value="in_progress"]');
                            inputStatus.prop('checked', true).trigger('change');

                            $('#kt-portlet__create-job [name="is_discount"]').prop('checked', false);
                            $('#kt-portlet__create-job .job-content__item-discount-selection').addClass('disable_it');

                            $('#kt-portlet__create-job [name="is_tax"]').prop('checked', false);
                            $('#kt-portlet__create-job .job-content__item-tax-selection').addClass('disable_it');

                            /* Empty loaded job */
                            JOB_MODULE.loaded_job=null;

                            /* append blacnk row */
                            JOB_MODULE.append_row();

                            /* recalculate data (values zero) */
                            JOB_MODULE.calculate_subtotal();

                        }

                        /* Empty mileage warnings */
                        $('.alert.warnings_caontainer').removeClass('show');

                        /* Remove do_bikeout */
                        $('#kt-portlet__create-job form').find('[name=do_bikeout]').remove();

                        /* reset invoice number from label */
                        $('#kt-portlet__create-job .kt-portlet__head-title').text("Create Invoice");

                        $('#kt-portlet__create-job')
                        .removeClass('kr-page__edit').removeClass('kr-page__add')
                        .addClass('kr-page__add');

                        /* Hide payments */
                        $('#kt-portlet__create-job .payments--belongs').css('cssText', 'display: none !important;');

                        /* remove payments */
                        $('#kt-portlet__create-job .payment_records').attr('data-content', '');

                        /* Reset texts */
                        $('#kt-portlet__create-job [data-textadd]').text(function(){
                            var text = $(this).attr('data-textadd');
                            $(this).text(text);
                        });

                        $('#kt-portlet__create-job .btn-printsampleinvoice').show();

                        /* clear constant data (helps in detect change) */
                        setTimeout(function(){
                            /* after half sec, so if modal is loading, it will complete */
                            JOB_MODULE.Utils.detect_change.data=kingriders.Utils.formdata_to_object(new FormData($(JOB_MODULE.Utils.detect_change.form)[0]));
                        }, 300);
                    },
                    load_job:function(job){

                        /* Load the job in page (this funtion is using in view job page) */


                        /* Update url */
                        var page_type = @if(isset($config) && $config->action=='view') 'view' @else 'edit' @endif;

                        kingriders.Utils.replaceUrl('Edit Invoice | Administrator', "{{url('admin/jobs')}}/"+job._id+"/"+page_type);

                        /* remove all listening */
                        JOB_MODULE.Utils.detect_change.remove_listening();

                        /* Remove do_bikeout */
                        $('#kt-portlet__create-job form').find('[name=do_bikeout]').remove();

                        /* reset invoice number from label */
                        $('#kt-portlet__create-job .kt-portlet__head-title').text("Create Invoice");

                        /* need to check if job is suitable for edit, (not in creating process) */
                        if(job.actions.status!=0){

                            /* update invoice number from label */
                            if( typeof job.invoice !== "undefined" && job.invoice )
                                $('#kt-portlet__create-job .kt-portlet__head-title').text("Invoice #"+job.invoice.id);

                            /* loaded job data */
                            job.actions.status=1;
                            JOB_MODULE.loaded_job=job;

                            /* check if page if loaded in modal */
                            var MODAL = $('#kt-portlet__create-job').parents('.modal');
                            if(MODAL.length){
                                MODAL.modal({
                                    show: true,
                                    backdrop: 'static'
                                });
                            }

                            /* Reset texts */
                            $('#kt-portlet__create-job [data-textedit]').text(function(){
                                var text = $(this).attr('data-textedit');
                                $(this).text(text);
                            });

                            /* change the action of form to edit */
                            $('#kt-portlet__create-job form [name=job_id]').remove();
                            $('#kt-portlet__create-job form').attr('action', $('#kt-portlet__create-job form').attr('data-edit'))
                            .prepend('<input type="hidden" name="job_id" value="'+job._id+'" />');

                            /* change the status */
                            $('#kt-portlet__create-job .job_status-wrapper').show().find('[name=status]').prop('checked', false);
                            $('#kt-portlet__create-job .job_status-wrapper .btn').removeClass('active');
                            var inputStatus=$('#kt-portlet__create-job .job_status-wrapper [name=status][value="'+job.status+'"]');
                            inputStatus.prop('checked', true).parent().addClass('active');
                            inputStatus.trigger('change');

                            /* bike in/out times */
                            var bike_in = typeof job.bike_in !== "undefined" && job.bike_in?moment(job.bike_in).format('DD/MMM/YYYY HH:mm:ss | dddd'):"";
                            var bike_out = typeof job.bike_out !== "undefined" && job.bike_out?moment(job.bike_out).format('DD/MMM/YYYY HH:mm:ss | dddd'):"";
                            $('#kt-portlet__create-job')
                            .removeClass('kr-page__edit').removeClass('kr-page__add')
                            .addClass('kr-page__edit');

                            $('#kt-portlet__create-job .job-content__bike-times .job-content__bike-in span').text(bike_in);
                            $('#kt-portlet__create-job .job-content__bike-times .job-content__bike-out span').text(bike_out);

                            /* load the job */
                            var job_items = job.services;

                            /* clear the items first */
                            $('#kt-portlet__create-job .datatable tbody tr').remove();

                            /* append job items */
                            job_items.forEach(function(job_item,index) {
                                var has_part = false,
                                    inventory=null;
                                if(typeof job_item.inventory_item !== "undefined" && job_item.inventory_item && typeof job_item.inventory_item.part !== "undefined" && job_item.inventory_item.part){
                                    has_part = true;
                                    var total_used_qty = job_items.reduce(function(total, obj){
                                        if(obj.inventory_item&&obj.inventory_item.part_id==job_item.inventory_item.part_id)return total+obj.inventory_item.qty;
                                        return total;
                                    },0);

                                    /* find inventory from sugs, because sugs are updated when anything changes */
                                    var item_inventory = JOB_MODULE.typeahead._sugs.find(function(x){return x.part_id==job_item.inventory_item.part_id});
                                    if(typeof item_inventory !== "undefined")item_inventory=item_inventory.inventory;
                                    else item_inventory=job_item.inventory_item.part.inventory;

                                    /* create inventory object to append data in row */
                                    inventory={
                                        part_inventry_id:job_item.inventory_item._id,
                                        remaining:item_inventory.remaining+total_used_qty, /* already added qty will not affect inventory */
                                        part_id:job_item.inventory_item.part_id
                                    };

                                }
                                var obj={
                                    id:job_item._id,
                                    description:job_item.description,
                                    rate:job_item.rate,
                                    qty:job_item.qty,
                                    total:job_item.total,
                                    has_part:has_part,
                                    ds_ref:job_item.ds_ref,
                                    inventory:inventory
                                };
                                JOB_MODULE.append_row(obj);
                            });

                            /* push it to plate change queue so after new data renderd, it will change the plate */
                            JOB_MODULE.select2Utils.platechange_queue.push(job.bike.plate);
                            $('#kt-portlet__create-job [name="plate"]').val(job.bike.id).trigger('change.select2');

                            /* Update mileage */
                            JOB_MODULE.bike_module.getMileage();

                            /* Update recent invoices */
                            JOB_MODULE.bike_module.getRecentInvoices();

                            /* load other data like bike */
                            var is_self = false;
                            if(typeof job.is_self !== "undefined" && job.is_self)is_self = true;

                            $('#kt-portlet__create-job [name="net_terms"]').val(job.net_terms).selectpicker('refresh');

                            var job_date = new Date(job.date).format('mmmm dd, yyyy');
                            var job_due_date = new Date(job.due_date).format('mmmm dd, yyyy');
                            $('#kt-portlet__create-job [name="date"]').attr('data-default', job_date).datepicker('update', job_date);
                            $('#kt-portlet__create-job [name="due_date"]').attr('data-default', job_due_date).datepicker('update', job_due_date);
                            $('#kt-portlet__create-job [name="driver_name"]').val(job.driver_name||null).removeAttr('data-krid');
                            $('#kt-portlet__create-job [name="is_charge_to_driver"]').prop('checked', job.charged_to_driver==1?true:false);
                            $('#kt-portlet__create-job [name="bill_to"]').val(job.subclient_id||null).trigger('change.select2');
                            $('#kt-portlet__create-job [name="driver_phone"]').val(job.driver_phone||null);
                            $('#kt-portlet__create-job [name="mileage"]').val(job.mileage);
                            $('#kt-portlet__create-job [name="is_self"]').prop('checked', is_self).trigger('change');

                            $('#kt-portlet__create-job [name="discount_value"]').val(job.discount.discount_value);
                            $('#kt-portlet__create-job [name="discount_type"]').val(job.discount.discount_type);
                            $('#kt-portlet__create-job [name="tax_value"]').val(job.tax.tax_value);

                            $('#kt-portlet__create-job [name="invoice_notes"]').val(job.invoice_notes);
                            $('#kt-portlet__create-job [name="internal_notes"]').val(job.internal_notes);

                            if(job.tax.tax_value>0){
                                /* checkbox */
                                $('#kt-portlet__create-job [name="is_tax"]').prop('checked', true);
                                $('#kt-portlet__create-job .job-content__item-tax-selection').removeClass('disable_it');
                            }
                            else{
                                $('#kt-portlet__create-job [name="is_tax"]').prop('checked', false);
                                $('#kt-portlet__create-job .job-content__item-tax-selection').addClass('disable_it');
                            }

                            if(job.discount.discount_value>0){
                                /* checkbox */
                                $('#kt-portlet__create-job [name="is_discount"]').prop('checked', true);
                                $('#kt-portlet__create-job .job-content__item-discount-selection').removeClass('disable_it');
                            }
                            else{
                                $('#kt-portlet__create-job [name="is_discount"]').prop('checked', false);
                                $('#kt-portlet__create-job .job-content__item-discount-selection').addClass('disable_it');
                            }


                            /* Hide payments */
                            $('#kt-portlet__create-job .payments--belongs').css('cssText', 'display: none !important;');
                            /* remove payments */
                            $('#kt-portlet__create-job .payment_records').attr('data-content', '');

                            if(job.invoice){
                                /* Seems invoice for this job is created, we need to show payments */

                                /* Show payments */
                                $('#kt-portlet__create-job .payments--belongs').show();

                                /* append payments */
                                var paymentHtml = JOB_MODULE.payments.generateHtml();
                                // var paymentCard = `
                                // <div class="card rounded-0 mt-2">
                                //     <div class="card-body">
                                //         <span class="h6">Payment History</span>
                                //         <div class="kt-separator kt-separator--border-dashed kt-separator--space-xs"></div>
                                //         ${paymentHtml}
                                //     </div>
                                // </div>
                                // `;
                                $('#kt-portlet__create-job .payment_records').attr('data-content', paymentHtml);
                            }

                            /* call the plugin of autosize */
                            autosize($('#kt-portlet__create-job textarea'));

                            /* Update notes autosize */
                            autosize.update($('#kt-portlet__create-job [name="invoice_notes"], #kt-portlet__create-job [name="internal_notes"]'));

                            /* update the typeahead */
                            JOB_MODULE.typeahead.init();

                            /* recalculate the subtotal */
                            JOB_MODULE.calculate_subtotal();

                            /* if this is in_progress state, we need to hide save button */
                            $('#kt-portlet__create-job .btn-printsampleinvoice').hide();
                            if(job.status!="in_progress"){
                                $('#kt-portlet__create-job form [type="submit"], #kt-portlet__create-job form .btnform--bikeout.btnform--bikeoutinner').hide();
                            }
                            /* mark this data as constant data (helps in detect change) */
                            var compare_data = {
                                status:job.status,
                                plate:job.bike_id,
                                mileage:job.mileage,
                                driver_name:job.driver_name,
                                driver_phone:job.driver_phone,
                                net_terms:job.net_terms,
                                bill_to:job.subclient_id,
                                date:job.date,
                                job_items:job.services.map(function(x){return {description:x.description,rate:x.rate,qty:x.qty}}),
                                subtotal:parseFloat(job.subtotal)||0,
                                discount_value:parseFloat(job.discount.discount_value)||0,
                                discount_type:job.discount.discount_type,
                                discount_amount:parseFloat(job.discount.discount_amount)||0,
                                tax_value:parseFloat(job.tax.tax_value)||0,
                                tax_amount:parseFloat(job.tax.tax_amount)||0,
                                total:parseFloat(job.total)||0,
                                invoice_notes:job.invoice_notes,
                                internal_notes:job.internal_notes,
                            };
                            if(is_self)compare_data.is_self=true;

                            JOB_MODULE.Utils.detect_change.data=compare_data;

                            /* listen for change */
                            JOB_MODULE.Utils.detect_change.listen();
                        }
                        else{
                            /* cannot laod the job now */
                            swal.fire({
                                position: 'center',
                                type: 'error',
                                title: 'Cannot load job',
                                html: 'Job is processing.. Please retry after some time',
                            });
                        }
                        kingriders.Utils.isDebug() && console.log('loaded_job', job);
                    },

                    detect_change:function(){
                        /* this will detect if anything changes on page */

                        /**
                        | const_data: constant data, that should not be change
                        | compare_data: data to compare, is not match, data is changed
                        */
                        var check_change=function(const_data, compare_data){
                            var detected=false;
                            /* find the change (if any) */
                            Object.keys(const_data).forEach(function(key, index){

                                /* match this to other data */

                                /* check if key exists in other data */
                                if(typeof compare_data[key] !== "undefined"){
                                    /* if we got null, replcae it with "" */
                                    if(const_data[key]==null)const_data[key]="";

                                    /* check if array, we need to match inner data */
                                    if( typeof const_data[key] == "object" ){

                                        /* Check both array's lentgh */
                                        if(compare_data[key].length !== const_data[key].length){
                                            /* Length not matched, some row is deleted/added */
                                            kingriders.Utils.isDebug() && console.log('%c Change Detect on "'+key+'" Length ', 'background: #fd397a; color: #fff');
                                            kingriders.Utils.isDebug() && console.log('%c Old: ', 'background: #222; color: #bada55', const_data[key].length);
                                            kingriders.Utils.isDebug() && console.log('%c New: ', 'background: #222; color: #bada55', compare_data[key].length);
                                            detected=true;
                                        }
                                        else{

                                            /* Loop through array to match each key/s */
                                            const_data[key].forEach(function(item, index2){

                                                /* Loop through keys in inner array */
                                                Object.keys(item).forEach(function(key2, index3){
                                                    /* if we got null, replcae it with "" */
                                                    if(const_data[key][index2][key2]==null)const_data[key][index2][key2]="";

                                                    /* check if key exists in inner array of other data */
                                                    if(typeof compare_data[key][index2] !== "undefined" && typeof compare_data[key][index2][key2] !== "undefined"){
                                                        if(compare_data[key][index2][key2] != const_data[key][index2][key2]){
                                                            kingriders.Utils.isDebug() && console.log('%c Change Detect on "'+key+'" on index "'+index2+'" ', 'background: #fd397a; color: #fff');
                                                            kingriders.Utils.isDebug() && console.log('%c Old: ', 'background: #222; color: #bada55', const_data[key][index2][key2]);
                                                            kingriders.Utils.isDebug() && console.log('%c New: ', 'background: #222; color: #bada55', compare_data[key][index2][key2]);
                                                            detected=true;
                                                        }
                                                    }
                                                });

                                            });
                                        }
                                    }
                                    /* key is not an array, just match it with other data */
                                    else if(compare_data[key] != const_data[key]){
                                        kingriders.Utils.isDebug() && console.log('%c Change Detect on "'+key+'" ', 'background: #fd397a; color: #fff');
                                        kingriders.Utils.isDebug() && console.log('%c Old: ', 'background: #222; color: #bada55', const_data[key]);
                                        kingriders.Utils.isDebug() && console.log('%c New: ', 'background: #222; color: #bada55', compare_data[key]);
                                        detected=true;
                                    }
                                }
                            });

                            return detected;
                        }

                        /* this will handle button's hide/show based on status */
                        var change_detected=function(){

                            // var current_status = $('#kt-portlet__create-job form [name="status"]:checked').val();
                            // if(current_status!="in_progress"){
                            //     /* Need to show Save/Bike out button */

                            // }
                            // else{
                            //     /* status is in progress, we need to show 'Save' button accordingly */
                            //     $('#kt-portlet__create-job form [type="submit"]').show();

                            // }

                            var inputStatus=$('#kt-portlet__create-job .job_status-wrapper [name=status]:checked');
                            inputStatus.trigger('change');
                        }


                        return {
                            data:null,
                            form:'#kt-portlet__create-job form',
                            check:function(){
                                /* remove keys for typeahead, because typeahead duplicates the input with same attributes */
                                var form = $(this.form);
                                form.find('.tt-hint').removeAttr('data-name').removeAttr('name');

                                var compare_data = kingriders.Utils.formdata_to_object(new FormData(form[0]));

                                if(this.data) {
                                    /* some data found, go for a compare */

                                    /* manupulate some data */
                                    var const_data = JSON.parse( JSON.stringify( this.data ) );

                                    const_data.is_self = typeof this.data.is_self !== "undefined"?true:false;
                                    compare_data.is_self = typeof compare_data.is_self !== "undefined"?true:false;

                                    return check_change(const_data, compare_data);
                                }
                                return false; // NO CHANGE DETECTED
                            },
                            change_detected:change_detected,
                            listen:function(){
                                /* add event hadnlers on all over the form */
                                var form = $(this.form);

                                form.off('change.dce').on('change.dce', '[name="is_charge_to_driver"], [name="bill_to"], [name="net_terms"], [name="plate"], [name="discount_type"], [name="date"], [name="is_self"], [name="total"]', function(){

                                    change_detected();

                                });

                                form.off('input.dce').on('input.dce', '[name="mileage"], [name="invoice_notes"], [name="internal_notes"], [name="driver_name"], [name="driver_phone"], [name="discount_value"], [name="tax_value"], [data-name="description"]', function(){
                                    /* check if status is not already in progress */

                                    change_detected();

                                });
                            },
                            remove_listening:function(){
                                var form = $(this.form);

                                form.off('change.dce').off('input.dce');
                            },
                        };
                    }()
                },
                append_row:function(item=null){
                    var insertAfter=null;
                    if(typeof arguments[1]!=="undefined" && arguments[1])insertAfter=arguments[1];
                    var template = $('#handlebars-addrow').html();
                    // Compile the template data into a function
                    var templateScript = Handlebars.compile(template);

                    /* Append checkbox for ref [ref: Rider Account id from delivery system] */
                    var showRef = false;
                    var loaded_job = JOB_MODULE.loaded_job;
                    if(loaded_job && loaded_job.status === "complete")showRef = true;

                    /* Check if any job_item has ref assinged, we will hide all delete button because that will cause issue in reordering items */
                    var hasSingleRef = false
                    if(loaded_job){
                        hasSingleRef = loaded_job.services.findIndex(function(x){return x.ds_ref && x.ds_ref !== '' && x.ds_ref > 0})>-1;
                    }

                    var index = $('#kt-portlet__create-job .datatable tbody tr').length;
                    var context = {
                        index:index,
                        append:false
                    }
                    if(item){
                        context.item=item;
                        context.append=true;
                    }
                    else{
                        /* Dont allow showRef because current item is not saved yet */
                        showRef = false;
                    }

                    context.showRef = showRef;
                    context.hasSingleRef = hasSingleRef;

                    var html = templateScript(context);

                    if(insertAfter) insertAfter.after(html);
                    else $('#kt-portlet__create-job .datatable tbody').append(html);


                    if(!item){
                        /* call the plugin of autosize */
                        autosize($('#kt-portlet__create-job textarea'));

                        /* update the typeahead */
                        JOB_MODULE.typeahead.init();

                    }


                },
                delete_row:function(self){
                    $(self).parents('tr').remove();

                    /* check if no rows present */
                    if($('#kt-portlet__create-job .datatable tbody tr').length==0){
                        /* append a blank row */
                        JOB_MODULE.append_row();
                    }

                    this.calculate_subtotal();

                },
                select2Utils:{
                    bikes:[],
                    platechange_queue:[],
                    dynamic_tag:null,
                    /* callback when new option added to select2 */
                    insertTags:function(data, tag){
                        // Insert the tag at the end of the results
                        data.push(tag);

                    },
                    /* callback of while new option is adding to select2 */
                    createTags:function(params){
                        // Don't offset to create a tag if there is no @ symbol
                        var term = $.trim(params.term);
                        return {
                            id: params.term,
                            text: "Select to create bike: "+params.term,
                            new_tag:true
                        }
                    },
                },
                errors:{
                    clear:function(){
                        $('#kt-portlet__create-job .error-alert').hide().find('.alert-text').html('');
                    },
                    make:function(html){
                        $('#kt-portlet__create-job .error-alert').show().find('.alert-text').html(html);
                    }
                },
                alerts:{
                    clear:function(){
                        $('#kt-portlet__create-job .message-alert').hide().find('.alert-text').html('');
                    },
                    make:function(html, hide_after=3000){
                        $('#kt-portlet__create-job .message-alert').show().find('.alert-text').html(html);

                        /* Hide after mentioned time */
                        setTimeout(function(){
                            JOB_MODULE.alerts.clear();
                        }, hide_after);
                    }
                },
                bike_module:{
                    refetch_rider:function(){
                        var selectedOp = $('#kt-portlet__create-job [name=plate] :selected');
                        if(selectedOp.length){
                            selectedOp.removeAttr('data-rider')
                            .removeAttr('data-phone')
                            .removeAttr('data-krid');
                            selectedOp.parents('select').trigger('change')
                        }
                    },
                    modal_closed:function(){
                        /* modal was closed without adding data, we need to remove the tags */
                        $('#kt-portlet__create-job select[name=plate] option[data-select2-tag="true"]').remove();
                        $('#kt-portlet__create-job select[name=plate]').val(null).trigger('change.select2');
                    },
                    form_submit:function(e){
                        var response = e.response;
                        var modal = e.modal;
                        var state = e.state; // can be 'beforeSend' or 'completed'
                        var linker = e.linker;
                        if(state=='beforeSend'){
                            /* request is not completed yet, we have form data available */
                            var data = {
                                id: response.plate,
                                text: response.plate
                            };

                            var newOption = new Option(data.text, data.id, false, true);
                            $('#kt-portlet__create-job [name="plate"]').append(newOption).trigger('change.select2');
                            newOption.setAttribute('data-ref', linker);

                        }
                        else if(state=="error"){
                            /* remove option from select */

                            $('#kt-portlet__create-job select[name=plate] option[data-select2-tag="true"]').remove();
                            var opt = $('#kt-portlet__create-job [name=plate] [data-ref="'+linker+'"]');
                            if(opt.length){
                                opt.remove();
                            }
                            $('#kt-portlet__create-job select[name=plate]').val(null).trigger('change.select2');
                        }
                        else{
                            /* request might be completed and we have response from server */
                            var opt = $('#kt-portlet__create-job [name=plate] [data-ref="'+linker+'"]');
                            if(opt.length){
                                /* change the id */
                                JOB_MODULE.bikes.push(response);
                                opt.val(response.id).removeAttr('data-ref');
                                opt.text(response.plate+"-"+response.manufacturer+"-"+response.model+" | "+response.client.name);
                                opt.attr('data-client-iswalking', response.client.walking_customer);
                                kingriders.Plugins.update_select2(document.querySelector('#kt-portlet__create-job [name="plate"]'));
                            }

                            $('#kt-portlet__create-job select[name=plate]').trigger('change');

                        }
                    },
                    form_loaded:function(){
                        if(typeof BIKE_MODULE !== "undefined"){
                            BIKE_MODULE.Utils.reset_page();

                            /* add the client name */
                            var plate = $('#kt-portlet__create-job [name=plate] [data-select2-tag]:last-child').val();
                            BIKE_MODULE.container.find('[name="plate"]').val(plate).trigger('change');
                            setTimeout(function(){BIKE_MODULE.container.find('[name="plate"]').focus();},100);
                        }
                    },
                    getActiveRider:function(){
                        return new Promise(function(resolve, reject){
                            /* we will fetch assigned rider from "app.kingriders.net" app */
                            var bike_id = $('#kt-portlet__create-job [name=plate]').val();

                            var bike = JOB_MODULE.bikes.find(function(bike){return bike.id==bike_id});
                            if(typeof bike !== "undefined" && bike){
                                var plate = bike.plate;

                                var date = new Date($('#kt-portlet__create-job [name=date]').val()).format('yyyy-mm-dd');

                                /* Sends Ajax to api */
                                var _URL = "{{url('admin/jobs')}}/"+plate+'/get-activerider';
                                $.ajax({
                                    url : _URL,
                                    type : 'GET',
                                    data:{date:date},
                                    complete:function(){

                                    }
                                })
                                .done(function (response) {

                                    if(response.status && response.status==1){
                                        resolve(response);
                                    }
                                    else{
                                        reject(response);
                                    }

                                })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    reject();
                                });
                            }
                            else{
                                reject();
                            }
                        });
                    },

                    mileage:{
                        fetching:false, // When true, means system is currently fetching the mileage
                        reading:null // latest reading of mileage
                    },

                    getMileage:function(){
                        /* Will fetch old mileage against this bike and show warning if exceed from threshold */
                        var that = this;

                        /* Empty mileage warnings */
                        $('.alert.warnings_caontainer').removeClass('show');

                        return new Promise(function(resolve, reject){

                            var bike_id = $('#kt-portlet__create-job [name=plate]').val();

                            if(!bike_id)return;

                            var payload={};

                            /* check if any job is loaded */
                            if(typeof JOB_MODULE.loaded_job !== "undefined" && JOB_MODULE.loaded_job){
                                /* Pass the job_id into the payload */
                                payload.job_id = JOB_MODULE.loaded_job._id;
                            }

                            var url = "{{ route('tenant.admin.jobs.get_last_mileage', '_:param') }}".replace('_:param', bike_id);
                            $.ajax({
                                url : url,
                                headers:{'X-NOFETCH':''}, /* don't allow fetch accounts */
                                type : 'GET',
                                data:payload,
                                beforeSend: function() {
                                    that.mileage.fetching = true;
                                },
                                complete: function(){
                                    that.mileage.fetching = false;
                                }
                            })
                            .done(function(response){
                                that.mileage.reading = parseFloat(response.mileage)||0;
                                resolve(response);
                            })
                            .fail(function (jqXHR, textStatus, errorThrown) {

                                /* this will handle & show errors */
                                kingriders.Plugins.KR_AJAX.showErrors(jqXHR);
                            });
                        });
                    },

                    recent_invoices:{
                        fetching:false, // When true, means system is currently fetching the invoices
                        data:null // latest data
                    },

                    getRecentInvoices:function(){
                        /* Will fetch recent invoices against this bike */
                        var that = this;

                        return new Promise(function(resolve, reject){

                            var bike_id = $('#kt-portlet__create-job [name=plate]').val();

                            if(!bike_id)return;

                            var payload={};

                            /* check if any job is loaded */
                            if(typeof JOB_MODULE.loaded_job !== "undefined" && JOB_MODULE.loaded_job){
                                /* Pass the job_id into the payload */
                                payload.job_id = JOB_MODULE.loaded_job._id;
                            }

                            var url = "{{ route('tenant.admin.jobs.get_recent_invoices', '_:param') }}".replace('_:param', bike_id);
                            $.ajax({
                                url : url,
                                headers:{'X-NOFETCH':''}, /* don't allow fetch accounts */
                                type : 'GET',
                                data:payload,
                                beforeSend: function() {
                                    that.recent_invoices.fetching = true;
                                },
                                complete: function(){
                                    that.recent_invoices.fetching = false;
                                }
                            })
                            .done(function(invoices){
                                that.recent_invoices.data = invoices;

                                // Render HTML
                                var table = $('.table-recentinvoices');
                                var rows = '';
                                if(invoices.length > 0){
                                    invoices.forEach(function(item){
                                        var date = moment(item.date).format("DD/MMM/YY");
                                        rows += `
                                        <tr>
                                            <td>#${item.invoice.id}</td>
                                            <td>${date}</td>
                                            <td>${item.total}</td>
                                            <td>
                                                <a target="_blank" href="${`{{route('tenant.admin.jobs.single.view', "_:param")}}`.replace('_:param', item._id)}" >View <i class="fa fa-external-link-alt"></i></a>
                                            </td>
                                        </tr>
                                        `;
                                    });
                                }
                                else{
                                    // No records found? render empty row
                                    rows += `
                                    <tr>
                                        <td colspan="4"><div class="text-center text-muted">No invoices found</div></td>
                                    </tr>
                                    `;
                                }

                                // Append rows
                                table.find('tbody').html(rows);


                                resolve(invoices);
                            })
                            .fail(function (jqXHR, textStatus, errorThrown) {

                                /* this will handle & show errors */
                                kingriders.Plugins.KR_AJAX.showErrors(jqXHR);
                            });
                        });
                    }
                },
                Bill_to:{
                    modal_closed:function(){

                        /* modal was closed without adding data, we need to remove the tags */
                        $('#kt-portlet__create-job select[name=bill_to] option[data-select2-tag="true"]').remove();
                        $('#kt-portlet__create-job select[name=bill_to]').val(null).trigger('change.select2');

                        $('#kt-portlet__create-job [name="is_charge_to_driver"]').prop('checked', false);

                    },
                    form_submit:function(e){
                        var response = e.response;
                        var modal = e.modal;
                        var state = e.state; // can be 'beforeSend' or 'completed'
                        var linker = e.linker;
                        if(state=='beforeSend'){
                            /* request is not completed yet, we have form data available */
                            var data = {
                                id: response.name,
                                text: response.name
                            };

                            var newOption = new Option(data.text, data.id, false, true);
                            $('#kt-portlet__create-job [name="bill_to"]').append(newOption).trigger('change.select2');
                            newOption.setAttribute('data-ref', linker);

                        }
                        else if(state=="error"){
                            /* remove option from select */

                            $('#kt-portlet__create-job select[name=bill_to] option[data-select2-tag="true"]').remove();
                            var opt = $('#kt-portlet__create-job [name=bill_to] [data-ref="'+linker+'"]');
                            if(opt.length){
                                opt.remove();
                            }
                            $('#kt-portlet__create-job select[name=bill_to]').val(null).trigger('change.select2');
                        }
                        else{
                            /* request might be completed and we have response from server */
                            var opt = $('#kt-portlet__create-job [name=bill_to] [data-ref="'+linker+'"]');
                            if(opt.length){
                                /* Check if sub clinet */
                                if(response.is_sub==1){
                                    /* change the id */
                                    opt.val(response.id).removeAttr('data-ref');
                                    opt.text(response.name);

                                }
                                else{
                                    /* Remove the dynamic tag since added client was not sub client */
                                    opt.remove();
                                }

                                /* Update selector */
                                kingriders.Plugins.update_select2(document.querySelector('#kt-portlet__create-job [name="bill_to"]'));
                            }

                            $('#kt-portlet__create-job select[name=bill_to]').trigger('change');

                        }
                    },
                    form_loaded:function(){
                        if(typeof CLIENT_MODULE !== "undefined"){
                            CLIENT_MODULE.Utils.reset_page();

                            /* add the client name */
                            var elem = $('#kt-portlet__create-job [name=bill_to] [data-select2-tag]:last-child');
                            var client_name = elem.text();
                            var krid = elem.attr('data-krid');
                            krid = typeof krid == "undefined"?"":krid;
                            krid = krid==""?0:krid;

                            $(CLIENT_MODULE.container).find('[name="name"]').val(client_name);

                            /* Change to sub client */
                            var bike_id = $('#kt-portlet__create-job [name=plate]').val();

                            $(CLIENT_MODULE.container).find('[name="is_sub"]').prop('checked', true).trigger('change');

                            /* Check if triggered from charge to driver option, we need to put phone number too */


                            /* It seems process is sytematically, we asume form is opened through 'charge to driver' option */
                            var phone = $('#kt-portlet__create-job [name=driver_phone]').val();
                            $(CLIENT_MODULE.container).find('[name="phone"]').val(phone);

                            /* Marked this customer as walking customer since it is simple driver */
                            $(CLIENT_MODULE.container).find('[name="walking_customer"]').prop('checked', true).trigger('change');

                            if(krid!=0){
                                /* update, krid */
                                $(CLIENT_MODULE.container).find('form [name="krid"]').val(krid);
                            }



                            setTimeout(function(){$(CLIENT_MODULE.container).find('[name="name"]').focus();},100);
                        }
                    },
                },
                receive_payment:{
                    modal_closed:function(){


                    },
                    form_submit:function(e){
                        var response = e.response;
                        var modal = e.modal;
                        var state = e.state; // can be 'beforeSend' or 'completed'
                        var linker = e.linker;
                        if(state=='beforeSend'){
                            /* request is not completed yet, we have form data available */
                            KTApp.block('#kt-portlet__create-job',{
                                overlayColor: '#000',
                                type: 'v2',
                                state: 'primary',
                                message: 'Please wait while job is processing...'
                            });


                        }
                        else if(state=="error"){
                            /* Unblock modal */

                            KTApp.unblock('#kt-portlet__create-job');
                        }
                        else{
                            /* request might be completed and we have response from server */

                            /* Update client */

                            if(typeof OPENINVOICE_MODULE !== "undefined"){
                                /* Find by client index */
                                var clientIndex = OPENINVOICE_MODULE._rawClients.findIndex(function(x){return x.id==response.id});
                                if(clientIndex>-1){
                                    OPENINVOICE_MODULE._rawClients[clientIndex] = response;

                                    /* Update clients */
                                    OPENINVOICE_MODULE._orderClients();
                                }
                            }

                            /* Find Updated job */

                            if(typeof JOB_MODULE !== "undefined"){
                                var loaded_job = JSON.parse( JSON.stringify( JOB_MODULE.loaded_job ) );

                                if(typeof loaded_job !== "undefined" && loaded_job){
                                    var job_id = loaded_job._id;

                                    /* Search this job in response */
                                    var updated_job = response.jobs.find(function(job){return job._id==job_id});
                                    if(typeof updated_job !== "undefined" && updated_job){
                                        /* Merge updated job with laoded job */

                                        /* Update invoice */
                                        loaded_job.invoice=updated_job.invoice;


                                        /* Invalidate the job */
                                        JOB_MODULE.loaded_job = loaded_job;

                                        if(typeof JOBS !== "undefined" && JOBS){
                                            /* Invalidate the datables row */

                                            /* Find row node */
                                            var rowNode = JOBS.datatable.row(function(x, job){return job._id==loaded_job._id}).node();
                                            if(rowNode){
                                                JOBS.datatable.row(rowNode).data(loaded_job).invalidate();

                                                /* remove the cache data */
                                                $(rowNode).removeAttr('data-row')
                                                .removeAttr('data-temp')
                                                .removeAttr('style');
                                            }

                                            /* Reload the modal */
                                            JOB_MODULE.Utils.load_job(loaded_job);
                                        }

                                    }
                                }
                            }

                            /* unblock the modal */
                            KTApp.unblock('#kt-portlet__create-job');


                        }


                        kingriders.Utils.isDebug() && console.log('response', e);
                    },
                    form_loaded:function(){
                        if(typeof OPENINVOICE_MODULE !== "undefined"){

                            setTimeout(function(){

                                var loaded_job = JOB_MODULE.loaded_job;
                                if(loaded_job){
                                    /* Pass to receive payment modal, so it will load it accordingly */

                                    OPENINVOICE_MODULE.Utils.load_page({
                                        client_id:loaded_job.client.id,
                                        invoice_id:loaded_job.invoice.id
                                    });

                                }

                            }, 50);

                        }
                    },
                },
                typeahead:function(){
                    var substringMatcher = function(strs) {
                        return function findMatches(q, cb) {
                            var matches, substringRegex;
                            // an array that will be populated with substring matches
                            matches = [];

                            // iterate through the pool of strings and for any string that
                            // starts with the substring `q`, add it to the `matches` array
                            $.each(strs, function(i, str) {

                                // Modified to use substring instead of RegEx
                                if (str.text.substr(0,q.length).toUpperCase() == q.toUpperCase()) {
                                    matches.push(str);
                                }
                            });

                            cb(matches);
                        };
                    };
                    return {
                        is_new:true,
                        instance:null,
                        selector:'#kt-portlet__create-job .typeahead-input',
                        source:null,
                        _sugs:{!! $sugs !!},
                        init:function(){

                            /* get all the descriptions there */
                            JOB_MODULE.typeahead.source = JOB_MODULE.typeahead._sugs;
                            if(JOB_MODULE.typeahead.source.length == 0)return;

                            /* remove duplicate data */
                            // JOB_MODULE.typeahead.source = JOB_MODULE.typeahead.source.filter(function(job, index, self){
                            //     return index == self.findIndex(function(y){
                            //         return y.text.toLowerCase()==job.text.toLowerCase();
                            //     });
                            // });

                            /* search engine */
                            JOB_MODULE.typeahead.bloodhoundEngine = new Bloodhound({
                                datumTokenizer: function (d) {
                                    return Bloodhound.tokenizers.whitespace(d.text);
                                },
                                queryTokenizer: Bloodhound.tokenizers.whitespace,
                                local:JOB_MODULE.typeahead.source
                            });
                            JOB_MODULE.typeahead.bloodhoundEngine.initialize();

                            /* initialize typeahead */
                            if(JOB_MODULE.typeahead.instance)JOB_MODULE.typeahead.instance.typeahead('destroy');
                            JOB_MODULE.typeahead.instance = $(JOB_MODULE.typeahead.selector).typeahead({
                                hint: true,
                                highlight: true,
                                minLength: 1
                            }, {
                                name: 'job_services',
                                source: JOB_MODULE.typeahead.bloodhoundEngine,
                                display:"text",
                                templates: {
                                    header: function() {
                                        return '<table class="typeahead-table__head"><tr>' +
                                            '   <th nowrap>Type</th>' +
                                            '   <th nowrap>Detail</th>' +
                                            '   <th nowrap>Rate</th>' +
                                            '</tr></table>';
                                        ;
                                    },
                                    suggestion: function (data) {
                                        return '<table class="typeahead-table__body"><tr>' +
                                        '   <td nowrap>' + (data.type=="service"?'<i class="la la-wrench"></i> Service':'<i class="la la-cogs"></i> Part <span class="d-inline-block text-danger font-weight-bold">('+data.inventory.remaining+' left)</span>' )+ '</td>'+
                                        '   <td nowrap>' + data.text + '</td>' +
                                        '   <td nowrap>' + data.value + '</td>' +
                                        '</tr></table>';
                                        return '<p class="text-left m-0">' + data.text + '  <span class="text-danger float-right font-weight-bold">AED ' + data.value + '</span></p>';
                                    }
                                }
                            });

                            /* event listener to make some convenience */
                            $(JOB_MODULE.typeahead.selector).on('typeahead:select', function(ev, suggestion) {
                                var target = ev.currentTarget;
                                JOB_MODULE.typeahead.make_selection(suggestion, 'select', target);
                            });
                            // $(JOB_MODULE.typeahead.selector).on('typeahead:render', function(ev, suggestion, is_async, source_name) {
                            //     var target = ev.currentTarget;
                            //     JOB_MODULE.typeahead.preview_selection(suggestion, 'render',target);
                            // });
                            $(JOB_MODULE.typeahead.selector).on('typeahead:autocomplete', function(ev, suggestion) {
                                var target = ev.currentTarget;
                                JOB_MODULE.typeahead.make_selection(suggestion, 'autocomplete',target);
                            });
                            // $(JOB_MODULE.typeahead.selector).on('typeahead:cursorchange', function(ev, suggestion) {
                            //     var target = ev.currentTarget;
                            //     JOB_MODULE.typeahead.preview_selection(suggestion, 'cursorchange',target);
                            // });
                            $(JOB_MODULE.typeahead.selector).on('keyup', function(){
                                var val = this.value;
                                if(val=='' || !val)JOB_MODULE.typeahead.clear_selection(this);
                            });
                        },
                        preview_selection:function(datum, event, target){
                            JOB_MODULE.typeahead.is_new=true;
                            if(typeof datum !== "undefined"){
                                /* preview this rate */
                                JOB_MODULE.typeahead.is_new=false;

                                var rate = datum.value;
                                var rowNode = $(target).parents('tr');
                                var rateElem = rowNode.find('[data-name="rate"]').val(null).attr('placeholder', rate);

                                return;
                            }

                            /* clear preview */
                            JOB_MODULE.typeahead.clear_selection();

                        },
                        make_selection:function(datum, event, target){
                            JOB_MODULE.events.trigger_mileage();

                            if(typeof datum !== "undefined"){
                                /* make this rate */
                                var rate = datum.value;
                                var rowNode = $(target).parents('tr');
                                var rateElem = rowNode.find('[data-name="rate"]').val(rate).trigger('input');

                                /* need to add max qty if this is part */
                                rowNode.find('[data-name="part_id"]').remove();
                                rowNode.find('[data-name="qty"]').removeAttr('qty-left');
                                if(datum.type=="part"){
                                    /* add part_id */
                                    var index=rowNode.index();
                                    var _remaining=datum.inventory.remaining;

                                    /* if this is edit page, we need to increase the qty that are already added in this job */
                                    if($('#kt-portlet__create-job [name=job_id]').length){

                                        /* find the current part already added inventory */
                                        var part_inventory=0;
                                        if(JOB_MODULE.loaded_job){
                                            /* search on loaded job and add already added qty to inventory */
                                            JOB_MODULE.loaded_job.services.forEach(function(service, i1){
                                                if(service.inventory_item && service.inventory_item.part_id==datum.part_id)part_inventory=service.inventory_item.qty;
                                            });

                                            _remaining+=part_inventory;
                                        }
                                    }
                                    rowNode
                                    .find('[data-name="qty"]')
                                    .attr('qty-left', _remaining)
                                    .after('<input type="hidden" data-name="part_id" name="job_items['+index+'][part_id]" class="form-control rounded-0" placeholder="" value="'+datum.part_id+'">')
                                    .trigger('input');

                                }

                                /* add animation */
                                rowNode.find('[data-name="rate"]').addClass('krselect2--animation');

                                setTimeout(function () {
                                    rowNode.find('[data-name="rate"]').removeClass('krselect2--animation');
                                }, 2500);

                                /* if autocomplete occurs, close the suggestion box too */
                                if(event=='autocomplete')$(JOB_MODULE.typeahead.selector).typeahead('close');

                                return;
                            }

                            /* clear preview */
                            JOB_MODULE.typeahead.clear_selection(target);
                        },
                        clear_selection:function(target){
                            // return;
                            var rowNode = $(target).parents('tr');
                            var rateElem = rowNode.find('[data-name="rate"]').val(null).attr('placeholder', '').trigger('input');

                            /* remove part things */
                            rowNode.find('[data-name="part_id"]').remove();
                            rowNode.find('[data-name="qty"]').removeAttr('qty-left');
                        }
                    }
                }(),
                save_progress:function(){
                     /* allow closing */
                    if(typeof JOBS !== "undefined"){
                        JOBS.events.allow_closing=true;
                    }
                    var plate = $('#kt-portlet__create-job [name=plate]').val();
                    var is_valid=true;


                    plate && (plate=plate.trim());

                    /* clear the errors */
                    JOB_MODULE.errors.clear();

                    /* bike is required */
                    if(!plate){
                        JOB_MODULE.errors.make("Please enter <strong>Bike</strong>.");
                        is_valid=false;
                    }

                    if(is_valid){

                        $('#kt-portlet__create-job form').trigger('submit');
                    }
                    else{
                        return; /* terminate the process */
                    }

                    /* block modal, so update job can be updated in modal */
                    KTApp.block('#kt-portlet__create-job',{
                        overlayColor: '#000',
                        type: 'v2',
                        state: 'primary',
                        message: 'Please wait while job is processing...'
                    });


                    // if($('#kt-portlet__create-job [name=job_id]').length){
                    //     // edit page

                    //     /* hide the modal (if found) */
                    //     var MODAL = $('#kt-portlet__create-job').parents('.modal');
                    //     if(MODAL.length){
                    //         MODAL.modal('hide');
                    //     }
                    // }
                    // else{
                    //     // add page


                    //     /* reset the page */
                    //     // JOB_MODULE.Utils.reset_page(true);

                    //     /* block modal */
                    //     KTApp.block('#kt-portlet__create-job',{
                    //         overlayColor: '#000',
                    //         type: 'v2',
                    //         state: 'primary',
                    //         message: 'Please wait while job is processing...'
                    //     });
                    // }

                },
                bike_out:function(self){
                    /* Confirm it */
                    /* confirm user for before closing */
                    swal.fire({
                        title: 'Are you sure?',
                        position: 'center',
                        type: 'warning',
                        text: "Bike out will set to current time",
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No',
                        showCancelButton: true,
                        reverseButtons: true
                    }).then((result) => {

                        if (result.value) {

                            /* Submit form with complete status */

                            /* This will complete job after validation */
                            JOB_MODULE.complete_job(true);

                        }
                    });
                },
                complete_job:function(do_bikeout=false){
                    /* change the status to complete and submit the form */
                    var total=parseFloat($('#kt-portlet__create-job [name=total]').val())||0;
                    var plate = $('#kt-portlet__create-job [name=plate]').val();
                    var mileage = $('#kt-portlet__create-job [name=mileage]').val();
                    var is_self = $('#kt-portlet__create-job [name=is_self]').is(':checked');
                    var is_valid=true;

                    plate && (plate=plate.trim());
                    mileage && (mileage=mileage.trim());

                    /* clear the errors */
                    JOB_MODULE.errors.clear();

                    /* total must be greater than 0 */
                    if(total<=0){
                        JOB_MODULE.errors.make("<strong>Total</strong> must be greater than 0.");
                        is_valid=false;
                    }
                    /* bike is required */
                    if(!plate){
                        JOB_MODULE.errors.make("Please enter bike.");
                        is_valid=false;
                    }

                    /* Check if services contains 'oil change'. No metter the order */
                    if(JOB_MODULE.isOilPresent()){

                        /* mileage is required */
                        if(!mileage){
                            JOB_MODULE.errors.make("Please enter mileage.");
                            is_valid=false;
                        }

                        /* mileage is greater then 0 */
                        if(mileage && !(mileage>0)){
                            JOB_MODULE.errors.make("Mileage must be greater than 0.");
                            is_valid=false;
                        }
                    }



                    if(!is_self){
                        /* validate driver details too */
                        var driver_name = $('#kt-portlet__create-job [name=driver_name]').val();
                        var driver_phone = $('#kt-portlet__create-job [name=driver_phone]').val();

                        driver_name && (driver_name=driver_name.trim());
                        driver_phone && (driver_phone=driver_phone.trim());

                        /* driver name is required */
                        if(!driver_name){
                            JOB_MODULE.errors.make("Please enter <strong>Driver Name<strong>.");
                            is_valid=false;
                        }

                        /* driver phone is required */
                        if(!driver_phone){
                            JOB_MODULE.errors.make("Please enter <strong>Driver Phone<strong>.");
                            is_valid=false;
                        }

                    }

                    if(is_valid){
                        /* allow closing */
                        if(typeof JOBS !== "undefined"){
                            JOBS.events.allow_closing=true;
                        }

                        /* block modal */
                        KTApp.block('#kt-portlet__create-job',{
                            overlayColor: '#000',
                            type: 'v2',
                            state: 'primary',
                            message: 'Please wait while job is processing...'
                        });
                        $('#kt-portlet__create-job form [name="status"][value="complete"]').prop('checked', true).trigger('change');

                        /* Check for dobikeout */
                        $('#kt-portlet__create-job form').find('[name=do_bikeout]').remove();
                        if(do_bikeout){
                            $('#kt-portlet__create-job form').append('<input type="hidden" name="do_bikeout" value="">');
                        }

                        $('#kt-portlet__create-job form').trigger('submit');


                    }
                },
                place_on_hold:function(){
                    /* allow closing */
                    if(typeof JOBS !== "undefined"){
                        JOBS.events.allow_closing=true;
                    }
                    KTApp.block('#kt-portlet__create-job',{
                        overlayColor: '#000',
                        type: 'v2',
                        state: 'primary',
                        message: 'Please wait while job is processing...'
                    });
                    $('#kt-portlet__create-job form [name="status"][value="on_hold"]').prop('checked', true).trigger('change');
                    $('#kt-portlet__create-job form').trigger('submit');

                },
                unhold_invoice:function(){
                    /* allow closing */
                    if(typeof JOBS !== "undefined"){
                        JOBS.events.allow_closing=true;
                    }
                    KTApp.block('#kt-portlet__create-job',{
                        overlayColor: '#000',
                        type: 'v2',
                        state: 'primary',
                        message: 'Please wait while job is processing...'
                    });
                    $('#kt-portlet__create-job form [name="status"][value="in_progress"]').prop('checked', true).trigger('change');
                    $('#kt-portlet__create-job form').trigger('submit');
                },
                invoice:{
                    get:function(){
                        /* check if any job is loaded */
                        if(typeof JOB_MODULE.loaded_job !== "undefined" && JOB_MODULE.loaded_job){
                            /* check if loaded job has invoice */
                            if(typeof JOB_MODULE.loaded_job.invoice !== "undefined" && JOB_MODULE.loaded_job.invoice)return JOB_MODULE.loaded_job.invoice;
                        }
                        return null;
                    },
                    getPayments:function(){
                        var invoice = this.get();
                        if(invoice){
                            if(invoice.payments){
                                return {
                                    payments:invoice.payments,
                                    total:invoice.payments.reduce(function(total,item){return total+(parseFloat(item.amount)||0)},0)
                                };
                            }

                        }

                        return null;
                    },
                    print:function(job){
                        setTimeout(function(){
                            /* pass data to handlebars template to compile */
                            var template = $('#handlebars-printinvoice').html();
                            // Compile the template data into a function
                            var templateScript = Handlebars.compile(template);

                            /* Need to modify client, if subclient found */
                            if(typeof job.subclient !== "undefined" && job.subclient){
                                /* Make it immutable */
                                var subclient = JSON.parse(JSON.stringify(job.subclient));


                                /* check if subclient was walking customer, we should use parent client details instead of subclient */
                                if(subclient.walking_customer==1){
                                    /* Overwrite the details */
                                    subclient.email = job.client.email;
                                    subclient.trn = job.client.trn;
                                    subclient.address = job.client.address;
                                }


                                /* Modify job client */
                                job.client=subclient;
                            }

                            var html = templateScript(job);
                            $('.invoice__wrapper').html(html);
                            printJS('invoice_slip', 'html');
                        },0);

                    },
                    printSampleInvoice: function(){

                        setTimeout(function(){
                            /* we need to create job based on form data */
                            var form = $('#kt-portlet__create-job form');
                            var sampleJob = kingriders.Utils.formdata_to_object(new FormData(form[0]));

                            /* Manupilate job */
                            sampleJob.job_items = sampleJob.job_items.map(function(item, index){
                                var rate = parseFloat(item.rate)||0;
                                var qty = parseFloat(item.qty)||0;
                                return {
                                    description: JOB_MODULE.typeahead.instance.filter('[name="job_items['+index+'][description]"]').val(),
                                    rate: rate,
                                    qty: qty,
                                    total: rate*qty
                                }
                            });

                            /* pass data to handlebars template to compile */
                            var template = $('#handlebars-printsampleinvoice').html();
                            // Compile the template data into a function
                            var templateScript = Handlebars.compile(template);

                            var html = templateScript(sampleJob);
                            $('.invoice__wrapper').html(html);
                            printJS('invoice_slip', 'html');
                        },0);
                    },
                    handleclick:function(self){

                        /* we need to create job based on form data */
                        var form = $(self).parents('form');
                        var formData = kingriders.Utils.formdata_to_object(new FormData(form[0]));

                        /* check if job_id found */
                        var job_id = null;
                        if(typeof formData.job_id !== "undefined" && formData.job_id)job_id=formData.job_id;

                        if(job_id){
                            var jobs = JOBS.datatable.rows().data().toArray();
                            var job = jobs.find(function(job){return job._id==job_id});
                            if(typeof job !== "undefined" && job){
                                /* job found, proceed with printing process */
                                this.print(job);
                                return ;
                            }
                        }

                        /* job was not found, we cannot print the invoice */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: 'Cannot print invoice',
                            html: 'Job is not completed, please complete the job so invoice can be generated.',
                        });
                    }
                },
                payments:{
                    generateHtml:function(){
                        var html='<span class="d-block text-center small text-muted">No payments found</span>';
                        /* Check if current job has invoice */
                        var paymentsObj = JOB_MODULE.invoice.getPayments();
                        if(paymentsObj){
                            /* check if payments found */
                            if(paymentsObj.payments && paymentsObj.payments.length){

                                var payments = Object.assign([], paymentsObj.payments);

                                /* Sort payments by created_at */
                                payments=payments.sort(function(a,b){
                                    var dateA = moment(a.created_at);
                                    var dateB = moment(b.created_at);

                                    if(dateA.isAfter(dateB))return -1;
                                    else if(dateA.isSame(dateB)) return 0;
                                    return 1;
                                });

                                /* Append payments */
                                html='<ul class="list-group">';
                                payments.forEach(function(payment){
                                    var by = payment.by;
                                    var date = new Date(payment.date).format('dd/mm/yyyy');
                                    var code = payment.code;
                                    var amount = payment.amount;

                                    html+=''+
                                    '<li class="list-group-item d-flex justify-content-between align-items-center rounded-0">'+
                                    '   <small>'+
                                    '       <span class="text-muted">#'+code+')</span> '+
                                    '       <b>AED '+amount+'</b>'+
                                    '       Payment on '+date+' by '+by+
                                    (payment.manually_paid?' <span class="text-warning">(Manually)</span>':'')+
                                    '   </small>'+
                                    '</li>';
                                });
                                html+='</ul>';
                            }
                        }

                        return html;
                    }
                },

                isOilPresent:function(){
                    /* Check if services contains 'oil change'. No metter the order */
                    var hasOil = $('#kt-portlet__create-job .tt-input[data-name="description"]')
                    .toArray()
                    .map(function(chunk){return chunk.value})
                    .findIndex(function(x){return x&&x.toLowerCase().match(/oil.*change|change.*oil/g)})>-1;

                    return hasOil;
                },
                events:{
                    onchange_netterms:function(){
                        /* Pick date, add terms and update due date */
                        var terms = parseInt($('#kt-portlet__create-job [name="net_terms"]').val())||0;
                        var date = moment($('#kt-portlet__create-job [name="date"]').val(), 'MMMM DD, YYYY').add(terms, "day").format('MMMM DD, YYYY');

                        /* Update due date */
                        $('#kt-portlet__create-job [name="due_date"]').attr('data-default', date).datepicker('update', date);
                    },

                    trigger_mileage:function(){

                        /* Empty mileage warnings */
                        $('.alert.warnings_caontainer').removeClass('show');

                        /* Check if services contains 'oil change'. No metter the order */
                        if(!JOB_MODULE.isOilPresent())return;

                        var currentMileage = parseFloat($('#kt-portlet__create-job [name=mileage]').val())||0;

                        var process = function(){

                            if(JOB_MODULE.bike_module.mileage.reading !== null){
                                var oldMileage = JOB_MODULE.bike_module.mileage.reading;
                                console.log('oldMileage', oldMileage);
                                console.log('currentMileage', currentMileage);

                                /* Show alert if mileage if more then threshold */
                                var diff = currentMileage - oldMileage;
                                var alertContent = '';

                                if(diff>MILEAGE_THRESHOLD){

                                    /* Above mileage */

                                    alertContent = `
                                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                        <div class="alert-text">
                                            <h4 class="alert-heading font-weight-normal">Above mileage by <strong>${diff-MILEAGE_THRESHOLD}</strong></h4>
                                            <div>
                                                Current Mileage: <strong>${currentMileage}</strong>
                                            </div>
                                            <div>
                                                Last Mileage: <strong>${oldMileage}</strong>
                                            </div>
                                        </div>
                                    `;


                                    /* Add success */
                                    $('.alert.warnings_caontainer')
                                    .removeClass('alert-warning')
                                    .removeClass('alert-success')
                                    .addClass('alert-warning');


                                }
                                else if(diff>0){
                                    /* Under mileage */

                                    alertContent = `
                                        <div class="alert-icon"><i class="flaticon2-check-mark"></i></div>
                                        <div class="alert-text">
                                            <h4 class="alert-heading font-weight-normal">Under mileage by <strong>${diff}</strong></h4>
                                            <div>
                                                Current Mileage: <strong>${currentMileage}</strong>
                                            </div>
                                            <div>
                                                Last Mileage: <strong>${oldMileage}</strong>
                                            </div>
                                        </div>
                                    `;

                                    /* Add success */
                                    $('.alert.warnings_caontainer')
                                    .removeClass('alert-warning')
                                    .removeClass('alert-success')
                                    .addClass('alert-success');

                                }

                                if(alertContent !== ''){

                                    /* Append html */
                                    $('.alert.warnings_caontainer').html(alertContent);
                                    $('.alert.warnings_caontainer').removeClass('show').addClass('show');

                                }
                            }

                        }

                        /* Check if we have latest reading, if not, need to send it */
                        if(JOB_MODULE.bike_module.mileage.reading === null){
                            /* Need to fetch mileage from server */

                            /* Check if not already fetching */
                            if(!JOB_MODULE.bike_module.mileage.fetching){
                                JOB_MODULE.bike_module.getMileage()
                                .then(function(job){
                                    process();
                                })
                            }
                        }
                        else process();

                    }
                }
            };
        }();

        $(function(){
            JOB_MODULE.append_row();

            JOB_MODULE.calculate_subtotal();

            /* Load any tooltips */
	        KTApp.initTooltips();

            /*
            |---------------------------------
            |               EVENTS
            |---------------------------------
            */

            $('#kt-portlet__create-job [name=plate]').on('change', function(){
                var self = $(this);
                var selected = self.find(':selected');
                var plate = self.val();
                // chances are, new tag is added
                if (typeof selected.attr('data-select2-tag') !== "undefined" && selected.attr('data-select2-tag') == 'true'){
                    /* push it to plate change queue so after new data renderd, it will change the plate */

                    /* we need to show form to create this record */
                    var btn = $('#kt-portlet__create-job [data-create-bike]');
                    if(btn.length){
                        btn.trigger('click');
                    }
                }
                else{

                    /* Update mileage */
                    JOB_MODULE.bike_module.getMileage();

                    /* Update recent invoices */
                    JOB_MODULE.bike_module.getRecentInvoices();

                }

                $('#kt-portlet__create-job [name="is_charge_to_driver"]').prop('checked', false);
                $('#kt-portlet__create-job [name="bill_to"]').val(null).trigger('change.select2');

                /* change self option based on client type */
                var is_walking = $('#kt-portlet__create-job [name=plate] :selected').attr('data-client-iswalking');
                if(is_walking==1){
                    $('#kt-portlet__create-job [name=is_self]').prop('checked', true).trigger('change');

                }
                else{


                    /* Get active rider against this plate */

                    /* First, check if data was already fetch and stored in attributed, get it from there */
                    var that = this.options[this.selectedIndex];
                    if(that.hasAttribute('data-rider')){
                        var rider_name = that.getAttribute('data-rider')||null;
                        var phone = that.getAttribute('data-phone')||null;
                        var krid = that.getAttribute('data-krid')||null;

                        /* append to form */
                        $('#kt-portlet__create-job [name="driver_name"]').val(rider_name).attr('data-krid', krid);
                        $('#kt-portlet__create-job [name="driver_phone"]').val(phone);
                    }
                    else{

                        /* Fetch from api */
                        JOB_MODULE.bike_module.getActiveRider()
                        .then(function(response){

                            kingriders.Utils.isDebug() && console.log('Active Rider: ', response);

                            /* Append the data */

                            /* Fetch data */
                            var rider_name = null,
                            krid=null;
                            var phone = null;

                            if(response.rider){
                                rider_name='KR'+response.rider.id+' '+response.rider.name;
                                krid = response.rider.id;
                            }
                            if(response.sim)phone=response.sim.sim_number;

                            /* store it to attibutes */
                            that.setAttribute('data-rider', rider_name);
                            $(that).removeAttr('data-krid');
                            krid && (that.setAttribute('data-krid', krid));
                            that.setAttribute('data-phone', phone);

                            /* append to form */
                            $('#kt-portlet__create-job [name="driver_name"]').val(rider_name);
                            krid && ($('#kt-portlet__create-job [name="driver_name"]').attr('data-krid', krid));
                            $('#kt-portlet__create-job [name="driver_phone"]').val(phone);

                        })
                        .catch(function(response){
                            if(typeof response !== "undefined" && response){
                                if(typeof response.status !== "undefined" && response.status==0){
                                    /* some response given from server and it seems data not found */

                                    /* store empty it to attibutes */
                                    that.setAttribute('data-rider', '');
                                    that.setAttribute('data-phone', '');

                                    /* append to form */
                                    $('#kt-portlet__create-job [name="driver_name"]').val(null).removeAttr('data-krid');
                                    $('#kt-portlet__create-job [name="is_charge_to_driver"]').prop('checked', false);
                                    $('#kt-portlet__create-job [name="driver_phone"]').val(null);
                                }
                            }
                        });

                    }

                }




            });


            /* Listen to mileage */
            $('#kt-portlet__create-job [name=mileage]').on('input', function(){

                JOB_MODULE.events.trigger_mileage();

            });

            /* Disable form submit when enter press on inputs */
            $(` #kt-portlet__create-job [name=driver_name],
                #kt-portlet__create-job [name=driver_phone],
                #kt-portlet__create-job [name=mileage],
                #kt-portlet__create-job [name=date],
                #kt-portlet__create-job [name=due_date],
                #kt-portlet__create-job [name=discount_value],
                #kt-portlet__create-job [name=tax_value]
            `).on('keypress', function(e){
                if(e.keyCode === 13){
                    e.preventDefault();
                }
            });



            $('#kt-portlet__create-job [name=is_charge_to_driver]').on('change', function(){
                var is_checked = $(this).is(':checked');
                var val = $('#kt-portlet__create-job [name=driver_name]').val();

                if(is_checked){

                    val && (val=val.trim());
                    if(!val)return;

                    var krid = $('#kt-portlet__create-job [name=driver_name]').attr('data-krid');
                    krid = typeof krid == "undefined"?"":krid;
                    krid = krid==""?0:krid;

                    var name = $('#kt-portlet__create-job [name=driver_name]').val();
                    var phone = $('#kt-portlet__create-job [name=driver_phone]').val();


                    /* Append to bill to */


                    /* Check if same name found on bill to */
                    var op=null;
                    if(krid!=0){
                        /* Search for same krid in bill_to dropdown */
                        var opF = $('#kt-portlet__create-job [name=bill_to] option[data-krid="'+krid+'"]');

                        if(opF.length){
                            op = opF;
                        }

                    }
                    else {
                        /* Search for text */
                        var opF = $('#kt-portlet__create-job [name=bill_to] option:contains('+name+')');
                        if(opF.length){
                            op = opF;
                        }
                    }


                    /* Check if any option was found */
                    if(op){
                        /* just select the option */
                        op.prop('selected', true);

                        $('#kt-portlet__create-job [name=bill_to]').trigger('change');
                    }
                    else{
                        /* Create new option and trigger new client form */

                        var op=new Option(name, name, false, true);
                        op.setAttribute('data-select2-tag', true); /* So that client add form can be open */
                        op.setAttribute('data-krid', krid);

                        $('#kt-portlet__create-job [name=bill_to]').append(op).trigger('change');
                    }


                }
                else{
                    /* Unselect billto */
                    $('#kt-portlet__create-job [name="bill_to"]').val(null).trigger('change.select2');
                }


            });


            $('#kt-portlet__create-job [name=bill_to]').on('change', function(){
                var self = $(this);
                var selected = self.find(':selected');
                var plate = self.val();
                // chances are, new tag is added
                if (typeof selected.attr('data-select2-tag') !== "undefined" && selected.attr('data-select2-tag') == 'true'){
                    /* push it to plate change queue so after new data renderd, it will change the plate */

                    /* we need to show form to create this record */
                    var btn = $('#kt-portlet__create-job [data-create-subclient]');
                    if(btn.length){
                        btn.eq(0).trigger('click');
                    }
                }
            });

            $('#kt-portlet__create-job [name=is_self]').on('change', function(){
                var self = $(this);
                var is_checked = self.is(':checked');
                if(is_checked){
                    /* disable the drive data, charge_to_driver & bill_to  */
                    $('#kt-portlet__create-job [name=driver_name], #kt-portlet__create-job [name=driver_phone], #kt-portlet__create-job [name=is_charge_to_driver], #kt-portlet__create-job [name=bill_to]')
                    .prop('disabled', true)
                    .parents('.job-header__item').removeClass('low-opacity').addClass('low-opacity');

                }else{
                    /* enable the driver data, charge_to_driver & bill_to  */
                    $('#kt-portlet__create-job [name=driver_name], #kt-portlet__create-job [name=driver_phone], #kt-portlet__create-job [name=is_charge_to_driver], #kt-portlet__create-job [name=bill_to]')
                    .prop('disabled', false)
                    .parents('.job-header__item').removeClass('low-opacity');
                }
            });


            $('#kt-portlet__create-job [name="is_discount"], #kt-portlet__create-job [name="is_tax"]').on('change', function(){
                var is_checked = $(this).is(':checked');

                /* update alieses */
                var name = $(this).attr('name');
                $('#kt-portlet__create-job [name="'+name+'"]').prop('checked', is_checked);

                if(is_checked){

                    $(this).parents('.job-content__item-selection').removeClass('disable_it')
                }
                else{
                    $(this).parents('.job-content__item-selection').removeClass('disable_it').addClass('disable_it');

                    /* make it 0 */
                    $(this).parents('.job-content__item-selection').find('input[type=number]').val(0);
                }

                /* call subtotal */
                JOB_MODULE.calculate_subtotal(true);

            });

            /* change date */
            $('#kt-portlet__create-job [name="date"]').on('change', function(){
                JOB_MODULE.events.onchange_netterms();
            });

            /* change status */
            $('#kt-portlet__create-job [name="status"]').on('change', function(){
                var val = $(this).val();

                /* show them by default */
                $('#kt-portlet__create-job .btnform.btnform--onhold').show();
                $('#kt-portlet__create-job .btnform.btnform--unhold').hide();


                /* clear the page if only job id found */
                if($('#kt-portlet__create-job [name=job_id]').length){
                    /* edit page */

                    var job = JOB_MODULE.loaded_job;

                    $('#kt-portlet__create-job .btnform.btnform--print').show();

                    $('#kt-portlet__create-job .btnform--bikeout.btnform--bikeoutinner').text('Bike out');

                    if(val=="complete"){
                        /* completed */
                        $('#kt-portlet__create-job .jobstatus-badge')
                        .removeClass('jobstatus-badge--progress').removeClass('jobstatus-badge--success').removeClass('jobstatus-badge--danger')
                        .addClass('jobstatus-badge--success');

                        /* show complete label on in_progress state */

                        $('#kt-portlet__create-job .btnform--bikeout').show();
                        $('#kt-portlet__create-job .btnform--bikeout.btnform--bikeoutinner').text('Save');


                        $('#kt-portlet__create-job form [type="submit"]').hide();
                    }
                    else if(val=="on_hold"){
                        /* on hold */
                        $('#kt-portlet__create-job .jobstatus-badge')
                        .removeClass('jobstatus-badge--progress').removeClass('jobstatus-badge--success').removeClass('jobstatus-badge--danger')
                        .addClass('jobstatus-badge--danger');

                        /* hides complete label on in_progress state */
                        $('#kt-portlet__create-job .btnform--bikeout, #kt-portlet__create-job .btnform.btnform--unhold').show();
                        $('#kt-portlet__create-job .btnform.btnform--onhold, #kt-portlet__create-job form [type="submit"], #kt-portlet__create-job .btnform--bikeout.btnform--bikeoutouter').hide();
                    }
                    else {
                        /* in progress */
                        $('#kt-portlet__create-job .jobstatus-badge')
                        .removeClass('jobstatus-badge--progress').removeClass('jobstatus-badge--success').removeClass('jobstatus-badge--danger')
                        .addClass('jobstatus-badge--progress');

                        /* hides complete label on in_progress state */
                        $('#kt-portlet__create-job .btnform--bikeout, #kt-portlet__create-job form [type="submit"]').show();
                        $('#kt-portlet__create-job .btnform--bikeout.btnform--bikeoutouter').hide();

                    }
                }
                else{
                    /* add page */

                    /* mark as in_progress */
                    $('#kt-portlet__create-job .jobstatus-badge')
                    .removeClass('jobstatus-badge--progress').removeClass('jobstatus-badge--success').removeClass('jobstatus-badge--danger')
                    .addClass('jobstatus-badge--progress');

                    $('#kt-portlet__create-job .btnform--bikeout, #kt-portlet__create-job .btnform.btnform--print, #kt-portlet__create-job .btnform.btnform--onhold').hide();
                    $('#kt-portlet__create-job form [type="submit"]').show();
                }
            });

            /* do some validation (Save Progress) */
            $('#kt-portlet__create-job form :submit').on('click', function(e){
                e.preventDefault();

                JOB_MODULE.save_progress();

            });


            $('#kt-portlet__create-job .datatable').on('keypress','input, textarea', function(e){
                if(e.keyCode === 13){
                    /* Enter key is pressed */
                    e.preventDefault(); // Ensure it is only this code that runs

                    /* --------------------------- */
                    /*      BARCODE-DETECTION      */
                    /* --------------------------- */
                    var input = this;
                    var inputVal = input.value;
                    inputVal && (inputVal = inputVal.trim());
                    /*
                        Strip "-1" at the end of input
                        because thier barcode scanner append "-1" in the end.
                        Reason not confirmed - Checked barcode setting via teamviewer for any suffix but no luck
                    */
                    var inputValStripped = inputVal.replace(/-1$/g, '');

                    /* Search in typeahead */
                    setTimeout(function(){
                        JOB_MODULE.typeahead.bloodhoundEngine.search(inputValStripped, function(datums) {

                            /* Process search returned and autocomplete the input */
                            if(datums.length === 1){
                                /* Autocomplete this */
                                var datum = datums[0];
                                input.value = datum.text;
                                JOB_MODULE.typeahead.make_selection(datum, 'select', input);
                            }
                            else if(datums.length === 0){
                                /* Maybe search wihout stripping? */
                                JOB_MODULE.typeahead.bloodhoundEngine.search(inputVal, function(datums) {

                                    /* Process search returned and autocomplete the input */
                                    if(datums.length === 1){
                                        /* Autocomplete this */
                                        var datum = datums[0];
                                        input.value = datum.text;
                                        JOB_MODULE.typeahead.make_selection(datum, 'select', input);
                                    }
                                });

                            }
                        });
                    }, 100); // Making the search process aync


                    /* append a blank row */
                    JOB_MODULE.append_row(null,$(this).parents('tr'));

                    /* recalculate data (values zero) */
                    JOB_MODULE.calculate_subtotal();

                    /* focus on 1st elem on new row */
                    $(this).parents('tr').next().find('textarea').focus();
                }
            });

            $('#kt-portlet__create-job .datatable').on('input','input[data-name="qty"]', function(e){

                var value=parseInt(this.value)||0;

                if(this.hasAttribute('qty-left')){
                    var left = parseInt(this.getAttribute('qty-left'))||0;
                    var current_part = $(this).parents('tr').find('[data-name="part_id"]');

                    /* find already added service with same part, and deduct the left qty accordingly */
                    var part_elems = $('#kt-portlet__create-job .datatable [data-name="part_id"][value="'+current_part.val()+'"]');
                    part_elems.each(function(){
                        if(!$(this).is(current_part[0])){
                            var _qty = parseInt($(this).parents('tr').find('[data-name="qty"]').val())||0;
                            left-=_qty;
                        }

                    });
                    if(value>left){
                        /* not much is left, restrict it */
                        this.value=left;
                    }
                }

                if(value<0){
                    /* cannot be negetive */
                    this.value=0;

                }

                /* recalculate subtotal */
                JOB_MODULE.calculate_subtotal(true);
            });

            $('#kt-portlet__create-job .datatable').on('keydown','input, textarea', function(e){

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
                    JOB_MODULE.delete_row(deleteBtn);

                    /* focus on 1st elem on new row */
                    if(nextRow.length) nextRow.find('textarea').focus();
                    else $('#kt-portlet__create-job .datatable tbody tr:last-child textarea').focus();


                }
            });

            /* Event to add amount to rider account */
            $('#kt-portlet__create-job .datatable').on('change','[name="do_rideraccount"]', function(e){
                var self = this;
                var charge = self.checked;

                /* If some edit is performed, we need to save the job first */
                if(JOB_MODULE.Utils.detect_change.check()){
                    /* Undo the check state */
                    if(charge)self.checked = false;
                    else self.checked = true;

                    swal.fire({
                        position: 'center',
                        type: 'error',
                        title: 'Changes detected!',
                        html: 'It seems invoice has some changes, save them first to charge the rider',
                    });
                    return;
                }

                /* If job is not saved already, we are not doing it */
                var jobitem_id = self.hasAttribute('data-id') ? self.getAttribute('data-id') : null;
                if(!jobitem_id)return;

                /* show laoding */
                self.disabled = true;

                /* Send ajax */
                var url = "{{ route('tenant.admin.jobs.charge_rider_account') }}";
                $.ajax({
                    url : url,
                    headers:{'X-NOFETCH':''}, /* don't allow fetch accounts */
                    type : 'POST',
                    data:{
                        jobitem_id:jobitem_id,
                        charge:charge?1:0
                    },
                    complete: function(){
                        /* hide laoding */
                        self.disabled = false;
                    }
                })
                .done(function(response){
                    var ref = parseInt(response)||null;
                    /* Operation is done, if checked, hide the delete button */
                    var hasRef = $('#kt-portlet__create-job table.datatable [name="do_rideraccount"]:checked').length>0;
                    if(hasRef)$(self).parents('.datatable').find('.btndelete').hide();
                    else $(self).parents('.datatable').find('.btndelete').show();

                    /* Invalidate the job */

                    if(JOB_MODULE.loaded_job && typeof JOBS !== "undefined" && JOBS){
                        /* Invalidate the datables row */

                        let loaded_job = JOB_MODULE.loaded_job;

                        /* Update the ref_id */
                        let serviceIndex = loaded_job.services.findIndex(function(x){ return x._id==jobitem_id; });
                        if(serviceIndex>-1)loaded_job.services[serviceIndex].ds_ref = ref;

                        /* Find row node */
                        var rowNode = JOBS.datatable.row(function(x, job){return job._id==loaded_job._id}).node();
                        if(rowNode){
                            JOBS.datatable.row(rowNode).data(loaded_job).invalidate();
                        }

                        console.log('Update Job: ', loaded_job);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {

                    /* this will handle & show errors */
                    var is_permission_err = false;
                    if(jqXHR.status == 403){
                        /* we should show alerts on preload ajax requests */
                        is_permission_err = true;
                    }

                    kingriders.Plugins.KR_AJAX.showErrors(jqXHR, is_permission_err);

                    /* Undo the check state */
                    if(charge)self.checked = false;
                    else self.checked = true;
                });
            });


            /*
            |---------------------------------
            |         CUSTOM CODE
            |---------------------------------
            */

            /* preload the kr-ajax module (only if laoded in modal) */
            var MODAL = $('#kt-portlet__create-job').parents('.modal');
            if(MODAL.length){
                setTimeout(function(){
                    $('#kt-portlet__create-job '+kingriders.Plugins.Selectors.kr_ajax_preload).each(function(i, elem){
                        /* initiate the ajax */
                        $(this).trigger('click.krevent', {
                            preload:true
                        });
                    });
                },100);
            }
            $('#kt-portlet__create-job [data-paymentpopover]').popover({
                container: '#kt-portlet__create-job',
                placement:'left',
                html:true,
            });

            /* This closes all popovers if you click anywhere except on a popover */
            $("#kt-portlet__create-job").on("mouseup", function (e) {
                var l = $(e.target);
                if (l.parents('.popover').length==0) {
                    $('#kt-portlet__create-job [data-paymentpopover]').popover("hide");
                }
            });



            var isEditPage = false;
            /* Check if page has config, do accordingly */
            @isset($config)
            /* This will help us in loading page as edit & view */
            @isset($config->job)
            var _JobLoaded = {!! $config->job !!};
            JOB_MODULE.Utils.load_job(_JobLoaded);
            isEditPage = true;
            @endisset

            @if($config->action=='view')
                /* Disable the page */
                $('#kt-portlet__create-job input, #kt-portlet__create-job textarea, #kt-portlet__create-job button:not(.close), #kt-portlet__create-job a.btn').prop('disabled', true);
                $(' #kt-portlet__create-job select ').prop('disabled', true).trigger('change.select2');

                /* Remove html of buttons */
                $('#kt-portlet__create-job [kr-ajax-inner-footer] button, #kt-portlet__create-job [kr-ajax-inner-footer] a.btn, #kt-portlet__create-job .btnReceivePayment').remove();
            @endif
            @endisset

            if(typeof KINGVIEW !== "undefined" && !isEditPage){
                /* Seems page was loaded in OnAir, reset page */
                $('#kt-portlet__create-job form').attr('action', $('#kt-portlet__create-job form').attr('data-add'));
                JOB_MODULE.Utils.reset_page();
            }

        });
    </script>
@endsection
