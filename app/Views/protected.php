<?= $this->extend('default') ?>

<?= $this->section('content') ?>

<?= form_open('/' . $link['shortcode']); ?>

<label class="mt-10 block mb-3 text-sm font-medium text-gray-900 dark:text-white">
    You are trying to access a protected link. Please enter the password to continue.
</label>

<div class="flex gap-2 sm:flex-row flex-col">

    <input type="password" autocomplete="off" name="password" value="<?= set_value('password') ?>" required placeholder="Input the password..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

    <?= form_submit('submit', 'Submit', [
        'class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800'
    ]); ?>

</div>

<?= form_close(); ?>

<?= $this->endSection() ?>