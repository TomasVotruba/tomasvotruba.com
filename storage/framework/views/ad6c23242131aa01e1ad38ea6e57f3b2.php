<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo $__env->make('_snippets/head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </head>
    <body>
        <?php echo $__env->make('_snippets/menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="container-fluid">
            <div id="content">
                <?php $__env->startSection('content'); ?>
                <?php $__env->stopSection(); ?>
            </div>
        </div>

        <?php $__env->startSection('custom_footer'); ?>
        <?php $__env->stopSection(); ?>

        <?php echo $__env->make('_snippets/google_analytics', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </body>
</html>
<?php /**PATH /var/www/tomasvotruba.com/resources/views/layout/layout_base.blade.php ENDPATH**/ ?>