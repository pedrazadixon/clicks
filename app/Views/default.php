<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clicks</title>

    <link href="//cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />

    <?= $this->include('partials/theme-checker') ?>

    <?= $this->renderSection('before_close_head', true) ?>
</head>

<body class="container px-1 mx-auto bg-white dark:bg-gray-900 antialiased ">

    <?= $this->include('partials/navbar') ?>

    <?php echo session()->getFlashdata('message'); ?>

    <?= $this->renderSection('content', true) ?>

    <script src="//cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

    <script src="<?= base_url('/js/default.js'); ?>"></script>

    <?= $this->renderSection('before_close_body', true) ?>
</body>

</html>