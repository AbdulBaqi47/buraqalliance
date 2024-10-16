@extends('Tenant.layouts.app')

@section('page_title')
    Accounts
@endsection
@section('head')
<style>
    @media (min-width: 768px){
        .modal.krajax-modal .modal-dialog {
            max-width: 30% !important;
        }
    }

    #departments_container .card{
        background-color: initial;
        border: none;
        border-radius: 0;
    }

    #departments_container.card-columns{
        column-count: 2;
        column-gap: .7rem;
        orphans: 1;
        widows: 1;
    }

    @media (max-width: 676px){
        #departments_container.card-columns{
            column-count: 1;
        }
    }
</style>
@endsection
@section('content')

<!--begin::Portlet-->

<div class="kt-portlet mt-3">
    <div class="kt-portlet__body  kt-portlet__body--fit">

        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label m-1">
                <h3 class="kt-portlet__head-title">
                    Total Available balance:
                    <strong class="total_available_balance text-success">0</strong>
                </h3>
            </div>
            @if ($helper_service->routes->has_access('module.accounts.add'))
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">
                        <button type="button" data-backdrop="static" kr-ajax-submit="ACCOUNTS.create_submit" kr-ajax-contentloaded="ACCOUNTS.modal_loaded" kr-ajax-preload kr-ajax="{{route('module.accounts.add')}}" class="btn btn-info btn-elevate btn-square">
                            <i class="flaticon2-plus-1"></i>
                            Add Account
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


{{-- ---------------------------- --}}
{{-- Loop through each department --}}
{{-- ---------------------------- --}}

<div class="card-columns" id="departments_container">
    @foreach ($departments as $depname => $accounts)
    <div class="card">

        @php
            $dep = $depname;
            if($depname=='bank')$dep='Bank Accounts';
            if($depname=='cih')$dep='Cash in Hand Accounts';
        @endphp

        <!--begin::Portlet-->
        <div class="kt-portlet m-0" data-ktportlet="true" data-index="{{$loop->index}}">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="flaticon2-position kt-label-font-color-2"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        {{$dep}}
                        {{-- <small>some sub title</small> --}}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group">
                        <div class="kt-portlet__head-group">
                            <a href="#" data-ktportlet-tool="reload" data-dep="{{$depname}}" onclick="ACCOUNTS.reload_card(this);return false;" class="btnAccountReload btn btn-sm btn-icon btn-brand btn-elevate btn-icon-md"><i class="la la-refresh"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                @if ($helper_service->routes->has_access('module.accounts.transfer.add'))
                <div class="card bg-light m-0">
                    <div class="card-body d-flex justify-content-between">
                        <div class="d-flex"></div>
                        <div class="d-flex">
                            <button kr-ajax-submit="ACCOUNTS.transfer.create_submit" kr-ajax-modalclosed="ACCOUNTS.modal_closed" kr-ajax-contentloaded="ACCOUNTS.transfer.modal_loaded" kr-ajax="{{route('module.accounts.transfer.add')}}?dep={{$depname}}" type="button" class="btn btn-outline-primary btn-elevate btn-square btn-sm py-0 px-2">
                                <i class="la la-mail-forward"></i>
                                Transfer amount
                            </button>
                        </div>
                    </div>
                </div>
                @endif
                <div class="kt-portlet__content">
                    <table class="table table-striped- table-bordered table-hover table-checkable datatable" data-account="{{$depname}}">
                        <thead>
                            <tr>
                                <th hidden>ID</th>
                                <th>Account Details</th>
                                <th>Balance</th>
                                <th></th>
                                <th hidden></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- -------------------------- --}}
                            {{-- Loop through each accounts --}}
                            {{-- -------------------------- --}}
                            @foreach ($accounts as $account)
                            <tr class="@if($loop->even) even @else odd @endif">
                                <td hidden>{{$account->id}}</td>
                                <td>
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex flex-column">
                                            <span>{{$account->title}}</span>
                                            @if ($account->department == 'bank')
                                            <small class="kt-font-bold ">{{ucfirst($account->type)}}</small>
                                            @endif
                                        </div>

                                        @isset($account->handle)
                                        <button class="btn btn-sm btn-icon" onclick="ACCOUNTS.copyhandle(this)" data-handle="{{$account->handle}}" data-toggle="kt-tooltip" data-skin="dark" data-original-title="Copy unique account handle">
                                            <i class="la la-copy"></i>
                                        </button>
                                        @endisset
                                    </div>
                                </td>
                                <td>{{ round($account->balance, 2) }}</td>
                                <td></td>
                                <td hidden>{{$account->created_at}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--end::Portlet-->

    </div>
    @endforeach
</div>



<!--end::Portlet-->


@endsection

@section('foot')

{{------------------------------------------------------------------------------
                                HANDLEBARS TEMPLAATES
--------------------------------------------------------------------------------}}

{{-- Department TEMPLATE --}}
@include('Accounts.handlebars_templates.new_department')


{{------------------------------------------------------------------------------
                            SCRIPTS (use in current page)
--------------------------------------------------------------------------------}}

<script type="text/javascript">
    var ACCOUNTS = function(){

        /* Initialize the datatables */
        var init_table=function(){
            var table = ACCOUNTS.table;

            // begin first table
            ACCOUNTS.datatable = $(ACCOUNTS.table).DataTable({
                dom:'t',
                paging: false,
                responsive: true,
                searchDelay: 100,
                processing: true,
                serverSide: false,
                deferRender: true,
                destroy:true,
                drawCallback:function(){
                    var api = this.api();
                },
                rowId:'_id',
                columns: [
                    {data: '_id', visible:false},
                    {data: 'title', orderable: false, width: "50%"},
                    {data: 'balance', orderable: false, width: "20%"},
                    {data: 'actions', orderable: false, width: "30%"},
                    {data: 'created_at', visible:false},
                ],
                order: [[4, 'asc']],
                columnDefs: [
                    {
                        targets: 3,
                        title: 'Actions',
                        orderable: false,
                        render: function(data, type, full, meta) {
                            var url = "{{route('module.accounts.transactions.view', '_:param')}}".replace('_:param', full._id);
                            return `
                            @if ($helper_service->routes->has_access('module.accounts.transactions.view'))
                            <a href="${url}" class="btn btn-outline-dark btn-elevate btn-square btn-sm py-0 px-2" title="View Transactions">
                                <i class="flaticon-eye"></i>
                                View details
                            </a>
                            @endif`;
                        },
                    },
                    {
                        targets: 2, // Balance
                        render: function(data, type, full, meta) {
                            if(data < 0){
                                return `<span class="kt-font-danger">${data}</span>`;
                            }

                            return data;
                        },
                    }
                ],
            });

            /* calculate total balance (of all accounts) */
            ACCOUNTS.calculate_total();
        };

        /* page settings */
        return {
            table:'.datatable',
            datatable:null,
            init:function(){
                init_table();
            },
            create_submit:function(e){
                var response = e.response;
                var modal = e.modal;
                var state = e.state; // can be 'beforeSend' or 'completed'
                var linker = e.linker;
                if(state=='beforeSend'){
                    /* request is not completed yet, we have form data available */

                    /* check department */
                    var dep = response.department;
                    var rowNode = null;

                    if(typeof dep !== "undefined" && dep){
                        /* we need to create  a row dynamically and add to datatables */
                        var tempid = $(ACCOUNTS.table).find('tbody tr').length;
                        var table = $(ACCOUNTS.table).filter('[data-account="'+dep+'"]');

                        var rowObj = {
                            _id:tempid,
                            department:response.department,
                            title:response.title,
                            type:response.type,
                            balance:0,
                            actions:null,
                            created_at:moment.utc().format()
                        };

                        if(table.length){
                            /* department found, append this account */
                            rowNode = ACCOUNTS.datatable
                            .table(table)
                            .row
                            .add( rowObj )
                            .draw()
                            .node();
                        }
                        else{
                            /* no department found, append new department */

                            var template = $('#handlebars-department').html();
                            // Compile the template data into a function
                            var templateScript = Handlebars.compile(template);

                            var deptitle = rowObj.department;
                            if(deptitle=='bank')deptitle='Bank Accounts';
                            if(deptitle=='cih')deptitle='Cash in Hand Account';

                            var context = {
                                deptitle:deptitle,
                                depname:rowObj.department
                            };

                            var html = templateScript(context);

                            $('#departments_container').append(html);

                            /* refresh datables */
                            ACCOUNTS.init();

                            var table = $(ACCOUNTS.table).filter('[data-account="'+dep+'"]');

                            rowNode = ACCOUNTS.datatable
                            .table(table)
                            .row
                            .add( rowObj )
                            .draw()
                            .node();

                        }
                    }

                    if(rowNode){
                        /* add the linker to row and change the color */
                        $( rowNode )
                        .css( 'background-color', 'rgba(169, 252, 220, .1)' )
                        .attr('data-temp', linker);

                        /* animate to element */
                        $([document.documentElement, document.body]).animate({
                                scrollTop: $(rowNode).offset().top-300
                        }, 300);
                    }
                }
                else if(state=="error"){
                        /* it seems server respond with some errors, we need to delete the newly added row */

                        /* corresponding row */
                        var rowNode = $(ACCOUNTS.table).find('tbody tr[data-temp="'+linker+'"]');
                        var table = rowNode.parents('table');

                        /* remove from datatables */
                        ACCOUNTS.datatable.table(table).row(rowNode[0]).remove();

                        /* remove from DOM */
                        rowNode.remove();
                    }
                else{
                    /* request might be completed and we have response from server */

                    /* corresponding row */
                    var rowNode = $(ACCOUNTS.table).find('tbody tr[data-temp="'+linker+'"]');

                    var table = rowNode.parents('table');

                    /* push newly added account */
                    ACCOUNTS_MODULE.accounts.push(Object.assign({}, response));

                    response.actions=null;


                    ACCOUNTS.datatable.table(table).row(rowNode[0]).data(response).invalidate();


                    /* remove the effect after some time */
                    setTimeout(function(){
                        rowNode
                        .removeAttr('data-temp')
                        .removeAttr('style');
                    },2000);
                }

                kingriders.Utils.isDebug() && console.log('response', e);
            },
            modal_loaded:function(){
                if(typeof ACCOUNTS_MODULE !== "undefined")ACCOUNTS_MODULE.Utils.reset_page();
            },
            reload_card:function($this){
                /* block modal */
                var portlet = $($this).parents('.kt-portlet');
                KTApp.block(portlet,{
                    overlayColor: '#000',
                    type: 'v2',
                    state: 'primary',
                    message: 'Reloading...'
                });

                var index = portlet.attr('data-index');

                /* send ajax to this page and get the latest portlet */
                $.ajax({
                    url : '#',
                    type : 'GET',
                    dataType: 'html',
                    complete: function(){
                        KTApp.unblock(portlet);
                    },
                })
                .done(function (response) {
                    /* find the same portlet  */
                    var html = $(response);

                    var _portlet = html.find('.kt-portlet[data-index="'+index+'"]');

                    if(_portlet.length){
                        /* replce the portlet */
                        portlet.replaceWith(_portlet);

                        /* reload the datatables */
                        ACCOUNTS.init();
                    }
                    else{
                        /* show error */
                        swal.fire({
                            position: 'center',
                            type: 'error',
                            title: 'Oops...',
                            html: "Cannot reload the card. Please try again",
                        });
                    }
                });
            },
            copyhandle:function(el){
                var text = el.getAttribute('data-handle');
                kingriders.Utils.copyTextToClipboard(text)
                .then(function(){

                    // Convert to tick, indicating it was a success
                    el.classList.add("text-success");
                    el.querySelector("i").classList.remove('la-copy');
                    el.querySelector("i").classList.add('la-check');

                    setTimeout(function(){

                        // revert back to original
                        el.classList.remove("text-success");
                        el.querySelector("i").classList.remove('la-check');
                        el.querySelector("i").classList.add('la-copy');
                    }, 5000);

                })
                .catch(function(error){

                    console.error(error);
                    alert('Something went wrong while coping' + error);

                });
            },
            calculate_total:function(){
                var all_acocunts = ACCOUNTS.datatable.rows().data().toArray();
                var total = all_acocunts.reduce(function(a, b){return a+parseFloat(b.balance)}, 0).toFixed(2);
                $('.total_available_balance').text('AED '+total);
            },
            modal_closed:function(e){
                var target = e.target;

                if(target){
                    /* Get the index of modal */
                    var index = parseFloat(target.getAttribute('kr-index'))||null;
                    if(index){
                        /* Reset the modal */
                        kingriders.Plugins.KR_AJAX.resetModal(index);
                    }
                    TRANSFER_MODULE = undefined;

                }
            },
            transfer:function(){

                return {
                    create_submit:function(e){
                        var response = e.response;
                        var modal = e.modal;
                        var state = e.state; // can be 'beforeSend' or 'completed'
                        var linker = e.linker;
                        if(state=='beforeSend'){
                            /* request is not completed yet, we have form data available */

                            /* just block the page for now */
                            KTApp.blockPage({
                                opacity:.2,
                                size:"sm",
                                type: 'v2',
                                state: 'primary',
                                message:"Please wait"
                            });
                        }
                        else{
                            /* request might be completed and we have response from server */

                            KTApp.unblockPage();

                            swal.fire({
                                toast: true,
                                customClass: {
                                    content:'mt-0 pl-2'
                                },
                                position: 'top',
                                showConfirmButton: true,
                                timer: 10000,
                                type: 'success',
                                html: `<b>AED ${response.amount}</b> transferred from <b>${response.account_from.title}</b> to <b>${response.account_to.title}</b>`,
                            });

                            /* Reload departments */
                            $('.btnAccountReload[data-dep="'+response.account_from.department+'"]').trigger('click');
                            if(response.account_from.department !== response.account_to.department) {
                                $('.btnAccountReload[data-dep="'+response.account_to.department+'"]').trigger('click');
                            }

                        }

                        kingriders.Utils.isDebug() && console.log('response', e);
                    },
                    modal_loaded:function(btn){
                        var model = btn.getAttribute('data-target');
                        if(typeof TRANSFER_MODULE !== "undefined")TRANSFER_MODULE.Utils.reset_page($(model));
                    },
                };
            }()
        };
    }();

    $(function(){


        ACCOUNTS.init();

        KTApp.initTooltips()
    });
</script>


@endsection
