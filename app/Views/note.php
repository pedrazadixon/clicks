<?= $this->extend('default') ?>


<?= $this->section('before_close_head') ?>
<?php if ($link["language"] === 'rich_text') : ?>
    <!-- <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" /> -->
<?php endif ?>

<?php if ($link["language"] !== 'rich_text') : ?>

    <style>
        .ace_cursor {
            color: transparent !important
        }

        .ace_tooltip {
            display: none !important;
        }
    </style>
<?php endif ?>
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<?php if ($link["language"] === 'rich_text') : ?>
    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Note content</h4>
    <div id="host" style="white-space: normal;" class="ql-editor mb-6 p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
<?php endif ?>

<?php if ($link["language"] !== 'rich_text') : ?>
    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Code content</h4>
    <div id="code-editor" class="w-full mb-6 hidden border dark:border-gray-600" style="min-height: 150px;"><?= htmlentities($link['content']) ?></div>
<?php endif ?>

<?= $this->endSection() ?>


<?= $this->section('before_close_body') ?>
<?php if ($link["language"] === 'rich_text') : ?>
    <script>
        const host = document.querySelector("#host");
        const shadow = host.attachShadow({
            mode: "open"
        });

        const style = document.createElement('style');
        style.textContent = `
            div {
                .ql-indent-1 {
                    margin-left: 1.5rem;
                }
                .ql-indent-2 {
                    margin-left: 3rem;
                }
                .ql-indent-3 {
                    margin-left: 4.5rem;
                }
                .ql-indent-4 {
                    margin-left: 6rem;
                }
                .ql-indent-5 {
                    margin-left: 7.5rem;
                }
                .ql-indent-6 {
                    margin-left: 9rem;
                }
                .ql-indent-7 {
                    margin-left: 10.5rem;
                }
                .ql-indent-8 {
                    margin-left: 12rem;
                }
                .ql-indent-9 {
                    margin-left: 13.5rem;
                }
                font-size: 1rem;
                p {
                    margin: .25rem;
                }
            }
        `;
        shadow.appendChild(style);

        const div = document.createElement('div');
        div.innerHTML = "<?= str_replace('"', '\"', $link['content']) ?>";
        shadow.appendChild(div);
    </script>
<?php endif ?>


<?php if ($link["language"] !== 'rich_text') : ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.40.0/ace.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.40.0/theme-monokai.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.40.0/theme-textmate.js"></script>

    <script>
        const codeEditor = ace.edit("code-editor");
        codeEditor.session.setUseWorker(false)
        codeEditor.setShowPrintMargin(false);
        codeEditor.session.setMode("ace/mode/<?= $link['language'] ?>");
        codeEditor.setTheme(`ace/theme/${currentTheme == 'dark' ? 'monokai' : 'textmate'}`);
        codeEditor.setOptions({
            maxLines: Infinity,
            wrap: true,
        });
        codeEditor.renderer.setScrollMargin(10, 10)
        codeEditor.setReadOnly(true);

        codeEditor.setHighlightActiveLine(false);
        codeEditor.setHighlightGutterLine(false);
        codeEditor.setDisplayIndentGuides(false);

        window.addEventListener('clicks', function(e) {
            if (e.detail.type == 'theme') {
                codeEditor.setTheme(`ace/theme/${e.detail.value == 'dark' ? 'monokai' : 'textmate'}`);
            }
        });

        document.getElementById('code-editor').classList.remove('hidden');
    </script>
<?php endif ?>
<?= $this->endSection() ?>