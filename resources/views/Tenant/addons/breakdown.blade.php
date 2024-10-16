@extends('Tenant.layouts.app')

@section('page_title')
Addon Breakdown {{ $breakdown_title }}
@endsection
@section('head')
    <style kr-ajax-head>
        .dataTables_wrapper .dataTable{
            margin:0 !important;
        }
        .action_link_icons {
            cursor: pointer;
        }
    </style>
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__addon_breakdown" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Addon Breakdown
                <small>{{ $breakdown_title }}</small>
            </h3>
        </div>

        @if ($view != 'inline_statement')
            <div class="kt-portlet__head-label">
                <div class="kr-widget__tagger kr-widget__tagger--warning">
                    <div>
                        <span>
                            AED
                            <span> {{ $breakdown->remaining }} </span>
                        </span>
                        <small>PAYABLE</small>
                    </div>
                </div>
            </div>
        @endif

        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>

    <div class="kt-portlet__body">

        <div class="row">

            @if ($view != 'inline_statement')
                <div class="@if($view === 'default') col-md-4 @else col-md-6 @endif">
                    <div class="d-flex justify-content-between">
                        <div class="h6">Expenses</div>
                        <span class="kt-font-inverse-light h5 m-0"> @if($view === 'default') {{ $breakdown->total_expenses }} @endif </span>
                    </div>

                    <table class="table table-bordered table-sm m-table dt-expenses">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Amount</th>
                                @if($view === 'default')
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($breakdown->expenses as $expense)
                            <tr>
                                <td>
                                    {{ Carbon\Carbon::parse($expense->given_date)->format('d F, Y')}}
                                </td>
                                <td>
                                    <h6 class="m-0">{{$expense->type}}</h6>
                                    {{$expense->description}}

                                </td>
                                <td>
                                    <div class="expense_amount_text ">
                                        <div class="d-flex flex-column">
                                            @if($view === 'default')
                                                {{$expense->amount}}
                                            @else
                                                <i class="fa fa-check text-success"></i>
                                            @endif
                                        </div>
                                    </div>
                                    @if($view === 'default')
                                        <div class="expense_amount_input" style="display: none !important">
                                            <input type="number" step="0.01" min='0.01' class="form-control " value="{{ $expense->amount }}"/>
                                        </div>
                                    @endif
                                </td>
                                @if($view === 'default')
                                <td class="text-center align-middle">
                                    @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.edit'))
                                        <div class="edit_actions" style="display: none">
                                            <i title="Save" class="fa fa-check fa-sm text-success action_link_icons" x-id="{{ $expense->_id }}" onclick="ADDON_ACTIONS.editActionSuccess(this, event)"></i>
                                            <i title="Cancel" class="fa fa-times fa-sm text-danger action_link_icons" x-id="{{ $expense->_id }}" onclick="ADDON_ACTIONS.editActionCancell(this, event)"></i>
                                        </div>
                                        <i title="Edit Amount" class="fa fa-edit fa-sm text-info action_link_icons" data-id="{{ $expense->_id }}" onclick="ADDON_ACTIONS.editTrigger(this, event)"></i>
                                    @endif
                                    @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.charge') && !isset($expense->charge_amount))
                                        <i title="Charge" data-amount="{{ $expense->amount }}" class="fa fa-money-bill text-warning fa-sm action_link_icons" data-id="{{ $expense->_id }}" onclick="ADDON_ACTIONS.chargeAction(this, event)"></i>
                                    @endif
                                    @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.delete'))
                                        <i title="Delete" class="fa fa-trash text-danger fa-sm action_link_icons" data-id="{{ $expense->_id }}" onclick="ADDON_ACTIONS.deleteAction(this, event)"></i>
                                    @endif
                                </td>
                                @endif
                            </tr>

                            @endforeach
                            @foreach ($pending_expenses_for_addon as $expense)
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <h6 class="m-0 text-warning">{{ $expense['title'] }}</h6>
                                    </td>
                                    <td>
                                        <div class="expense_amount_text ">
                                            <div class="d-flex flex-column">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($view === 'default')
                <div class="col-md-4">
                    <div class="d-flex justify-content-between">
                        <div class="h6">Chargeable</div>
                        <span class="kt-font-inverse-light h5 m-0"> {{ $breakdown->expenses->whereNotNull('charge_amount')->sum('charge_amount') + $addon->price }} </span>
                    </div>
                    <table class="table table-bordered table-sm m-table dt-charged">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Amount</th>
                                @if($view === 'default')
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    {{ Carbon\Carbon::parse($addon->date)->format('d F, Y')}}
                                </td>
                                <td>
                                    <h6 class="m-0"> <b>Base:</b> {{$addon->setting->title}}</h6>
                                    {!! $addon->readable_details ?? "" !!}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        {{$addon->price}}
                                    </div>
                                </td>
                                @if($view === 'default')
                                <td></td>
                                @endif
                            </tr>
                            @foreach ($breakdown->expenses->whereNotNull('charge_amount')->where('charge_amount', '>', 0) as $expense)
                            <tr>
                                <td>
                                    {{ Carbon\Carbon::parse($expense->given_date)->format('d F, Y')}}
                                </td>
                                <td>
                                    <h6 class="m-0">{{$expense->type}}</h6>
                                    {{$expense->description}}

                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        {{$expense->charge_amount}}
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.remove-charge') && $view === 'default')
                                        <i data-id="{{ $expense->_id }}" onclick="ADDON_ACTIONS.removeChargeAction(this, event)" class="fa fa-eraser text-danger action_link_icons" title="Remove Charge"></i>
                                    @endif
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between">
                        <div class="h6">Charged</div>
                        <span class="kt-font-inverse-light h5 m-0"> {{ $breakdown->total_deductions }} </span>
                    </div>
                    <table class="table table-bordered table-sm m-table dt-deductions">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($breakdown->deductions as $deduction)

                            <tr>
                                <td>
                                    {{Carbon\Carbon::parse($deduction->date)->format('d F, Y')}}
                                </td>
                                <td>
                                    {{$deduction->amount}}
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>
                </div>

            @else

                <div class="@if($view === 'inline_statement') col-md-12 @else col-md-6 @endif">
                    <div class="d-flex justify-content-between">
                        <div class="h6">Chargeable Breakdown</div>

                    </div>
                    <table class="table table-bordered table-sm m-table dt-deductions">

                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Credit</th>
                                <th>Debit</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($statement_breakdown as $item)

                            <tr>
                                <td>
                                    <h6 class="m-0">{!! $item->title !!} </h6>
                                    <small class="d-block">
                                        {!! $item->description !!}
                                    </small>
                                </td>
                                <td>
                                    {{Carbon\Carbon::parse($item->date)->format('d F, Y')}}
                                </td>
                                <td>
                                    @if ($item->type === 'cr')
                                    {{$item->amount}}
                                    @else
                                    0
                                    @endif
                                </td>
                                <td>
                                    @if ($item->type === 'dr')
                                    {{$item->amount}}
                                    @else
                                    0
                                    @endif
                                </td>
                            </tr>

                            @endforeach

                            {{-- Sum Row --}}
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <b>
                                        {{ $statement_breakdown->where('type', 'cr')->sum('amount') }}
                                    </b>
                                </td>
                                <td>
                                    <b>
                                        {{ $statement_breakdown->where('type', 'dr')->sum('amount') }}
                                    </b>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            @endif
        </div>


    </div>
</div>

<!--end::Portlet-->




@endsection

@section('foot')
    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script kr-ajax-head type="text/javascript">

        var ADDON_BREAKDOWN = {
            container: '#kt-portlet__addon_breakdown',
            data: {!! json_encode($breakdown) !!},


            datatable: {
                expenses: null,
                deductions: null,
                charged: null,
            },
            table: null,
            Utils:{
                load: function(data){

                }
            },

            initTable: function(tableKey, tableEl){

                // begin first table
                this.datatable[tableKey] = tableEl.DataTable({
                    responsive: true,
                    dom:'tp',
                    lengthMenu: [[-1], ["All"]],
                    searchDelay: 100,
                    processing: true,
                    destroy: true,
                    serverSide: false,
                    deferRender: true,
                    ordering: false,

                });
            }
        }

        $(function(){
            // ADDON_BREAKDOWN.initTable('expenses', $('.dt-expenses'));
            // ADDON_BREAKDOWN.initTable('deductions', $('.dt-deductions'));
            // ADDON_BREAKDOWN.initTable('charged', $('.dt-charged'));
        });
    </script>
    @if($view === 'default')
        <script kr-ajax-head type="text/javascript">
            var ADDON_ACTIONS = {
                callAjax(url, data = null){
                    $.ajax({url, method: 'POST', data}).then(res => {
                        if(res.status){
                            swal.fire({
                                title: 'Completed',
                                type: 'success',
                            }).then(() => {
                                let this_route = {!! json_encode(request()->route()) !!};
                                $.ajax(
                                    `{{ route('tenant.admin.addons.breakdown', '__param') }}`.replace('__param', this_route.parameters.id)
                                )
                                .then(res => {
                                    $('#kt-portlet__addon_breakdown').html($(res).find('#kt-portlet__addon_breakdown'));
                                })
                            })
                        }
                    });
                },
                @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.edit'))
                    editTrigger(element, e){
                        e.preventDefault();
                        let el = $(element);
                        let closest_row = el.closest('tr')
                        let text = $(closest_row[0]).find('.expense_amount_text');
                        let input =$(closest_row[0]).find('.expense_amount_input');
                        $(closest_row[0]).find('[data-id]').hide()
                        $(closest_row[0]).find('.edit_actions').show();
                        text.hide();
                        input.show();
                    },
                    editActionSuccess(element, e){
                        e.preventDefault();
                        let el = $(element);
                        let closest_row = el.closest('tr')
                        let text = $(closest_row[0]).find('.expense_amount_text');
                        let input =$(closest_row[0]).find('.expense_amount_input');
                        let id = el.attr('x-id');
                        let amount = input.find('input').val();
                        let url = '{{ route("tenant.admin.addons.breakdown.edit","__param") }}'.replace('__param', id);
                        ADDON_ACTIONS.callAjax(url, {amount});
                    },
                    editActionCancell(element, e){
                        e.preventDefault();
                        let el = $(element);
                        let closest_row = el.closest('tr')
                        let text = $(closest_row[0]).find('.expense_amount_text');
                        let input =$(closest_row[0]).find('.expense_amount_input');
                        let id = el.attr('data-id');
                        $(closest_row[0]).find('[data-id]').show();
                        input.hide();
                        text.show();
                        $('.edit_actions').hide();
                    },
                @endif
                @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.charge'))
                    chargeAction(element, e){
                        e.preventDefault();
                        let el = $(element);
                        let id = el.attr('data-id');
                        let amount = el.attr('data-amount');
                        let url = '{{ route("tenant.admin.addons.breakdown.charge","__param") }}'.replace('__param', id);
                        swal.fire({
                            title: 'Charge addon expense',
                            type: 'warning',
                            input: 'number',
                            inputPlaceholder: "Charge amount",
                            text: "Enter charge amount",
                            inputValue: amount,
                            showCancelButton: true,
                            inputValidator: (value) => {
                                if (!value && value < 0) {
                                    return "You need to write something!";
                                }
                            },
                            confirmButtonText: 'Yes, Charge!',
                            scrollbarPadding: false,
                        }).then(ret => {
                            let value = parseFloat(ret.value)
                            let data = {amount: value}
                            if(value > 0){
                                ADDON_ACTIONS.callAjax(url, data);
                            }else{
                                swal.fire({
                                    title: 'Invalid Input Value Provided',
                                    type: 'error',
                                })
                            }
                        })
                    },
                @endif
                @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.delete'))
                    deleteAction(element, e){
                        e.preventDefault();
                        let el = $(element);
                        let id = el.attr('data-id');
                        let url = '{{ route("tenant.admin.addons.breakdown.delete","__param") }}'.replace('__param', id);
                        swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            showLoaderOnConfirm: true,
                            scrollbarPadding: false,
                        }).then(ret => {
                            if(ret.value){
                                ADDON_ACTIONS.callAjax(url);
                            }
                        })
                    },
                @endif
                @if ($helper_service->routes->has_access('tenant.admin.addons.breakdown.remove-charge'))
                    removeChargeAction(element, e){
                        e.preventDefault();
                        let el = $(element);
                        let id = el.attr('data-id');
                        let url = '{{ route("tenant.admin.addons.breakdown.remove-charge","__param") }}'.replace('__param', id);
                        swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Remove Charge!',
                            showLoaderOnConfirm: true,
                            scrollbarPadding: false,
                        }).then(ret => {
                            if(ret.value){
                                ADDON_ACTIONS.callAjax(url);
                            }
                        })
                    }
                @endif
            }
            $(function(){
            });
        </script>
    @endif
@endsection

