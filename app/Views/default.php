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

    <?php if (session()->has('message')): ?>
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <?php echo session()->getFlashdata('message'); ?>
            </div>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content', true) ?>

    <script src="//cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

    <script src="<?= base_url('/js/default.js'); ?>"></script>

    <?= $this->renderSection('before_close_body', true) ?>
</body>

</html>