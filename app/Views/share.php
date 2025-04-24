<?= $this->extend('default') ?>

<?php $is_qr = array_key_exists('qr', request()->getGet()) ?>

<!-- before_close_head -->
<?= $this->section('before_close_head') ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.1/qrcode.js" referrerpolicy="no-referrer"></script>
<?= $this->endSection() ?>


<!-- content -->
<?= $this->section('content') ?>
<div class="gap-3 p-4 md:flex <?= $is_qr ? 'flex flex-col' : '' ?>">

    <div class="flex flex-col grow" style="<?= $is_qr ? 'order: 2;' : '' ?>">
        <div>
            <label for="short-url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Short URL</label>
        </div>

        <div class="flex gap-2 w-full flex-col md:flex-row">
            <input id="short-url" type="text" class="p-3 text-base font-semibold bg-gray-50 border border-gray-300 text-gray-500 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200 dark:focus:ring-blue-500 dark:focus:border-blue-500" value="<?= base_url($link['shortcode']) ?>" disabled readonly>


            <div class="flex gap-2">
                <button data-copy-to-clipboard-target="short-url" class="grow text-white px-8 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 items-center inline-flex justify-center">
                    <span id="default-message">Copy</span>
                    <span id="success-message" class="hidden">
                        <div class="inline-flex items-center">
                            <svg class="w-3 h-3 text-white me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                            </svg>
                            Copied!
                        </div>
                    </span>
                </button>

                <a href="<?= base_url($link['shortcode']) ?>" target="_blank" class="flex-none text-white px-3 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 items-center inline-flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-external-link">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                        <path d="M11 13l9 -9" />
                        <path d="M15 4h5v5" />
                    </svg>
                </a>
            </div>

        </div>

        <?php if ($link['type'] == 'url' || $link['type'] == 'qr'): ?>
            <div class="mt-3">
                <label for="small-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Long url</label>
                <input type="text" id="small-input" value="<?= $link['content'] ?>" disabled readonly class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        <?php endif; ?>

        <div class="flex my-3">

            <button id="share-button" type="button" class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M7.926 10.898 15 7.727m-7.074 5.39L15 16.29M8 12a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm12 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm0-11a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                </svg>
            </button>

            <a href="https://www.facebook.com/sharer.php?u=<?= urlencode(base_url($link['shortcode'])) ?>" target="_blank" class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M13.135 6H15V3h-1.865a4.147 4.147 0 0 0-4.142 4.142V9H7v3h2v9.938h3V12h2.021l.592-3H12V6.591A.6.6 0 0 1 12.592 6h.543Z" clip-rule="evenodd" />
                </svg>
            </a>

            <a href="https://api.whatsapp.com/send?text=Check+out+this+link <?= urlencode(base_url($link['shortcode'])) ?>" target="_blank" class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path fill="currentColor" fill-rule="evenodd" d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z" clip-rule="evenodd" />
                    <path fill="currentColor" d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z" />
                </svg>
            </a>

            <a href="https://x.com/intent/tweet?text=Check+out+this+link <?= urlencode(base_url($link['shortcode'])) ?>" target="_blank" class="text-blue-700 border border-blue-700 hover:bg-blue-700 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center me-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:focus:ring-blue-800 dark:hover:bg-blue-500">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13.795 10.533 20.68 2h-3.073l-5.255 6.517L7.69 2H1l7.806 10.91L1.47 22h3.074l5.705-7.07L15.31 22H22l-8.205-11.467Zm-2.38 2.95L9.97 11.464 4.36 3.627h2.31l4.528 6.317 1.443 2.02 6.018 8.409h-2.31l-4.934-6.89Z" />
                </svg>
            </a>

        </div>
    </div>

    <div class="flex flex-col items-center mt-3 md:mt-0" style="<?= $is_qr ? 'order: 1;' : '' ?>">
        <div class="bg-white rounded-md p-2" style="width: fit-content;">
            <canvas id="qr-canvas"></canvas>
        </div>
        <button id="qr-download" style="max-width: 216px;" class="w-full text-white mt-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 items-center inline-flex justify-center">
            Descargar QR
        </button>
    </div>

</div>
<?= $this->endSection() ?>


<!-- before_close_body -->
<?= $this->section('before_close_body') ?>
<script>
    window.addEventListener('load', function() {
        const clipboard = FlowbiteInstances.getInstance('CopyClipboard', 'short-url');
        const $defaultMessage = document.getElementById('default-message');
        const $successMessage = document.getElementById('success-message');

        clipboard.updateOnCopyCallback((clipboard) => {
            $defaultMessage.classList.add('hidden');
            $successMessage.classList.remove('hidden');

            setTimeout(() => {
                $defaultMessage.classList.remove('hidden');
                $successMessage.classList.add('hidden');
            }, 2000);
        });
    })

    const shareData = {
        title: "Short URL",
        text: "Check out this link!",
        url: "<?= base_url($link['shortcode']) ?>",
    };

    const btn = document.querySelector("#share-button");

    btn.addEventListener("click", async () => {
        try {
            await navigator.share(shareData);
        } catch (err) {
            console.log(`Error: ${err}`);
        }
    });

    document.getElementById('qr-download').addEventListener('click', function() {
        const canvas = document.getElementById('qr-canvas');
        const link = document.createElement('a');
        link.download = 'qrcode_' + "<?= $link['shortcode'] ?>" + '.png';
        link.href = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');
        link.click();
    });
</script>

<script>
    QRCode.toCanvas(document.getElementById('qr-canvas'), '<?= base_url($link['shortcode']) ?>', {
        width: <?= $is_qr ? 300 : 200 ?>,
        margin: 0.3,
        color: {
            dark: "#101828",
            light: "#fff",
        },
    }, function(error) {
        if (error) console.error(error)
    })
</script>
<?= $this->endSection() ?>