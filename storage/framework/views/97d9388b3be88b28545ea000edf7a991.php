<?php if(session()->has('message')): ?>
<div class="mt-2">
    <div class="alert alert-success fade show" role="alert">
        <div class="alert-icon"><i class="flaticon2-correct"></i></div>
        <div class="alert-text"><?php echo e(session('message')); ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
</div>
<?php elseif(session()->has('error')): ?>
<div class="mt-2">
    <div class="alert alert-danger fade show" role="alert">
        <div class="alert-icon"><i class="flaticon2-delete"></i></div>
        <div class="alert-text"><?php echo e(session('error')); ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="la la-close"></i></span>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH C:\Users\DELL 5300\Documents\buraqalliance\resources\views/Central/includes/message.blade.php ENDPATH**/ ?>