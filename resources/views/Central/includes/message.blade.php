@if (session()->has('message'))
<div class="mt-2">
    <div class="alert alert-success fade show" role="alert">
        <div class="alert-icon"><i class="flaticon2-correct"></i></div>
        <div class="alert-text">{{ session('message') }}</div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
</div>
@elseif (session()->has('error'))
<div class="mt-2">
    <div class="alert alert-danger fade show" role="alert">
        <div class="alert-icon"><i class="flaticon2-delete"></i></div>
        <div class="alert-text">{{ session('error') }}</div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
</div>
@endif
