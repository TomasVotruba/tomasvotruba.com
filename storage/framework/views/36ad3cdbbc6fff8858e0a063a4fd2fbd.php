<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <h1>Read about Bold Innovations in PHP</h1>

        

        {% include "_snippets/post/$post_list->twig" with ['posts' => posts] %}
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('custom_footer'); ?>
    
    <div id="footer">
        <div class="container-fluid text-center">
            Get next post first →

            <a href="https://$twitter->com/votrubaT" target="blank">Twitter</a>
            <span class="pl-2 pr-2">•</span>

            <a href="<?php echo e(route(\TomasVotruba\Website\ValueObject\RouteName::RSS)); ?>" target="blank">RSS</a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout/layout_base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/tomasvotruba.com/resources/views/blog.blade.php ENDPATH**/ ?>