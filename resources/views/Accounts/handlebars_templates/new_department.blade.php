{{-- NEW DEPARTMENT --}}
<script id="handlebars-department" type="text/x-handlebars-template">
    <div class="card">

        <!--begin::Portlet-->
        <div class="kt-portlet m-0" data-ktportlet="true">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="flaticon2-position kt-label-font-color-2"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        @{{deptitle}}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group">
                        <div class="kt-portlet__head-group">
                            <a href="#" data-ktportlet-tool="reload" class="btn btn-sm btn-icon btn-brand btn-elevate btn-icon-md"><i class="la la-refresh"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="kt-portlet__content">
                    <table class="table table-striped- table-bordered table-hover table-checkable datatable" data-account="@{{depname}}">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--end::Portlet-->

    </div>
</script>