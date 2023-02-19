<?php $__env->startSection('content'); ?>
    <div class="container-fluid" id="blog">
        <h1><?php echo e($title); ?></h1>

        <div class="row">
            <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php /** @var $book \TomasVotruba\Website\ValueObject\Book */ ?>

                <div class="text-center col-12 col-md-6 mb-5 mt-4">
                    <a href="<?php echo e(route('book-detail', ['slug' => $book->getSlug()])); ?>" target="blank">
                        <img src="<?php echo e($book->getCoverImage()); ?>" style="width: 17em; padding: .5em;margin:0 0 1em 0" class="shadow" alt="Rector book cover">
                    </a>

                    <br>

                    <a
                        href="<?php echo e(route('book-detail', ['slug' => $book->getSlug()])); ?>"
                        target="blank"
                        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'btn btn-lg mt-4 mb-2',
                            'btn-success' => $book->isFinished(),
                            'btn-warning' => ! $book->isFinished(),
                        ]) ?>"
                    >
                        <?php if($book->isFinished()): ?>
                            Buy a Copy
                        <?php else: ?>
                            Become Early Adopter
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout/layout_base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/tomasvotruba.com/resources/views/book/books.blade.php ENDPATH**/ ?>