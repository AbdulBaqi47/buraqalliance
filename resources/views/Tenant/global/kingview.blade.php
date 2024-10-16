@extends('Tenant.layouts.app')

@section('page_title')
    Create Client
@endsection
@section('content')

<button type="button" id="onair_cta" @if($modal_width=='full') kr-ajax-modal-type="full" @else kr-ajax-size="{{$modal_width}}" @endif data-backdrop="static" kr-ajax-modalclosing="KINGVIEW.modal_closed" kr-ajax-submit="KINGVIEW.form_submit" kr-ajax-contentloaded="KINGVIEW.form_loaded" kr-ajax="{{url(Request::getRequestUri())}}" class="btn btn-info btn-elevate btn-square d-none">
    <i class="flaticon2-plus-1"></i>
    Button
</button>

@endsection

@section('foot')
    {{------------------------------------------------------------------------------
                                SCRIPTS (use in current page)
    --------------------------------------------------------------------------------}}
    <script type="text/javascript">
    /* We are laoding this page 2 times, so we need to include this code 1 time only */

    var afterAjax = function(callback){
        /* Use reccursion to determine if no more ajax request are pending, we will use jquery $.active for this */

        var _Process=function () {
            if ($.active==0) {
                clearInterval(_CheckForReq);
                if(typeof callback == "function")callback();
            } 
        }

        /* Attach interval on process */
        var _CheckForReq = setInterval(_Process, 500);

        /* But for now, call it instantly */
        _Process();
        
    }
    var KINGVIEW = {
        redirect:function(){
            /* Check if any ajax requests are pending, we will do redirect after that */
            afterAjax(function(){
                console.log('REDIRECTING...');
                window.location.assign(localStorage.OnAirUrl || '/');
            });
        },
        modal_closed:function(){
            setTimeout(function(){
                KINGVIEW.redirect();
            }, 300);
        },
        form_submit:function(e){
            var response = e.response;
            var modal = e.modal;
            var state = e.state; // can be 'beforeSend' or 'completed'
            var linker = e.linker;
            if(state=='beforeSend'){
                /* request is not completed yet, we have form data available */
                KTApp.blockPage({
                    overlayColor: '#000',
                    type: 'v2',
                    state: 'primary',
                    message: 'Please wait while data is processing...'
                });
                
            }
            else {
                /* request might be completed and we have response from server */

                KINGVIEW.redirect();

            }
        },
        form_loaded:function(){
            /* Unblock page */
            KTApp.unblockPage();
        },
    };


    $(function(){

        KTApp.blockPage({
            overlayColor: '#000',
            type: 'v2',
            state: 'primary',
            message: 'Please wait while page is processing...'
        });

        

        $('#onair_cta').trigger('click');
    });
    </script>
@endsection

