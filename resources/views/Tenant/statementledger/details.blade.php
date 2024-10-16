@extends('Tenant.layouts.app')

@section('page_title')
    Details
@endsection
@section('content')

<!--begin::Portlet-->
<div class="kt-portlet" id="kt-portlet__detail-vehicleledgertransaction" kr-ajax-content>

    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">Details</h3>

        </div>
        <div class="kt-portlet__head-label" kr-ajax-closebtn></div>
    </div>

    <div class="kt-portlet__body">

        <div class="row">
            <div class="col-md-12">

                <table class="table table-bordered m-table">
                    <tbody>
                        <tr>
                            <th scope="row">ID</th>
                            <td>
                                <span>{{ $ledger_item->id }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Date</th>
                            <td>
                                <span>{{ Carbon\Carbon::parse($ledger_item->date)->format('M d, Y') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Title</th>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center">
                                        <span>{{ $ledger_item->title }}</span>

                                        @if(isset($ledger_item->attachment))
                                            <a class="ml-2" target="_blank" href="{{Storage::url($ledger_item->attachment)}}"><i class="la la-file-picture-o"></i></a>
                                        @endif
                                    </div>

                                    @if(isset($ledger_item->description) && $ledger_item->description != '')
                                        <pre style="white-space: pre-wrap;">{{ $ledger_item->description }}</pre>
                                    @endif
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Amount (AED)</th>
                            <td>
                                <span>{{ $ledger_item->amount }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Tag</th>
                            <td>
                                <span>{{ $ledger_item->tag }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Account</th>
                            <td>
                                <span>{{ $account_name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Added By</th>
                            <td>
                                <span>{{ $by }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Ledger ID</th>
                            <td>
                                @isset($ledger)
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ $ledger->id }}</span>
                                        <a class="kt-link kt-link-primary" target="_blank" href="{{ route('tenant.admin.ledger.view') }}?value={{ Carbon\Carbon::parse($ledger->date)->format('Y-m-d') }}&type=day&filter=all">View</a>
                                    </div>
                                @endisset
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>

<!--end::Portlet-->




@endsection


