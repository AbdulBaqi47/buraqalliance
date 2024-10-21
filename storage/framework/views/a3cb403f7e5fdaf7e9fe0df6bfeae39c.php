

<?php $__env->startSection('title', __('Not Found')); ?>
<?php $__env->startSection('code', '404'); ?>

<?php $__env->startSection('message'); ?>

    <?php if(isset($exception)): ?>
        <?php echo e($exception->getMessage()); ?>

    <?php else: ?>
        Page Not Found, please recheck URL
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('errors::minimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DELL 5300\Documents\buraqalliance\resources\views/errors/404.blade.php ENDPATH**/ ?>