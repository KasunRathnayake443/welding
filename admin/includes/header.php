<?php
if (!isset($pageTitle)) {
    $pageTitle = APP_NAME;
}

$pageCssFiles = $pageCssFiles ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?></title>

    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/admin/shared.css">

    <?php foreach ($pageCssFiles as $cssFile): ?>
        <link rel="stylesheet" href="<?php echo ASSETS_URL . 'css/admin/' . e($cssFile); ?>">
    <?php endforeach; ?>
</head>
<body>