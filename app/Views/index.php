<?= $this->extend('default') ?>


<?= $this->section('before_close_head') ?>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div x-data="tabsApp">

    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400" role="tablist">
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'url-tab',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'url-tab',
                    }"
                    type="button"
                    x-on:click="activeTab = 'url-tab'"
                    role="tab">
                    Short a URL
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'qr-tab',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'qr-tab',
                    }"
                    type="button"
                    x-on:click="activeTab = 'qr-tab'"
                    role="tab">
                    QR Code
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'note-tab',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'note-tab',
                    }"
                    type="button"
                    x-on:click="activeTab = 'note-tab'"
                    role="tab">
                    Share a note
                </button>
            </li>
            <li role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'linkgroup-tab',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'linkgroup-tab',
                    }"
                    type="button"
                    x-on:click="activeTab = 'linkgroup-tab'"
                    role="tab">
                    Link group
                </button>
            </li>
        </ul>
    </div>


    <?= form_open(base_url('generate')); ?>

    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">

        <template x-if="activeTab == 'url-tab' || activeTab == 'qr-tab'">
            <div>
                <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Shorten a long URL
                </label>
                <div class="relative mb-3">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961" />
                        </svg>
                    </div>
                    <input type="url" autocomplete="off" name="url" :required="activeTab == 'url-tab' || activeTab == 'qr-tab'" value="<?= set_value('url') ?>" placeholder="https://example.com/my-long-url..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                </div>
            </div>
        </template>

        <template x-if="activeTab == 'note-tab'">
            <div>
                <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Share a note
                </label>
                <div class="relative mb-3">
                    <textarea name="note" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here..."></textarea>
                </div>
            </div>
        </template>


        <div x-cloak x-show="activeTab == 'linkgroup-tab'">
            <p class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                You need be logged in to create a link group.
            </p>
        </div>


        <div data-accordion="collapse" x-show="activeTab == 'url-tab' || activeTab == 'qr-tab' || activeTab == 'note-tab'">
            <h2 id="accordion-collapse-heading-1">
                <?php $expanded = ! empty(array_diff(array_keys(validation_errors()), ['url'])) ? 'true' : 'false'; ?>
                <button type="button" class="flex items-center w-full text-sm font-medium rtl:text-right text-gray-500 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3" data-accordion-target="#accordion-collapse-body-1" aria-expanded="<?= $expanded ?>" aria-controls="accordion-collapse-body-1">
                    <span>Customize your link</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-collapse-body-1" class="hidden" aria-labelledby="accordion-collapse-heading-1">
                <div class="p-5 mt-2 border rounded-md border-gray-200 dark:border-gray-700">

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Shortcode
                    </label>
                    <div class="relative mb-4">
                        <div class="flex">
                            <?php $shortcode_prefix = strlen(base_url()) - 1 ?>
                            <input type="text" value="<?= base_url() ?>" style="width: <?= $shortcode_prefix ?>ch;" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-s-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" disabled />
                            <input type="text" autocomplete="off" name="shortcode" value="<?= set_value('shortcode') ?>" placeholder="my-custom-text..." minlength="4" maxlength="50" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-e-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-2 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </div>

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Password
                    </label>
                    <div class="relative mb-4">
                        <div class="flex">
                            <input type="password" name="password" value="<?= set_value('password') ?>" placeholder="Set a password..." minlength="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-2 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                    </div>

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Expiration
                    </label>
                    <div class="relative mb-4">
                        <div class="flex">
                            <select name="expiration_type" id="expiration_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected>No expiration</option>
                                <option value="time" <?= set_value('expiration_type') == 'time' ? 'selected' : '' ?>>Time</option>
                                <option value="visits" <?= set_value('expiration_type') == 'visits' ? 'selected' : '' ?>>Visits</option>
                            </select>
                        </div>

                        <div class="hidden flex mt-2 max-w-sm gap-2" id="expiration-time">
                            <select name="expiration_after" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <?php foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 24, 48, 72] as $value): ?>
                                    <option value="<?= $value ?>" <?= set_value('expiration_after') == $value ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach ?>
                            </select>

                            <select name="expiration_unit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="minutes" <?= set_value('expiration_unit') == 'minutes' ? 'selected' : '' ?>>Minutes</option>
                                <option value="hours" <?= set_value('expiration_unit') == 'hours' ? 'selected' : '' ?>>Hours</option>
                                <option value="days" <?= set_value('expiration_unit') == 'days' || empty(set_value('expiration_unit')) ? 'selected' : '' ?>>Days</option>
                                <option value="weeks" <?= set_value('expiration_unit') == 'weeks' ? 'selected' : '' ?>>Weeks</option>
                                <option value="months" <?= set_value('expiration_unit') == 'months' ? 'selected' : '' ?>>Months</option>
                            </select>

                        </div>

                        <div class="hidden flex mt-2 max-w-sm gap-2" id="expiration-visits">
                            <input type="number" name="expiration_visits" min="1" value="<?= empty(set_value('expiration_visits')) ? 3 : set_value('expiration_visits') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            <input type="text" value="visits" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div x-show="activeTab == 'url-tab' || activeTab == 'qr-tab' || activeTab == 'note-tab'">
            <?php if (count(validation_errors()) > 0): ?>
                <div class="flex items-center p-4 mt-3 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                    <div>
                        <?= validation_list_errors() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-3" x-show="activeTab == 'url-tab' || activeTab == 'qr-tab' || activeTab == 'note-tab'">
            <button name="submit" :value="activeTab" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <span x-show="activeTab == 'url-tab'">Shorten</span>
                <span x-cloak x-show="activeTab == 'qr-tab'">Generate QR</span>
                <span x-cloak x-show="activeTab == 'note-tab'">Share</span>
                <span x-cloak x-show="activeTab == 'linkgroup-tab'">Create</span>
            </button>
        </div>

    </div>

    <?= form_close(); ?>

</div>

<?= $this->endSection() ?>


<?= $this->section('before_close_body') ?>

<script>
    const expirationHandler = () => {
        document.getElementById("expiration-time").classList.add("hidden");
        document.getElementById("expiration-visits").classList.add("hidden");

        if (document.getElementById("expiration_type").value == "time")
            document.getElementById("expiration-time").classList.remove("hidden");

        if (document.getElementById("expiration_type").value == "visits")
            document.getElementById("expiration-visits").classList.remove("hidden");

    }

    document.getElementById("expiration_type").addEventListener("change", () => {
        expirationHandler();
    });

    expirationHandler();
</script>


<script>
    (function() {

        document.addEventListener('alpine:init', () => {
            Alpine.data('tabsApp', () => ({
                activeTab: 'url-tab',
            }));
        });

    })();
</script>


<?= $this->endSection() ?>