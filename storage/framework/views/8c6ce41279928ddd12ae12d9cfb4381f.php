<title><?php echo e($title); ?> | Tomas Votruba</title>
<meta charset="utf-8">
<meta name="robots" content="index, follow">


<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">


<meta name="twitter:card" content="summary"/>
<meta name="twitter:creator" content="@votrubaT"/>

<?php if(isset($post)): ?>
    <?php /** @var $post \TomasVotruba\Blog\ValueObject\Post */ ?>

    <meta property="og:title" content="<?php echo e($post->getTitle()); ?>" />
    <meta property="og:url" content="https://tomasvotruba.com<?php echo e(route(\TomasVotruba\Website\ValueObject\RouteName::POST_DETAIL, ['slug' => $post->getSlug()])); ?>" />

    
    <meta property="og:image" content="https://tomasvotruba.com<?php echo e(asset('assets/images/tomas_votruba.jpg')); ?>"/>
    <meta name="twitter:image" content="https://tomasvotruba.com<?php echo e(asset('assets/images/tomas_votruba.jpg')); ?>"/>
<?php else: ?>
    <meta property="og:image" content="<?php echo e(asset('assets/images/tomas_votruba.jpg')); ?>"/>
    <meta name="twitter:image" content="<?php echo e(asset('assets/images/tomas_votruba.jpg')); ?>"/>
<?php endif; ?>

<link rel="alternate" type="application/rss+xml" title="Tomas Votruba Blog RSS" href="<?php echo e(route(\TomasVotruba\Website\ValueObject\RouteName::RSS)); ?>">





<link rel="stylesheet" rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:700&amp;display=swap" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous" media="print" onload="this.media='all'">


<link href="<?php echo e(asset('assets/css/style.css')); ?>?<?php echo mt_rand(1, 100) ?>" rel="stylesheet" type="text/css" />
<?php /**PATH /var/www/tomasvotruba.com/resources/views/_snippets/head.blade.php ENDPATH**/ ?>