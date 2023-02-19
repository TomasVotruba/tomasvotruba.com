<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-9">
                <h1>
                    I help PHP Companies <br>
                    Change Fast&nbsp;and&nbsp;Safely
                </h1>
            </div>

            <div class="col-4 col-md-3">
                <a href="<?php echo e(route('about')); ?>">
                    <img src="<?php echo e(asset('assets/images/tomas_votruba.jpg')); ?>" class="mt-auto rounded-circle shadow">
                </a>
            </div>
        </div>

        <br>

        <div class="clearfix"></div>

        <h2 class="mb-4">
            What I wrote recently?
        </h2>

        <div class="text-bigger">
            <?php $__currentLoopData = $last_posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php /** @var $post \TomasVotruba\Blog\ValueObject\Post */ ?>
                <div class="mb-4 row">
                    <div class="col-12">
                        <a href="<?php echo e(route(\TomasVotruba\Website\ValueObject\RouteName::POST_DETAIL, ['slug' =>  $post->getSlug()])); ?>" class="pt-3 pr-3"><?php echo e($post->getTitle()); ?></a>
                    </div>

                    <div class="small text-secondary col-12 pt-2">
                        <?php echo e($post->getDateTime()->format("Y-m-d")); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <a href="<?php echo e(route(\TomasVotruba\Website\ValueObject\RouteName::BLOG)); ?>" class="btn btn-warning pull-right mt-4">Discover more Posts </a>
        </div>

        <br>
        <br>
        <br>
        <hr>
        <br>
        <br>

        

        <blockquote class="blockquote text-center">
            "<?php echo e($quote); ?>"
        </blockquote>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout/layout_base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/tomasvotruba.com/resources/views/homepage.blade.php ENDPATH**/ ?>