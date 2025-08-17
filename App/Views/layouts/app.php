<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My PHP MVC App' ?></title>

<link rel="stylesheet" href="/public/assets/style.css">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

    <main class="container mx-auto px-6 py-6">
        <?= $content ?>
    </main>

<?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
