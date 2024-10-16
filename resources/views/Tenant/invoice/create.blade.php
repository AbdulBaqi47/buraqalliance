@extends('Tenant.layouts.app')

@section('page_title')
    Create Invoice
@endsection
@section('head')

    <style kr-ajax-head>
        .job-content__item-container{
            min-height: 200px;
        }
        #kt-portlet__create-invoice table.datatable thead{
            background: #f9f9f9;
        }
        #kt-portlet__create-invoice table.datatable thead th:nth-of-type(2) {
            width:50%;
        }
        #kt-portlet__create-invoice table.datatable thead th:nth-of-type(3) {
            width:7%;
        }
        #kt-portlet__create-invoice table.datatable thead th:nth-of-type(4) {
            width:7%;
        }
        #kt-portlet__create-invoice table.datatable thead th:nth-of-type(5) {
            width:8%;
        }
        #kt-portlet__create-invoice table.datatable thead th:nth-of-type(6) {
            width:13%;
        }
        #kt-portlet__create-invoice table.datatable thead th:nth-of-type(7) {
            width:10%;
        }
        #kt-portlet__create-invoice table.datatable thead th:nth-of-type(8) {
            width:5%;
        }
        #kt-portlet__create-invoice table.datatable tbody tr td {
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

        #kt-portlet__create-invoice table.datatable thead th{
            padding: 5px 8px;
            font-weight: bold;
        }
        #kt-portlet__create-invoice table.datatable tbody td{
            padding: 5px 5px;
        }
        #kt-portlet__create-invoice .job-content input,
        #kt-portlet__create-invoice .job-content table.datatable tbody textarea{
            padding: 4px 7px;
            height: auto;
        }
        #kt-portlet__create-invoice table.datatable tbody .btndelete{
            height: 1.5rem;
            width: 1.5rem;
        }
        #kt-portlet__create-invoice .kt-portlet__head{
            min-height: 45px;
        }
        .low-opacity{
            opacity: .4;
        }
        .low-opacity input,
        .low-opacity input::placeholder {
            color: transparent;
        }

        #kt-portlet__create-invoice .jobstatus-badge{
            transition: none;
        }

        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--progress {
            color: #ffb822;
        }
        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--progress .kt-badge{
            background-color: #ffb822;
        }
        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--progress::after{
            content: "In Progress";
            font-size: 16px;
        }

        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--success {
            color: #1dc9b7;
        }
        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--success .kt-badge{
            background-color: #1dc9b7;
        }
        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--success::after{
            content: "Completed";
            font-size: 16px;
        }

        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--danger {
            color: #fd397a;
        }
        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--danger .kt-badge{
            background-color: #fd397a;
        }
        #kt-portlet__create-invoice .jobstatus-badge.jobstatus-badge--danger::after{
            content: "On Hold";
            font-size: 16px;
        }
        #kt-portlet__create-invoice .btnform:hover,
        #kt-portlet__create-invoice .btnform:focus,
        #kt-portlet__create-invoice .btn-printsampleinvoice:hover,
        #kt-portlet__create-invoice .btn-printsampleinvoice:focus{
            background-color: initial !important;
        }

        .kr-page__add .job-content__bike-times{
            display: none;
        }
        .kr-page__add .btnform{
            display: none;
        }


        #kt-portlet__create-invoice .typeahead .tt-menu .typeahead-table__head{
            color: #000000;
            background: #eee;
            border-color: #000;
            border-spacing: 0;
            margin: 0;
            border-bottom: 2px solid #999;
        }
        #kt-portlet__create-invoice .typeahead .tt-menu .typeahead-table__head tr th{
            padding:0 3px;
            border: 1px solid #ccc;
            font-weight: 500;
        }
        #kt-portlet__create-invoice .typeahead .tt-menu .typeahead-table__body tr td{
            border: 1px solid #ccc;
            border-bottom: none;
            padding:0 3px;
        }
        #kt-portlet__create-invoice .typeahead .tt-menu .typeahead-table__body:last-child{
            border-bottom: 1px solid #ccc;
        }
        #kt-portlet__create-invoice .typeahead .tt-menu table tr > :nth-child(1) {
            width: 110px;
        }
        #kt-portlet__create-invoice .typeahead .tt-menu table tr > :nth-child(2) {
            width: 395px;
        }
        #kt-portlet__create-invoice .typeahead .tt-menu table tr > :nth-child(3) {
            min-width: 76px;
        }

        #kt-portlet__create-invoice .job-content__item-discount-selection {
            width:60%;
        }
        #kt-portlet__create-invoice .job-content__item-tax-selection{
            width:40%;
        }

        #kt-portlet__create-invoice .job-content__item-tax-selection.disable_it [type="number"] {
            opacity: .6;
            pointer-events: none;
            background: #eee;
        }
        #kt-portlet__create-invoice .payments--wrapper {
            width: 130px;
            font-size: 10px;
        }
        #kt-portlet__create-invoice .payments--wrapper input,
        #kt-portlet__create-invoice .payments--wrapper .btn{
            font-size: 10px;
        }
        #kt-portlet__create-invoice .payments--wrapper .btn:hover {
            color: #fff !important;
        }
        #kt-portlet__create-invoice [data-paymentpopover]{
            cursor: pointer;
            font-size: 14px;
        }

        #kt-portlet__create-invoice .balance_due--wrapper h3 {
            font-size: 14px;
            margin: 0;
            font-weight: 400;
        }
        #kt-portlet__create-invoice .balance_due--wrapper > span {
            font-size: 30px;
            color: #08976d;
            font-weight: 500;
            letter-spacing: 1px;
        }
        #kt-portlet__create-invoice .kr-bootstrapselect .bootstrap-selector{
            border:1px solid #e2e5ec !important;
            border-radius:0 !important;
        }

    </style>
@endsection
@section('content')

@if($errors->any())

    <div class="alert alert-danger mb-2" role="alert">
        <div class="alert-text">
            <h4 class="alert-heading">Got Issues!</h4>
            <ul>
                @foreach ($errors->all() as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>

        </div>
    </div>

@endif

<!--begin::Portlet-->
<div class="kt-portlet m-0 pb-2" id="kt-portlet__create-invoice" kr-ajax-content>
    <form class="kt-form" data-add="{{route('tenant.admin.invoices.create')}}" data-edit="{{route('tenant.admin.invoices.edit')}}" action="{{route('tenant.admin.invoices.create')}}" method="POST">
        @csrf
        <div class="kt-portlet__head d-block d-sm-flex" kr-ajax-inner-header>
            <div class="kt-portlet__head-label my-2 my-sm-0">
                <h3 class="kt-portlet__head-title">Create Invoice</h3>
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

            <div class="row">
                <div class="col-md-8">
                    <div class="job-header row">
                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <label>Client: <span class="text-danger">*<span></label>
                            <select name="client_id" data-placeholder="Select Client" required class="form-control kr-select2 @error('client_id') is-invalid @enderror">
                                <option></option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="job-header__item col-md-8 mb-3 mb-md-0">
                            <label> Reference: <span class="text-danger">*<span></label>
                            <select name="payment_ref_ids[]" onchange="INVOICE_MODULE.calculate_subtotal(true);" multiple data-placeholder="Select payment references" class="form-control kr-select2">
                                <option></option>
                                @foreach ($payment_refs as $item)
                                    <option value="{{$item->id}}" data-amount="{{ $item->amount }}">{{$item->title}}</option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                            
                    <div class="row mt-2">

                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="mb-1">Month: <span class="text-danger">*<span></div>
                            <input type="text" data-default="" required readonly name="month" data-state="month" class="rounded-0 kr-datepicker form-control @error('month') is-invalid @enderror" value="{{old('month')}}">

                        </div>
                        
                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="mb-1">Invoice Date: <span class="text-danger">*<span></div>
                            <input type="text" data-default="" required readonly name="date" data-state="date" class="rounded-0 kr-datepicker form-control @error('date') is-invalid @enderror" value="{{old('date')}}">

                        </div>
                        <div class="job-header__item col-md-4 mb-3 mb-md-0">
                            <div class="mb-1">Due Date: <span class="text-danger">*<span></div>
                            <input type="text" data-default="" required readonly name="due_date" data-state="date" class="rounded-0 kr-datepicker form-control @error('due_date') is-invalid @enderror" value="{{old('due_date')}}">

                        </div>

                    </div>

                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body p-0">
                                    <mark class="card-title text-center text-dark kt-heading kt-heading--thin m-0 mb-2 d-block">Related Invoices</mark>

                                    <div class="table-responsive">
                                        <table class="table table-sm m-0 table-relatedinvoices">
                                            <thead>
                                                <tr>
                                                    <th class="p-0 py-1 pl-1">Invoice #</th>
                                                    <th class="p-0 py-1 pl-1">Client</th>
                                                    <th class="p-0 py-1 pl-1">Month</th>
                                                    <th class="p-0 py-1 pl-1">Amount</th>
                                                    <th class="p-0 py-1 pl-1">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
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
                                    <th>Subtotal</th>
                                    <th>Tax</th>
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
                        <button type="button" class="btn btn-outline-info btn-elevate btn-square btn-sm btnadd_row" onclick="INVOICE_MODULE.append_row();INVOICE_MODULE.calculate_subtotal();">
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
                                    <div class="job-content__item-selection job-content__item-discount-selection">
                                        <span class="m-0 mr-2 text-muted d-flex d-lg-none align-items-center mb-1">
                                            Discount:
                                        </span>
                                        <div class="input-group">
                                            <span class="m-0 mr-2 text-muted d-none d-lg-flex align-items-center">
                                                Discount:
                                            </span>
                                            <input type="number" oninput="INVOICE_MODULE.calculate_subtotal(true);" class="form-control rounded-0" placeholder="0" name="discount_value">
                                            <div class="input-group-append">
                                                <select onchange="INVOICE_MODULE.calculate_subtotal();" class="form-control p-0 rounded-0 h-auto" name="discount_type">
                                                    <option value="percentage">%</option>
                                                    <option value="fixed">AED</option>
                                                </select>
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
                                            Distribution
                                        -----------------------------}}
                                        <div class="d-flex justify-content-between small text-warning job-content__item-paymentrefamount-wrapper">
                                            <div>
                                                <span class="m-0">To be distributed</span>
                                            </div>
                                            <span class="m-0">
                                                <input type="hidden" name="payment_ref_amount">
                                                <span class="job-content__item-paymentrefamount-text">0.00</span>
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
                                            Balance Due
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
                
                <div class="w-50">
                    <div class="alert alert-solid-danger error-alert m-0 mt-2 mb-2 rounded-0 border-danger alert-bold" role="alert" style="display: none;">
                        <div class="alert-text"></div>
                    </div>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-brand btn-wide rounded-0 px-4" data-textadd="Generate Invoice" data-textedit="Update Invoice">Generate Invoice</button>
                    <div class="d-flex flex-column justify-content-center">
                        <button type="button" class="btn-link btn btn-square py-0 text-info btnform btnform--print" onclick="INVOICE_MODULE.invoice.handleclick(this);return false;">
                            Print invoice
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
    @include('Tenant.invoice.handlebars_templates.add_row')

    {{-- INVOICE TEMPLATE --}}
    @include('Tenant.invoice.handlebars_templates.print_invoice')
    @include('Tenant.invoice.handlebars_templates.print_sampleinvoice')

    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">

        var MILEAGE_THRESHOLD = 2000;

        var INVOICE_MODULE = function(){
            var CONTAINER = '#kt-portlet__create-invoice';

            var table = $(CONTAINER + ' .datatable');

            var calculate_subtotal = function(){
                /* variables to store*/
                var subtotal=0,
                    total=0,
                    tax_amount=0,
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
                    var tax_value = parseFloat($(this).find('[data-name="tax_value"]').val())||0;

                    var item_subtotal = rate * qty;
                    var item_tax_amount = (tax_value * item_subtotal) / 100;
                    var item_total = item_subtotal + item_tax_amount;

                    /* Updating inner DOM for each row */
                    $(this).find('.job-items__subtotal').text(item_subtotal.toFixed(2));
                    $(this).find('.job-items__total').text(item_total.toFixed(2));

                    /* adding amount to subtotal, for calculating final amount */
                    subtotal+=item_subtotal;
                    tax_amount+=item_tax_amount;
                });

                total=subtotal;

                /* finding DISCOUNT (if any) */
                var discount_type = $(INVOICE_MODULE.container + ' [name=discount_type]').val();
                var discount_value = parseFloat($(INVOICE_MODULE.container + ' [name=discount_value]').val()) || 0;
                var discount_amount = 0;
                if(discount_value>0){
                    if (discount_type == 'percentage') discount_amount = (discount_value * subtotal) / 100;
                    else discount_amount = discount_value;
                }

                /* subtracting discount amount from total*/
                total-=discount_amount;

                /* adding tax amount to total*/
                total+=tax_amount;

                // Find payment ref amounts
                var paymentRefSum = $(INVOICE_MODULE.container + ' [name="payment_ref_ids[]"] option:selected')
                // Fetch amount of entry
                .map((i, op) => parseFloat(op.getAttribute('data-amount')) || 0)
                .toArray()
                // Sum the amount
                .reduce((sum, item) => sum + item, 0);

                paymentRefSum -= total;

                /* Finding paid amount */
                var total_paid = 0;
                var paymentsObj = INVOICE_MODULE.invoice.getPayments();
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
                $(INVOICE_MODULE.container + ' .job-content__item-subtotal-text').text(subtotal.toFixed(2));
                $(INVOICE_MODULE.container + ' [name=subtotal]').val(subtotal.toRound(2));
                /* DISCOUNT */
                $(INVOICE_MODULE.container + ' .discount_amount_text').text(discount_amount.toFixed(2));
                $(INVOICE_MODULE.container + ' [name=discount_amount]').val(discount_amount.toRound(2));

                /* TAX */
                $(INVOICE_MODULE.container + ' .job-content__item-tax-amount-label').text(tax_amount.toFixed(2));
                $(INVOICE_MODULE.container + ' [name=tax_amount]').val(tax_amount.toRound(2));

                /* TOTAL */
                $(INVOICE_MODULE.container + ' .job-content__item-total-text').text(total.toFixed(2));
                $(INVOICE_MODULE.container + ' [name=total]').val(total.toRound(2)).trigger('change');

                /* PaymentRef (To be Distributed) */
                $(INVOICE_MODULE.container + ' .job-content__item-paymentrefamount-text').text(paymentRefSum.toFixed(2));
                $(INVOICE_MODULE.container + ' .job-content__item-paymentrefamount-wrapper')
                .removeClass('text-warning')
                .removeClass('text-success')
                .addClass(paymentRefSum.toRound(2) === 0 ? 'text-success' : 'text-warning' );

                /* AMOUNT PAID */
                $(INVOICE_MODULE.container + ' .job-content__item-amount-paid-text').text(total_paid.toFixed(2));
                $(INVOICE_MODULE.container + ' [name=amount_paid]').val(total_paid.toRound(2));


                /* BALANCE DUE */
                $(INVOICE_MODULE.container + ' .job-content__item-balance-text').text(balance_due.toFixed(2));
                $(INVOICE_MODULE.container + ' [name=balance_due]').val(balance_due.toRound(2));


                if(balance_due==0){
                    /* Invoice is paid */
                    $(INVOICE_MODULE.container + ' .btnReceivePayment').hide();

                }

                /* CHeck if invoice is manually paid, need to disable the page */
                if(INVOICE_MODULE.loaded_data && INVOICE_MODULE.loaded_data.manually_paid === true){
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
                        $(this).attr('name', 'items['+rowIndex+']['+name+']');
                    });

                    /* update SR # */
                    $(this).find('.srno').text(rowIndex+1);
                });
            }

            var DISABLEPAGE = function(){
                /* Disable the page */
                $(INVOICE_MODULE.container + ' input, #kt-portlet__create-invoice textarea, #kt-portlet__create-invoice button:not(.close), #kt-portlet__create-invoice a.btn').prop('disabled', true);
            }
            var ENABLEPAGE = function(){
                /* Enable the page */
                $(INVOICE_MODULE.container + ' input, #kt-portlet__create-invoice textarea, #kt-portlet__create-invoice button:not(.close), #kt-portlet__create-invoice a.btn').prop('disabled', false);
            }

            return {
                container: CONTAINER,
                loaded_data:null,
                calculate_subtotal:function(is_minimal=false){
                    /* clear the errors */
                    INVOICE_MODULE.errors.clear();

                    if(!is_minimal) update_indices();

                    /* calculate the amount through each loop*/
                    calculate_subtotal();

                },
                Utils:{
                    related_invoices:{
                        fetching:false, // When true, means system is currently fetching the invoices
                        data:null // latest data
                    },

                    getRecentInvoices:function(){
                        /* Will fetch recent invoices against this bike */
                        var that = this;

                        return new Promise(function(resolve, reject){

                            var client_id = $(INVOICE_MODULE.container + ' [name=client_id]').val();

                            if(!client_id)return;

                            var payload={};

                            /* check if any job is loaded */
                            if(typeof INVOICE_MODULE.loaded_data !== "undefined" && INVOICE_MODULE.loaded_data){
                                /* Pass the job_id into the payload */
                                payload.invoice_id = INVOICE_MODULE.loaded_data.id;
                            }

                            var url = "{{ route('tenant.admin.invoices.related', '_:param') }}".replace('_:param', client_id);
                            $.ajax({
                                url : url,
                                headers:{'X-NOFETCH':''}, /* don't allow fetch accounts */
                                type : 'GET',
                                data:payload,
                                beforeSend: function() {
                                    that.related_invoices.fetching = true;
                                },
                                complete: function(){
                                    that.related_invoices.fetching = false;
                                }
                            })
                            .done(function(invoices){
                                that.related_invoices.data = invoices;

                                // Render HTML
                                var table = $('.table-relatedinvoices');
                                var rows = '';
                                if(invoices.length > 0){
                                    invoices.forEach(function(item){
                                        var month = moment(item.month).format("MMM YYYY");
                                        rows += `
                                        <tr>
                                            <td>#${item.id}</td>
                                            <td>${item.client?.name}</td>
                                            <td>${month}</td>
                                            <td>${item.total}</td>
                                            <td>
                                                <a target="_blank" href="${`{{route('tenant.admin.invoices.single.edit', "_:param")}}`.replace('_:param', item.id)}" >View <i class="fa fa-external-link-alt"></i></a>
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
                    },

                    reset_page:function(force=false){
                        /* clear the page if only job id found */
                        if($(INVOICE_MODULE.container + ' [name=invoice_id]').length || force){
                            $(INVOICE_MODULE.container + ' form [name=invoice_id]').remove();

                            /* clear the items */
                            $(INVOICE_MODULE.container + ' .datatable tbody tr').remove();
                            $(INVOICE_MODULE.container + ' [name="client_id"]').val(null).trigger('change.select2');
                            $(INVOICE_MODULE.container + ' [name="payment_ref_ids[]"]').val(null).trigger('change.select2');
                            $(INVOICE_MODULE.container + ' [name="date"]').val(null);
                            $(INVOICE_MODULE.container + ' [name="due_date"]').val(null);
                            $(INVOICE_MODULE.container + ' [name="invoice_notes"]').val(null);
                            $(INVOICE_MODULE.container + ' [name="internal_notes"]').val(null);

                            $(INVOICE_MODULE.container + ' [name="discount_value"]').val(null);
                            $(INVOICE_MODULE.container + ' [name="discount_type"]').val('percentage');

                            $(INVOICE_MODULE.container + ' .job-content__item-discount-selection').addClass('disable_it');

                            $(INVOICE_MODULE.container + ' [name="is_tax"]').prop('checked', false);
                            $(INVOICE_MODULE.container + ' .job-content__item-tax-selection').addClass('disable_it');

                            /* Empty loaded job */
                            INVOICE_MODULE.loaded_data=null;

                            /* append blacnk row */
                            INVOICE_MODULE.append_row();

                            /* recalculate data (values zero) */
                            INVOICE_MODULE.calculate_subtotal();

                        }

                        /* Empty mileage warnings */
                        $('.alert.warnings_caontainer').removeClass('show');

                        /* Remove do_bikeout */
                        $(INVOICE_MODULE.container + ' form').find('[name=do_bikeout]').remove();

                        /* reset invoice number from label */
                        $(INVOICE_MODULE.container + ' .kt-portlet__head-title').text("Create Invoice");

                        $(INVOICE_MODULE.container)
                        .removeClass('kr-page__edit').removeClass('kr-page__add')
                        .addClass('kr-page__add');

                        /* Hide payments */
                        $(INVOICE_MODULE.container + ' .payments--belongs').css('cssText', 'display: none !important;');

                        /* remove payments */
                        $(INVOICE_MODULE.container + ' .payment_records').attr('data-content', '');

                        /* Reset texts */
                        $(INVOICE_MODULE.container + ' [data-textadd]').text(function(){
                            var text = $(this).attr('data-textadd');
                            $(this).text(text);
                        });

                        $(INVOICE_MODULE.container + ' .btn-printsampleinvoice').show();

                        /* clear constant data (helps in detect change) */
                        setTimeout(function(){
                            /* after half sec, so if modal is loading, it will complete */
                            INVOICE_MODULE.Utils.detect_change.data=kingriders.Utils.formdata_to_object(new FormData($(INVOICE_MODULE.Utils.detect_change.form)[0]));
                        }, 300);
                    },
                    load_data:function(modal){
                        console.log(modal)

                        /* Load the job in page (this funtion is using in view job page) */

                        /* remove all listening */
                        INVOICE_MODULE.Utils.detect_change.remove_listening();

                        /* reset invoice number from label */
                        $(INVOICE_MODULE.container + ' .kt-portlet__head-title').text("Create Invoice");

                        /* need to check if job is suitable for edit, (not in creating process) */
                        if(modal.actions.status!=0){

                            /* update invoice number from label */
                            $(INVOICE_MODULE.container + ' .kt-portlet__head-title').text("Invoice "+modal.display_name);

                            /* loaded job data */
                            modal.actions.status=1;
                            INVOICE_MODULE.loaded_data=modal;

                            /* Reset texts */
                            $(INVOICE_MODULE.container + ' [data-textedit]').text(function(){
                                var text = $(this).attr('data-textedit');
                                $(this).text(text);
                            });

                            /* change the action of form to edit */
                            $(INVOICE_MODULE.container + ' form [name=invoice_id]').remove();
                            $(INVOICE_MODULE.container + ' form').attr('action', $(INVOICE_MODULE.container + ' form').attr('data-edit'))
                            .prepend('<input type="hidden" name="invoice_id" value="'+modal.id+'" />');

                            /* load the data */
                            var items = modal.items;

                            /* clear the items first */
                            $(INVOICE_MODULE.container + ' .datatable tbody tr').remove();

                            /* append job items */
                            items.forEach(function(item,index) {
                                var obj={
                                    id:item._id,
                                    description:item.description,
                                    rate:item.rate,
                                    qty:item.qty,
                                    total:item.total,
                                    tax_value:item.tax_value,
                                };
                                INVOICE_MODULE.append_row(obj);
                            });

                            $(INVOICE_MODULE.container + ' [name="client_id"]').val(modal.client.id).trigger('change.select2');

                            /* Update recent invoices */
                            INVOICE_MODULE.Utils.getRecentInvoices();

                            /* load other data */

                            var date = new Date(modal.date).format('mmmm dd, yyyy');
                            var due_date = new Date(modal.due_date).format('mmmm dd, yyyy');
                            $(INVOICE_MODULE.container + ' [name="date"]').attr('data-default', date).datepicker('update', date);
                            $(INVOICE_MODULE.container + ' [name="due_date"]').attr('data-default', due_date).datepicker('update', due_date);
                            $(INVOICE_MODULE.container + ' [name="month"]').attr('data-default', modal.month).datepicker('update', modal.month);

                            $(INVOICE_MODULE.container + ' [name="payment_ref_ids[]"]').val(modal.transaction_ledger_ids).trigger('change');

                            $(INVOICE_MODULE.container + ' [name="discount_value"]').val(modal.discount_value);
                            $(INVOICE_MODULE.container + ' [name="discount_type"]').val(modal.discount_type);

                            $(INVOICE_MODULE.container + ' [name="invoice_notes"]').val(modal.invoice_notes);
                            $(INVOICE_MODULE.container + ' [name="internal_notes"]').val(modal.internal_notes);

                            /* Hide payments */
                            $(INVOICE_MODULE.container + ' .payments--belongs').css('cssText', 'display: none !important;');
                            /* remove payments */
                            $(INVOICE_MODULE.container + ' .payment_records').attr('data-content', '');

                            if(!!modal.payment_refs && modal.payment_refs.length > 0){
                                /* Seems invoice for this job is created, we need to show payments */

                                /* Show payments */
                                $(INVOICE_MODULE.container + ' .payments--belongs').show();

                                /* append payments */
                                var paymentHtml = INVOICE_MODULE.payments.generateHtml();

                                $(INVOICE_MODULE.container + ' .payment_records').attr('data-content', paymentHtml);
                            }

                            /* call the plugin of autosize */
                            autosize($(INVOICE_MODULE.container + ' textarea'));

                            /* Update notes autosize */
                            autosize.update($(INVOICE_MODULE.container + ' [name="invoice_notes"], ' + INVOICE_MODULE.container + ' [name="internal_notes"]'));

                            /* recalculate the subtotal */
                            INVOICE_MODULE.calculate_subtotal();

                            /* mark this data as constant data (helps in detect change) */
                            var compare_data = {
                                status:modal.status,
                                client_id:modal.client_id,
                                date:modal.date,
                                due_date:modal.due_date,
                                items:modal.items.map(function(x){return {description:x.description,rate:x.rate,qty:x.qty,tax_value:x.tax_value}}),
                                subtotal:parseFloat(modal.subtotal)||0,
                                discount_value:parseFloat(modal.discount_value)||0,
                                discount_type:modal.discount_type,
                                discount_amount:parseFloat(modal.discount_amount)||0,
                                total:parseFloat(modal.total)||0,
                                invoice_notes:modal.invoice_notes,
                                internal_notes:modal.internal_notes,
                            };

                            INVOICE_MODULE.Utils.detect_change.data=compare_data;

                            /* listen for change */
                            INVOICE_MODULE.Utils.detect_change.listen();
                        }
                        else{
                            /* cannot laod the data now */
                            swal.fire({
                                position: 'center',
                                type: 'error',
                                title: 'Cannot load invoice',
                                html: 'Job is processing.. Please retry after some time',
                            });
                        }
                        kingriders.Utils.isDebug() && console.log('loaded_data', modal);
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

                            // var current_status = $(INVOICE_MODULE.container + ' form [name="status"]:checked').val();
                            // if(current_status!="in_progress"){
                            //     /* Need to show Save/Bike out button */

                            // }
                            // else{
                            //     /* status is in progress, we need to show 'Save' button accordingly */
                            //     $(INVOICE_MODULE.container + ' form [type="submit"]').show();

                            // }

                            var inputStatus=$(INVOICE_MODULE.container + ' .job_status-wrapper [name=status]:checked');
                            inputStatus.trigger('change');
                        }


                        return {
                            data:null,
                            form:CONTAINER + ' form',
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

                    var index = $(INVOICE_MODULE.container + ' .datatable tbody tr').length;
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
                    else $(INVOICE_MODULE.container + ' .datatable tbody').append(html);


                    if(!item){
                        /* call the plugin of autosize */
                        autosize($(INVOICE_MODULE.container + ' textarea'));

                    }

                    // [type=number] to [type=text] for additional validations
                    kingriders.Plugins.refresh_numbertotext();


                },
                delete_row:function(self){
                    $(self).parents('tr').remove();

                    /* check if no rows present */
                    if($(INVOICE_MODULE.container + ' .datatable tbody tr').length==0){
                        /* append a blank row */
                        INVOICE_MODULE.append_row();
                    }

                    this.calculate_subtotal();

                },
                errors:{
                    clear:function(){
                        $(INVOICE_MODULE.container + ' .error-alert').hide().find('.alert-text').html('');
                    },
                    make:function(html){
                        $(INVOICE_MODULE.container + ' .error-alert').show().find('.alert-text').html(html);
                    }
                },
                alerts:{
                    clear:function(){
                        $(INVOICE_MODULE.container + ' .message-alert').hide().find('.alert-text').html('');
                    },
                    make:function(html, hide_after=3000){
                        $(INVOICE_MODULE.container + ' .message-alert').show().find('.alert-text').html(html);

                        /* Hide after mentioned time */
                        setTimeout(function(){
                            INVOICE_MODULE.alerts.clear();
                        }, hide_after);
                    }
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
                            KTApp.block(INVOICE_MODULE.container,{
                                overlayColor: '#000',
                                type: 'v2',
                                state: 'primary',
                                message: 'Please wait while job is processing...'
                            });


                        }
                        else if(state=="error"){
                            /* Unblock modal */

                            KTApp.unblock(INVOICE_MODULE.container);
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

                            if(typeof INVOICE_MODULE !== "undefined"){
                                var loaded_data = JSON.parse( JSON.stringify( INVOICE_MODULE.loaded_data ) );

                                if(typeof loaded_data !== "undefined" && loaded_data){
                                    var job_id = loaded_data._id;

                                    /* Search this job in response */
                                    var updated_job = response.jobs.find(function(job){return job._id==job_id});
                                    if(typeof updated_job !== "undefined" && updated_job){
                                        /* Merge updated job with laoded job */

                                        /* Update invoice */
                                        loaded_data.invoice=updated_job.invoice;


                                        /* Invalidate the job */
                                        INVOICE_MODULE.loaded_data = loaded_data;

                                        if(typeof JOBS !== "undefined" && JOBS){
                                            /* Invalidate the datables row */

                                            /* Find row node */
                                            var rowNode = JOBS.datatable.row(function(x, job){return job._id==loaded_data._id}).node();
                                            if(rowNode){
                                                JOBS.datatable.row(rowNode).data(loaded_data).invalidate();

                                                /* remove the cache data */
                                                $(rowNode).removeAttr('data-row')
                                                .removeAttr('data-temp')
                                                .removeAttr('style');
                                            }

                                            /* Reload the modal */
                                            INVOICE_MODULE.Utils.load_data(loaded_data);
                                        }

                                    }
                                }
                            }

                            /* unblock the modal */
                            KTApp.unblock(INVOICE_MODULE.container);


                        }


                        kingriders.Utils.isDebug() && console.log('response', e);
                    },
                    form_loaded:function(){
                        if(typeof OPENINVOICE_MODULE !== "undefined"){

                            setTimeout(function(){

                                var loaded_data = INVOICE_MODULE.loaded_data;
                                if(loaded_data){
                                    /* Pass to receive payment modal, so it will load it accordingly */

                                    OPENINVOICE_MODULE.Utils.load_page({
                                        client_id:loaded_data.client.id,
                                        invoice_id:loaded_data.invoice.id
                                    });

                                }

                            }, 50);

                        }
                    },
                },
                save_progress:function(){
                     /* allow closing */
                    if(typeof INVOICES !== "undefined"){
                        INVOICES.events.allow_closing=true;
                    }
                    
                    /* clear the errors */
                    INVOICE_MODULE.errors.clear();
                    var is_valid=true;

                    /* client is required */
                    var client_id = $(INVOICE_MODULE.container + ' [name=client_id]').val();
                    client_id && (client_id=client_id.trim());
                    if(!client_id){
                        INVOICE_MODULE.errors.make("Please select <strong>Client</strong>.");
                        is_valid=false;
                    }

                    /* month is required */
                    var month = $(INVOICE_MODULE.container + ' [name=month]').val();
                    month && (month=month.trim());
                    if(!month){
                        INVOICE_MODULE.errors.make("Please select <strong>Month</strong>.");
                        is_valid=false;
                    }

                    /* date is required */
                    var date = $(INVOICE_MODULE.container + ' [name=date]').val();
                    date && (date=date.trim());
                    if(!date){
                        INVOICE_MODULE.errors.make("Please select <strong>Invoice Date</strong>.");
                        is_valid=false;
                    }

                    /* due_date is required */
                    var due_date = $(INVOICE_MODULE.container + ' [name=due_date]').val();
                    due_date && (due_date=due_date.trim());
                    if(!due_date){
                        INVOICE_MODULE.errors.make("Please select <strong>Due Date</strong>.");
                        is_valid=false;
                    }

                    
                    /* total must be greater than 0 */
                    var total=parseFloat($(INVOICE_MODULE.container + ' [name=total]').val())||0;
                    if(total<=0){
                        INVOICE_MODULE.errors.make("<strong>Total</strong> must be greater than 0.");
                        is_valid=false;
                    }

                    if(is_valid){

                        $(INVOICE_MODULE.container + ' form').trigger('submit');
                    }
                    else{
                        return; /* terminate the process */
                    }

                    /* block modal, so update job can be updated in modal */
                    KTApp.block(INVOICE_MODULE.container,{
                        overlayColor: '#000',
                        type: 'v2',
                        state: 'primary',
                        message: 'Please wait while data is processing...'
                    });

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
                            INVOICE_MODULE.complete_job(true);

                        }
                    });
                },
                complete_job:function(do_bikeout=false){
                    /* change the status to complete and submit the form */
                    var total=parseFloat($(INVOICE_MODULE.container + ' [name=total]').val())||0; 
                    var plate = $(INVOICE_MODULE.container + ' [name=plate]').val();
                    var mileage = $(INVOICE_MODULE.container + ' [name=mileage]').val();
                    var is_self = $(INVOICE_MODULE.container + ' [name=is_self]').is(':checked');
                    var is_valid=true;

                    plate && (plate=plate.trim());
                    mileage && (mileage=mileage.trim());

                    /* clear the errors */
                    INVOICE_MODULE.errors.clear();

                    /* total must be greater than 0 */
                    if(total<=0){
                        INVOICE_MODULE.errors.make("<strong>Total</strong> must be greater than 0.");
                        is_valid=false;
                    }
                    /* bike is required */
                    if(!plate){
                        INVOICE_MODULE.errors.make("Please enter bike.");
                        is_valid=false;
                    }

                    /* Check if services contains 'oil change'. No metter the order */
                    if(INVOICE_MODULE.isOilPresent()){

                        /* mileage is required */
                        if(!mileage){
                            INVOICE_MODULE.errors.make("Please enter mileage.");
                            is_valid=false;
                        }

                        /* mileage is greater then 0 */
                        if(mileage && !(mileage>0)){
                            INVOICE_MODULE.errors.make("Mileage must be greater than 0.");
                            is_valid=false;
                        }
                    }



                    if(!is_self){
                        /* validate driver details too */
                        var driver_name = $(INVOICE_MODULE.container + ' [name=driver_name]').val();
                        var driver_phone = $(INVOICE_MODULE.container + ' [name=driver_phone]').val();

                        driver_name && (driver_name=driver_name.trim());
                        driver_phone && (driver_phone=driver_phone.trim());

                        /* driver name is required */
                        if(!driver_name){
                            INVOICE_MODULE.errors.make("Please enter <strong>Driver Name<strong>.");
                            is_valid=false;
                        }

                        /* driver phone is required */
                        if(!driver_phone){
                            INVOICE_MODULE.errors.make("Please enter <strong>Driver Phone<strong>.");
                            is_valid=false;
                        }

                    }

                    if(is_valid){
                        /* allow closing */
                        if(typeof JOBS !== "undefined"){
                            JOBS.events.allow_closing=true;
                        }

                        /* block modal */
                        KTApp.block(INVOICE_MODULE.container,{
                            overlayColor: '#000',
                            type: 'v2',
                            state: 'primary',
                            message: 'Please wait while job is processing...'
                        });
                        $(INVOICE_MODULE.container + ' form [name="status"][value="complete"]').prop('checked', true).trigger('change');

                        /* Check for dobikeout */
                        $(INVOICE_MODULE.container + ' form').find('[name=do_bikeout]').remove();
                        if(do_bikeout){
                            $(INVOICE_MODULE.container + ' form').append('<input type="hidden" name="do_bikeout" value="">');
                        }

                        $(INVOICE_MODULE.container + ' form').trigger('submit');


                    }
                },
                place_on_hold:function(){
                    /* allow closing */
                    if(typeof JOBS !== "undefined"){
                        JOBS.events.allow_closing=true;
                    }
                    KTApp.block(INVOICE_MODULE.container,{
                        overlayColor: '#000',
                        type: 'v2',
                        state: 'primary',
                        message: 'Please wait while job is processing...'
                    });
                    $(INVOICE_MODULE.container + ' form [name="status"][value="on_hold"]').prop('checked', true).trigger('change');
                    $(INVOICE_MODULE.container + ' form').trigger('submit');

                },
                unhold_invoice:function(){
                    /* allow closing */
                    if(typeof JOBS !== "undefined"){
                        JOBS.events.allow_closing=true;
                    }
                    KTApp.block(INVOICE_MODULE.container,{
                        overlayColor: '#000',
                        type: 'v2',
                        state: 'primary',
                        message: 'Please wait while job is processing...'
                    });
                    $(INVOICE_MODULE.container + ' form [name="status"][value="in_progress"]').prop('checked', true).trigger('change');
                    $(INVOICE_MODULE.container + ' form').trigger('submit');
                },
                invoice:{
                    get:function(){
                        /* check if any data is loaded */
                        if(typeof INVOICE_MODULE.loaded_data !== "undefined" && INVOICE_MODULE.loaded_data){
                            return INVOICE_MODULE.loaded_data;
                        }
                        return null;
                    },
                    getPayments:function(){
                        var invoice = this.get();
                        if(invoice){
                            if(invoice.payments){
                                var paidPayments = invoice.payments.filter(x => x.status === 'paid');
                                return {
                                    payments:paidPayments,
                                    total:paidPayments.reduce(function(total,item){return total+(parseFloat(item.amount)||0)},0)
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
                            var form = $(INVOICE_MODULE.container + ' form');
                            var sampleJob = kingriders.Utils.formdata_to_object(new FormData(form[0]));

                            /* Manupilate job */
                            sampleJob.job_items = sampleJob.job_items.map(function(item, index){
                                var rate = parseFloat(item.rate)||0;
                                var qty = parseFloat(item.qty)||0;
                                return {
                                    description: INVOICE_MODULE.typeahead.instance.filter('[name="job_items['+index+'][description]"]').val(),
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
                        var paymentsObj = INVOICE_MODULE.invoice.getPayments();
                        if(paymentsObj){
                            /* check if payments found */
                            if(paymentsObj.payments && paymentsObj.payments.length){

                                var payments = Object.assign([], paymentsObj.payments);

                                /* Sort payments by created_at */
                                payments=payments.sort(function(a,b){
                                    var dateA = moment(a.time);
                                    var dateB = moment(b.time);

                                    if(dateA.isAfter(dateB))return -1;
                                    else if(dateA.isSame(dateB)) return 0;
                                    return 1;
                                });

                                /* Append payments */
                                html='<ul class="list-group">';
                                payments.forEach(function(payment){
                                    var by = payment.by;
                                    var date = moment(payment.time).format('DD/MM/YYYY');
                                    var code = payment.id;
                                    var amount = payment.amount;

                                    html+=''+
                                    '<li class="list-group-item d-flex justify-content-between align-items-center rounded-0">'+
                                    '   <small>'+
                                    '       <span class="text-muted">#'+code+')</span> '+
                                    '       <b>AED '+amount+'</b>'+
                                    '       Payment on '+date+' by '+by+
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
                    var hasOil = $(INVOICE_MODULE.container + ' .tt-input[data-name="description"]')
                    .toArray()
                    .map(function(chunk){return chunk.value})
                    .findIndex(function(x){return x&&x.toLowerCase().match(/oil.*change|change.*oil/g)})>-1;

                    return hasOil;
                },
                events:{
                    onchange_netterms:function(){
                        /* Pick date, add terms and update due date */
                        // var terms = parseInt($(INVOICE_MODULE.container + ' [name="net_terms"]').val())||0;
                        var terms = 10; // Due in 10 days
                        var date = moment($(INVOICE_MODULE.container + ' [name="date"]').val(), 'MMMM DD, YYYY').add(terms, "day").format('MMMM DD, YYYY');

                        /* Update due date */
                        $(INVOICE_MODULE.container + ' [name="due_date"]').attr('data-default', date).datepicker('update', date);
                    },
                }
            };
        }();

        $(function(){
            INVOICE_MODULE.append_row();

            INVOICE_MODULE.calculate_subtotal();

            /* Load any tooltips */
	        KTApp.initTooltips();

            /*
            |---------------------------------
            |               EVENTS
            |---------------------------------
            */

            /* Disable form submit when enter press on inputs */
            $(` #kt-portlet__create-invoice [name=date],
                #kt-portlet__create-invoice [name=due_date],
                #kt-portlet__create-invoice [name=discount_value],
                #kt-portlet__create-invoice [name=tax_value]
            `).on('keypress', function(e){
                if(e.keyCode === 13){
                    e.preventDefault();
                }
            });

            /* change date */
            $(INVOICE_MODULE.container + ' [name="date"]').on('change', function(){
                INVOICE_MODULE.events.onchange_netterms();
            });

            /* do some validation (Save Progress) */
            $(INVOICE_MODULE.container + ' form :submit').on('click', function(e){
                e.preventDefault();

                INVOICE_MODULE.save_progress();

            });


            $(INVOICE_MODULE.container + ' .datatable').on('keypress','input, textarea', function(e){
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

                    /* append a blank row */
                    INVOICE_MODULE.append_row(null,$(this).parents('tr'));

                    /* recalculate data (values zero) */
                    INVOICE_MODULE.calculate_subtotal();

                    /* focus on 1st elem on new row */
                    $(this).parents('tr').next().find('textarea').focus();
                }
            });

            $(INVOICE_MODULE.container + ' .datatable').on('keydown','input, textarea', function(e){

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
                    INVOICE_MODULE.delete_row(deleteBtn);

                    /* focus on 1st elem on new row */
                    if(nextRow.length) nextRow.find('textarea').focus();
                    else $(INVOICE_MODULE.container + ' .datatable tbody tr:last-child textarea').focus();


                }
            });


            /*
            |---------------------------------
            |         CUSTOM CODE
            |---------------------------------
            */

            /* preload the kr-ajax module (only if laoded in modal) */
            var MODAL = $(INVOICE_MODULE.container).parents('.modal');
            if(MODAL.length){
                setTimeout(function(){
                    $(INVOICE_MODULE.container + ' '+kingriders.Plugins.Selectors.kr_ajax_preload).each(function(i, elem){
                        /* initiate the ajax */
                        $(this).trigger('click.krevent', {
                            preload:true
                        });
                    });
                },100);
            }
            $(INVOICE_MODULE.container + ' [data-paymentpopover]').popover({
                container: INVOICE_MODULE.container,
                placement:'left',
                html:true,
            });

            /* This closes all popovers if you click anywhere except on a popover */
            $("#kt-portlet__create-invoice").on("mouseup", function (e) {
                var l = $(e.target);
                if (l.parents('.popover').length==0) {
                    $(INVOICE_MODULE.container + ' [data-paymentpopover]').popover("hide");
                }
            });



            var isEditPage = false;
            /* Check if page has config, do accordingly */
            @isset($config)
            /* This will help us in loading page as edit & view */
            @isset($config->invoice)
            var ModelData = {!! $config->invoice !!};
            INVOICE_MODULE.Utils.load_data(ModelData);
            isEditPage = true;
            @endisset

            @if($config->action=='view')
                /* Disable the page */
                $(INVOICE_MODULE.container + ' input, #kt-portlet__create-invoice textarea, #kt-portlet__create-invoice button:not(.close), #kt-portlet__create-invoice a.btn').prop('disabled', true);
                $(' #kt-portlet__create-invoice select ').prop('disabled', true).trigger('change.select2');

                /* Remove html of buttons */
                $(INVOICE_MODULE.container + ' [kr-ajax-inner-footer] button, #kt-portlet__create-invoice [kr-ajax-inner-footer] a.btn, #kt-portlet__create-invoice .btnReceivePayment').remove();
            @endif
            @endisset

            if(typeof KINGVIEW !== "undefined" && !isEditPage){
                /* Seems page was loaded in OnAir, reset page */
                $(INVOICE_MODULE.container + ' form').attr('action', $(INVOICE_MODULE.container + ' form').attr('data-add'));
                INVOICE_MODULE.Utils.reset_page();
            }

        });
    </script>
@endsection
