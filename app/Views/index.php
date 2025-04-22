<?= $this->extend('default') ?>


<?= $this->section('before_close_head') ?>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<style>
    html.dark .ql-toolbar .ql-stroke {
        fill: none;
        stroke: rgb(223, 223, 223);
    }

    html.dark .ql-toolbar .ql-fill {
        fill: rgb(223, 223, 223);
        stroke: none;
    }

    html.dark .ql-toolbar .ql-picker {
        color: rgb(223, 223, 223);
    }

    html.dark .ql-toolbar .ql-picker .ql-picker-options {
        background-color: rgb(75, 75, 75);
    }

    .ql-container.ql-snow,
    .ql-toolbar.ql-snow {
        border: none;
    }

    .ql-editor.ql-blank::before {
        color: gray;
    }

    html.dark .ql-editor.ql-blank::before {
        color: lightgray;
    }

    /* .ql-toolbar.ql-snow {
        border-bottom: 1px rgba(115, 115, 115, 0.2) solid;
    } */

    .ql-editor {
        min-height: 150px;
    }

    .ql-snow .ql-picker.ql-language {
        width: 98px;
    }

    /* .noselection .ace_cursor {
        color: transparent;
    }

    .ace_tooltip {
        display: none !important;
    } */
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<div x-data="tabsApp">

    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400" role="tablist">
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'url',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'url',
                    }"
                    type="button"
                    x-on:click="showTab('url')"
                    role="tab">
                    Short a URL
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'qr',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'qr',
                    }"
                    type="button"
                    x-on:click="showTab('qr')"
                    role="tab">
                    QR Code
                </button>
            </li>
            <li class="me-2" role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'note',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'note',
                    }"
                    type="button"
                    x-on:click="showTab('note')"
                    role="tab">
                    Note or Code
                </button>
            </li>
            <li role="presentation">
                <button
                    class="inline-block p-4"
                    :class="{
                        'border-b-2 text-blue-600 hover:text-blue-600 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500': activeTab === 'linkgroup',
                        'border-b-2 border-transparent hover:border-gray-300 hover:text-gray-600 dark:hover:text-gray-300': activeTab !== 'linkgroup',
                    }"
                    type="button"
                    x-on:click="showTab('linkgroup')"
                    role="tab">
                    Link Group
                </button>
            </li>
        </ul>
    </div>


    <?= form_open(base_url('generate'), ['id' => 'primary-form']); ?>

    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" x-data="{ expirationType: '<?= set_value('expiration_type', '') ?>' }">

        <div v-cloak x-show="activeTab == 'url' || activeTab == 'qr'">
            <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                Shorten a long URL
            </label>
            <div class="relative mb-3">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.213 9.787a3.391 3.391 0 0 0-4.795 0l-3.425 3.426a3.39 3.39 0 0 0 4.795 4.794l.321-.304m-.321-4.49a3.39 3.39 0 0 0 4.795 0l3.424-3.426a3.39 3.39 0 0 0-4.794-4.795l-1.028.961" />
                    </svg>
                </div>
                <input type="url" autocomplete="off" name="url" :required="activeTab == 'url' || activeTab == 'qr'" value="<?= set_value('url') ?>" placeholder="https://example.com/my-long-url..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
            </div>
        </div>

        <div x-cloak x-show="activeTab == 'note'">
            <label for="email-address-icon" class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                Share a note
            </label>
            <div class="relative mb-3 bg-gray-50 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white" style="overflow: auto;">

                <div class="flex border-b border-gray-300 dark:bg-gray-700 dark:border-gray-600">

                    <select id="noteType" name="note_type" x-model="noteType" class="p-1 text-sm bg-gray-50 border-0 dark:bg-gray-700 text-gray-900 dark:text-gray-200 cursor-pointer" aria-label="Select note type">
                        <option value="rich_text">Rich Text</option>
                        <option value="abap">ABAP</option>
                        <option value="ada">Ada</option>
                        <option value="assembly_x86">Assembly x86</option>
                        <option value="c_cpp">C/C++</option>
                        <option value="clojure">Clojure</option>
                        <option value="cobol">COBOL</option>
                        <option value="csharp">C#</option>
                        <option value="css">CSS</option>
                        <option value="dart">Dart</option>
                        <option value="erlang">Erlang</option>
                        <option value="fortran">Fortran</option>
                        <option value="golang">Go</option>
                        <option value="groovy">Groovy</option>
                        <option value="haskell">Haskell</option>
                        <option value="html">HTML</option>
                        <option value="java">Java</option>
                        <option value="javascript">JavaScript</option>
                        <option value="json">JSON</option>
                        <option value="kotlin">Kotlin</option>
                        <option value="lua">Lua</option>
                        <option value="matlab">MATLAB</option>
                        <option value="objectivec">Objective-C</option>
                        <option value="pascal">Pascal</option>
                        <option value="perl">Perl</option>
                        <option value="php">PHP</option>
                        <option value="powershell">PowerShell</option>
                        <option value="python">Python</option>
                        <option value="r">R</option>
                        <option value="ruby">Ruby</option>
                        <option value="rust">Rust</option>
                        <option value="scala">Scala</option>
                        <option value="swift">Swift</option>
                        <option value="typescript">TypeScript</option>
                        <option value="xml">XML</option>
                        <option value="text">Other</option>
                    </select>

                    <div id="toolbar">
                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <select class="ql-header">
                                <option value="1"></option>
                                <option value="2"></option>
                                <option value="3"></option>
                                <option value="4"></option>
                                <option value="5"></option>
                                <option value="6"></option>
                                <option selected></option>
                            </select>
                        </span>

                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>

                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <select class="ql-color"></select>
                            <select class="ql-background"></select>
                        </span>

                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <button class="ql-script" value="sub"></button>
                            <button class="ql-script" value="super"></button>
                        </span>

                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <button class="ql-blockquote"></button>
                        </span>

                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                            <button class="ql-indent" value="-1"></button>
                            <button class="ql-indent" value="+1"></button>
                            <select class="ql-align">
                                <option selected></option>
                                <option value="center"></option>
                                <option value="right"></option>
                                <option value="justify"></option>
                            </select>
                        </span>

                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <button class="ql-formula"></button>
                            <button class="ql-link"></button>
                        </span>

                        <span class="ql-formats" x-show="noteType == 'rich_text'">
                            <button class="ql-clean"></button>
                        </span>

                    </div>

                </div>

                <div x-cloak x-show="noteType == 'rich_text'" id="note-editor"></div>
                <div x-cloak x-show="noteType != 'rich_text'" id="code-editor" class="w-full" style="min-height: 150px;"></div>

            </div>

        </div>


        <div x-cloak x-show="activeTab == 'linkgroup'">
            <p class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
                You need be logged in to create a link group.
            </p>
        </div>


        <div data-accordion="collapse" x-cloak x-show="activeTab == 'url' || activeTab == 'qr' || activeTab == 'note'">
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
                            <select name="expiration_type" id="expiration_type" x-model="expirationType" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="" selected>No expiration</option>
                                <option value="time" <?= set_value('expiration_type') == 'time' ? 'selected' : '' ?>>Time</option>
                                <option value="visits" <?= set_value('expiration_type') == 'visits' ? 'selected' : '' ?>>Visits</option>
                            </select>
                        </div>

                        <div class="flex mt-2 max-w-sm gap-2"
                            id="expiration-time"
                            x-show="expirationType === 'time'">
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

                        <div class="flex mt-2 max-w-sm gap-2"
                            id="expiration-visits"
                            x-show="expirationType === 'visits'">
                            <input type="number" name="expiration_visits" min="1" value="<?= empty(set_value('expiration_visits')) ? 3 : set_value('expiration_visits') ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            <input type="text" value="visits" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm w-full rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div id="errors">
            <?php if (count(validation_errors()) > 0): ?>
                <div class="flex items-center p-4 mt-3 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                    <div>
                        <?= validation_list_errors() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-3" x-cloak x-show="activeTab == 'url' || activeTab == 'qr' || activeTab == 'note'">

            <input type="hidden" name="form_type" :value="activeTab" />

            <button x-on:click="submitForm" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <span x-show="activeTab == 'url'">Shorten</span>
                <span x-cloak x-show="activeTab == 'qr'">Generate QR</span>
                <span x-cloak x-show="activeTab == 'note'">Share</span>
                <span x-cloak x-show="activeTab == 'linkgroup'">Create</span>
            </button>
        </div>

    </div>

    <?= form_close(); ?>

</div>
<?= $this->endSection() ?>


<?= $this->section('before_close_body') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.40.0/ace.min.js" type="text/javascript"></script>

<script>
    (function() {

        const quill = new Quill('#note-editor', {
            modules: {
                toolbar: '#toolbar'
            },
            placeholder: 'Write your note here...',
            theme: 'snow'
        });

        let defaultTab = 'url';
        const defaultNoteType = "<?= set_value('note_type', 'rich_text') ?>";

        const codeEditor = ace.edit("code-editor");
        codeEditor.session.setUseWorker(false)
        codeEditor.setShowPrintMargin(false);
        codeEditor.session.setMode("ace/mode/" + (defaultNoteType == 'rich_text' ? 'text' : defaultNoteType));
        codeEditor.setTheme(`ace/theme/${currentTheme == 'dark' ? 'monokai' : 'textmate'}`);
        codeEditor.setOptions({
            maxLines: Infinity,
            wrap: true,
        });

        // codeEditor.setReadOnly(true);

        window.addEventListener('clicks', function(e) {
            if (e.detail.type == 'theme') {
                codeEditor.setTheme(`ace/theme/${e.detail.value == 'dark' ? 'monokai' : 'textmate'}`);
            }
        });

        document.getElementById('toolbar').prepend(document.getElementById('noteType'));

        if (window.location.hash)
            defaultTab = window.location.hash.substring(1);

        document.addEventListener('alpine:init', () => {
            Alpine.data('tabsApp', () => ({
                init() {
                    this.$watch('noteType', (newValue) => {
                        if (newValue !== 'rich_text') {
                            codeEditor.session.setMode("ace/mode/" + newValue);
                        }
                    });
                },
                activeTab: defaultTab,
                noteType: defaultNoteType,
                showTab(tab) {
                    window.location.hash = tab;
                    this.activeTab = tab;
                    document.getElementById('errors').innerHTML = '';
                    document.querySelector('input[name="url"]').value = '';
                    quill.setText('');
                    codeEditor.setValue('');
                },
                submitForm(e) {

                    const form = e.target.closest('form');

                    if (form.checkValidity() === false) {
                        form.reportValidity();
                        return;
                    }

                    if (this.activeTab == 'note') {

                        const contentInput = document.createElement('input');
                        contentInput.type = 'hidden';
                        contentInput.name = 'content';

                        if (this.noteType == 'rich_text') {
                            const editorContent = quill.getText();
                            if (editorContent.trim() === '') {
                                alert('Please write a note before submitting.');
                                return;
                            }
                            contentInput.value = quill.getSemanticHTML();
                        } else {
                            const codeContent = codeEditor.getValue();
                            if (codeContent.trim() === '') {
                                alert('Please white any code before submitting.');
                                return;
                            }
                            contentInput.value = codeContent;
                        }
                        form.appendChild(contentInput);
                    }

                    form.submit();
                },
            }));

        });

    })();
</script>
<?= $this->endSection() ?>