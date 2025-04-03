<?= $this->extend('default') ?>

<?= $this->section('content') ?>

<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="main-tabs" data-tabs-toggle="#main-tabs-content" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block cursor-pointer p-4 border-b-2 rounded-t-lg" data-tabs-target="#url-tab" type="button" role="tab" aria-controls="url-tab" aria-selected="false">
                Short a URL
            </button>
        </li>
        <li class="me-2" role="presentation">
            <button class="inline-block cursor-pointer p-4 border-b-2 rounded-t-lg" data-tabs-target="#qr-tab" type="button" role="tab" aria-controls="qr-tab" aria-selected="false">
                QR Code
            </button>
        </li>
        <li class="me-2" role="presentation">
            <button class="inline-block cursor-pointer p-4 border-b-2 rounded-t-lg" data-tabs-target="#note-tab" type="button" role="tab" aria-controls="note-tab" aria-selected="false">
                Share a note
            </button>
        </li>
    </ul>
</div>

<div id="main-tabs-content">

    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="url-tab" role="tabpanel" aria-labelledby="url-tab">
        <?= form_open(base_url('generate')); ?>
        <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
            Shorten a long URL
        </label>
        <div class="relative mb-3">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961" />
                </svg>
            </div>
            <?= form_input('url', set_value('url'), [
                'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                'required' => 'required',
                'placeholder' => 'https://example.com/my-long-url...'
            ]); ?>
        </div>


        <div data-accordion="collapse">
            <h2 id="accordion-collapse-heading-1">
                <button type="button" class="flex items-center w-full text-sm font-medium rtl:text-right text-gray-500 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3" data-accordion-target="#accordion-collapse-body-1" aria-expanded="false" aria-controls="accordion-collapse-body-1">
                    <span>Customize your link</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-collapse-body-1" class="hidden" aria-labelledby="accordion-collapse-heading-1">
                <div class="p-5 mt-2 border rounded-md border-gray-200 dark:border-gray-700">

                    <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                        Shortcode
                    </label>
                    <div class="relative mb-3">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                            </svg>
                        </div>
                        <?= form_input('shortcode', set_value('shortcode'), [
                            'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                            'placeholder' => 'my-custom-text',
                        ]); ?>
                    </div>

                </div>
            </div>
        </div>


        <?php if (count(validation_errors()) > 0): ?>
            <div class="flex items-center p-4 mt-3 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                <div>
                    <?= validation_list_errors() ?>
                </div>
            </div>
        <?php endif; ?>


        <div class="mt-3">
            <?= form_submit('submit_url', 'Shorten', [
                'class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800'
            ]); ?>
        </div>
        <?= form_close(); ?>
    </div>

    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="qr-tab" role="tabpanel" aria-labelledby="qr-tab">
        <form>
            <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                Create a QR Code
            </label>
            <div class="relative mb-3">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961" />
                    </svg>
                </div>
                <input type="text" id="email-address-icon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="https://example.com/my-long-url..." />
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Create
            </button>
        </form>
    </div>

    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="note-tab" role="tabpanel" aria-labelledby="note-tab">
        <form>
            <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                Share a note
            </label>
            <div class="relative mb-3">

                <textarea id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."></textarea>

            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Share
            </button>
        </form>
    </div>

</div>

<?= $this->endSection() ?>