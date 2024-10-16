@php
    if($config !== null){
        $id = $config['id'];
        $modal = $config['modal'];
        $category = $config['category'];
    }
@endphp
@extends('Tenant.layouts.app')

@section('page_title')
View Activity Log
@endsection

@section('head')
    <style kr-ajax-head>
        /* CSS for timeline */
        .kt-timeline .kt-timeline__item{position:relative}.kt-timeline .kt-timeline__item:before{content:"";width:4px;height:100%;background:#ebedf2;left:1.5rem;top:0;position:absolute}.kt-timeline .kt-timeline__item .kt-timeline__item-section{display:flex;align-items:center}.kt-timeline .kt-timeline__item .kt-timeline__item-section .kt-timeline__item-section-border{border-top:8px solid #fff;border-bottom:8px solid #fff;position:relative}.kt-timeline .kt-timeline__item .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{width:3.2rem;height:3.2rem;border-radius:100%;display:flex;align-items:center;justify-content:center}.kt-timeline .kt-timeline__item .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon>i{font-size:1.7rem}.kt-timeline .kt-timeline__item .kt-timeline__item-section .kt-timeline__item-datetime{margin-left:1.2rem;color:#74788d;font-weight:600;font-size:.9rem}.kt-timeline .kt-timeline__item .kt-timeline__item-text{text-decoration:none;margin-left:4.4rem;color:#595d6e;font-weight:400;font-size:1rem;display:block}.kt-timeline .kt-timeline__item .kt-timeline__item-info{padding:1rem 0 2rem 4.4rem;color:#74788d;font-weight:400;font-size:.9rem}.kt-timeline .kt-timeline__item:last-child .kt-timeline__item-info{padding-bottom:1rem}.kt-timeline .kt-timeline__item.kt-timeline__item--brand .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--brand .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(93,120,255,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--metal .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--metal .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(211,218,230,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--light .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--light .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(255,255,255,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--dark .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--dark .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(100,92,161,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--accent .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--accent .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(0,197,220,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--focus .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--focus .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(152,22,244,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--primary .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--primary .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(88,103,221,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--success .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--success .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(29,201,183,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--info .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--info .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(85,120,235,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--warning .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--warning .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(255,184,34,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}.kt-timeline .kt-timeline__item.kt-timeline__item--danger .kt-timeline__item-section .kt-timeline__item-section-border{background-color:#fff}.kt-timeline .kt-timeline__item.kt-timeline__item--danger .kt-timeline__item-section .kt-timeline__item-section-border .kt-timeline__item-section-icon{background-color:rgba(253,57,122,.2)}.kt-timeline .kt-timeline__item .kt-timeline__item-text:hover{color:#5d78ff}

    </style>
    <!--end::Page Vendors Styles -->
@endsection
@section('content')
<!-- begin:: Content -->
    <div kr-ajax-content class="kt-content @if(!isset($id)) mt-5 @else m-0 p-0 @endif  kt-grid__item kt-grid__item--fluid kt-log_activity" id="kt_content">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand fa fa-hotel"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                    View Activity Log
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar" kr-ajax-closebtn>
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                        </div>
                    </div>
                </div>
            </div>
            <div  @if(isset($id)) hidden @endif class="kt-portlet portlet-selection">
                <div class="row row-no-padding">
                    <div class="col-md-6">
                        <div class="my-2 mx-4">
                            <label>Select Model:</label>
                            <select class="form-control kr-select2" name="subject_type">
                                @foreach ($models as $model)
                                    @php
                                        $class_name=str_replace('"', "", $model->subject_model);
                                        $class=new $class_name;
                                        $table=$class->getTable();
                                    @endphp
                                <option @if(isset($modal) && $modal === $model->subject_model) selected @endif value="{{$model->subject_model}}" >{{$model->subject_model}} [{{$table}}]</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="my-2 mx-4">
                            <label>Model ID</label>
                            <input type="text" value="{{$id ?? null}}"  required class="form-control" name="subject_id" placeholder="Enter Subject_id w.r.t Selected Model">
                        </div>
                    </div>
                </div>
            </div>
            <div  @if(isset($id)) hidden @endif class="kt-portlet__foot">
                <button class="btn btn-primary btn-sm btn-upper float-right submit_log" >Search</button>
            </div>
        </div>
        <!--begin::Portlet-->
        <div class="kt-portlet">

            <div class="kt-portlet__body">
                <div class="kt-timeline">

                    <!--Begin::Timeline -->
                    <div class="kt-timeline">

                        <div class="d-flex justify-content-center h5 text-muted">Loading content...</div>

                        <!--End::Item -->
                    </div>

                    <!--End::Timeline 1 -->
                </div>
            </div>
        </div>

        <!--end::Portlet-->
    </div>
@endsection
@section('foot')
<script kr-ajax-head src="https://cdnjs.cloudflare.com/ajax/libs/platform/1.3.6/platform.js" integrity="sha512-IWkgOhd0ifQr+qh32Uty9REcCuCaK6dPqVfihocITHr2LDgizgbNucjvzxRGFw6xmSmRNv8kzIIFvfGyxncmHg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script kr-ajax-head>

    $(function(){

        $('.kt-log_activity .submit_log').on('click', function(e){

            let subject_id = $('.kt-log_activity [name="subject_id"]').val();
            let subject_type=$('.kt-log_activity [name="subject_type"]').val();
            let category = 'default';
            @if (isset($category))
                category = '{{$category}}';
            @endif
            $.ajax({
                url : "{!! route('tenant.admin.getActivityLog') !!}",
                type : 'GET',
                headers: {
                    'X-NOFETCH': ''
                },
                data: {subject_id:subject_id, subject_type:subject_type, category:category},
                beforeSend: function() {
                    KTApp.block('.kt-log_activity', {
                        size:"sm",
                        type: 'v2',
                        state: 'primary',
                        message:"Processing...."
                    });
                },
                complete: function(){
                    KTApp.unblock('.kt-log_activity');
                },
                success: function(data){
                    $('.kt-log_activity .kt-timeline').html('');
                    if(data.length > 0){
                        data.forEach(function(item){
                        let new_data =item.props?.current ?? [];
                        let old_data =item.props?.old ?? [];
                        let dateObj= new Date(item.time);
                        let html_data = '';
                        let html_old_data = '';
                        let symbol= `<i class="flaticon-edit-1 kt-font-brand"></i>`;
                        $.each(new_data, function( key, value ) {
                            html_data += '<p><strong>' + key +': </strong>' + value + '</p>';
                        });
                        let updated_new=`<button type="button" class="btn btn-outline-success btn-elevate btn-sm btn-circle mx-1" data-toggle="popover" data-trigger="focus" data-placement="top" data-html="true" data-content='${html_data}'>Updated Data</button>`;
                            $.each(old_data, function( index, value ) {
                                html_old_data += '<p><strong>' + index +': </strong>' + value +'</p>';
                            });
                            var updated_old= `<button type="button" class="btn btn-outline-warning btn-elevate btn-sm btn-circle" data-toggle="popover" data-placement="top" data-trigger="focus" data-html="true" data-content='${html_old_data}'>Old Data</button>`;
                            if(item.description==='created'){
                                symbol=`<i class="flaticon2-plus-1 kt-font-success"></i>`;
                                updated_old='';
                                updated_new = updated_new.replace('Updated Data','Data Created');
                            }
                            if(item.description==='deleted'){
                                symbol=`<i class="flaticon-delete kt-font-danger"></i>`;
                                updated_old='';
                                updated_new=`<button type="button" class="btn btn-outline-success btn-elevate btn-sm btn-circle mx-1" data-toggle="popover" data-trigger="focus" data-placement="top" data-html="true" data-content='${html_data}'>Data</button>`;
                            }
                            if(Array.isArray(new_data) && Array.isArray(old_data)){
                                updated_new = '';
                                updated_old = '<button type="button" class="btn btn-outline-warning btn-elevate btn-sm btn-circle mx-1">No Change</button>'
                            }
                            let description= kingriders.Utils.capitalizeFirstLetter(item.description);
                            var activity_log_html = `
                            <div class="kt-timeline__item kt-timeline__item--success">
                                <div class="kt-timeline__item-section">
                                    <div class="kt-timeline__item-section-border">
                                        <div class="kt-timeline__item-section-icon">
                                            ${symbol}
                                        </div>
                                    </div>
                                    <span class="kt-timeline__item-datetime">${description} By<span class="text-danger"><b> ${item.user ? item.user.name : "Cron"}</b></span></span>
                                </div>
                                <p class="kt-timeline__item-text">${updated_new} ${updated_old}</p>
                                <div class="kt-timeline__item-info">
                                    <span><b>IP: </b><a target="_blank" href="https://tools.keycdn.com/geo?host=${item.ip}">${item.ip}</a></span><br/>
                                    <span><b>User Agent: </b><i>${platform.parse(item.user_agent).toString()}</i></span><br/>
                                    <span>${moment(dateObj).format('MMMM DD, yyyy hh:MM:ss A')}</span>
                                </div>
                            </div> `;
                            $('.kt-log_activity .kt-timeline').append(activity_log_html);
                        });
                    }
                    $('[data-toggle="popover"]').popover();
                },
                error: function(error){
                    swal.fire({
                        position: 'center',
                        type: 'error',
                        title: 'Oops...',
                        text: 'Unable to update.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

    });

</script>
@endsection
