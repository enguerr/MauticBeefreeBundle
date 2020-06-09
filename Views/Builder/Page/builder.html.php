<style>
    body, html {
        height: 100%;
        margin: 0;
    }

</style>
<div id="gjs" style="height:0px; overflow:hidden;"></div>
<script type="text/javascript">
    // Set up GrapesJS editor with the Newsletter plugin
    var bodytext = '';
    var m = (window.opener.mQuery('textarea.builder-html').val()).match(/<body[^>]*>([^<]*(?:(?!<\/?body)<[^<]*)*)<\/body\s*>/i);
    if (m) {
        bodytext = m[0];
    }
    var editor = grapesjs.init({
        height: '100%',
        noticeOnUnload: 0,
        storageManager: {type: null},
        container: '#gjs',
        components: bodytext,

        assetManager: {
            assets: <?php echo json_encode($images); ?>,
            upload: '<?php echo $view['router']->generate('mautic_grapejs_upload', [], true) ?>',
            uploadName: 'files',
            multiUpload: true,
            // Text on upload input
            uploadText: 'Drop files here or click to upload',
            // Label for the add button
            addBtnText: 'Add image',
            // Default title for the asset manager modal
            modalTitle: 'Select Image',
        },

        plugins: ['grapesjs-parser-postcss', 'gjs-preset-newsletter'],
        pluginsOpts: {
            'gjs-preset-newsletter': {
                modalLabelImport: 'Paste all your code here below and click import',
                modalLabelExport: 'Copy the code and use it wherever you want',
                codeViewerTheme: 'material',
                //defaultTemplate: templateImport,
                importPlaceholder: '',
                cellStyle: {
                    'font-size': '12px',
                    'font-weight': 300,
                    'vertical-align': 'top',
                    color: 'rgb(111, 119, 125)',
                    margin: 0,
                    padding: 0,
                }
            }
        },
    });
    var pnm = editor.Panels;
    pnm.removeButton("options", "gjs-open-import-template");
    pnm.removeButton("options", "gjs-toggle-images");
    pnm.addButton('options', [{
        id: 'undo',
        className: 'fa fa-undo',
        attributes: {title: 'Undo'},
        command: function () { editor.runCommand('core:undo') }
    }, {
        id: 'redo',
        className: 'fa fa-repeat',
        attributes: {title: 'Redo'},
        command: function () { editor.runCommand('core:redo') }
    }
    ]);

    editor.Panels.removeButton("options", "import");
    pnm.addButton('options',
        [{
            id: 'save',
            className: 'btn-alert-button',
            label: 'Save and close',
            command: function (editor1, sender) {
                var newContent = ($('textarea#templateBuilder').val()).replace('||BODY||', editor.getHtml());
                window.opener.mQuery('textarea.builder-html').val(newContent);
                window.close();
            },
            attributes: {title: 'Save and close'}
        }
        ]);
</script>