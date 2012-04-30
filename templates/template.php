<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title><?php echo isset($title) ? e($title) : 'My Example Site'; ?></title>
</head>
<body>
    
    <?php echo $content; ?>
    
    <ul id="nav">
        <?php foreach ($app['contentcollection']->getObjects() as $object): ?>
            <?php $metadata = $object->getMetadata(); ?>
            <li><a href="<?php echo e($object->getOutputName()) ?>"><?php echo e($metadata['title']) ?></a></li>
        <?php endforeach ?>
    </ul>
    
</body>
</html>