<style type="text/css">
    * {
        margin: 0px;
        padding: 0px;
    }

    body {
        overflow: hidden;
        background-color: #CCCCCC;
        color: #000000;
    }

    #bee-plugin-container {
        position: absolute;
        top: 5px;
        bottom: 30px;
        left: 5px;
        right: 5px;
    }

    #integrator-bottom-bar {
        position: absolute;
        height: 25px;
        bottom: 0px;
        left: 5px;
        right: 0px;
    }
</style>
<script src="https://app-rsrc.getbee.io/plugin/BeePlugin.js" type="text/javascript"></script>
<div id="bee-plugin-container"></div>
<script type="text/javascript">

    var endpoint = "https://auth.getbee.io/apiauth";

    var payload = {
        client_id: "2efb5f4f-f7c6-4bd7-a7c1-0ea778839efd", // Enter your client id
        client_secret: "6FdtxHwfVAQMPHz1roVHnDhM21z8rQguoWx49vvCq4OW5jaOtt24", // Enter your secret key
        grant_type: "password" // Do not change
    };
    var specialLinks = [{
        type: 'close',
        label: 'SpecialLink.Unsubscribe',
        link: 'http://[unsubscribe]/'
    }];

    var save = function (filename, content) {
        console.log('saving ',filename,mQuery('textarea.builder-html', window.parent.document));
        mQuery('textarea.builder-html', window.parent.document).val(content);
    };
    var saveAsTemplate = function (filename, content) {
        console.log('saving template',filename,mQuery('textarea.template-builder-html', window.parent.document));
        mQuery('textarea.template-builder-html', window.parent.document).val(content);
    };

    $.post(endpoint, payload)
        .done(function(data) {
            var token = data;
            // Define a global variable to reference the BEE Plugin instance.
            // Tip: Later, you can call API methods on this instance, e.g. bee.load(template)
            var bee;

            // Define a simple BEE Plugin configuration...
            var config = {
                uid: 'eng',
                container: 'bee-plugin-container',
                autosave: 30, // [optional, default:false]
                language: 'fr-FR', // [optional, default:'en-US']
                trackChanges: false, // [optional, default: false]
                //specialLinks: specialLinks, // [optional, default:[]]
                /*mergeTags: mergeTags, // [optional, default:[]]
                mergeContents: mergeContents, // [optional, default:[]]*/
                preventClose: true, // [optional, default:false]
                //editorFonts : {}, // [optional, default: see description]
                //contentDialog : {}, // [optional, default: see description]
                //defaultForm : {}, // [optional, default: {}]
                //roleHash : "", // [optional, default: ""]
                //rowDisplayConditions : {}, // [optional, default: {}]
                onChange: function (jsonFile, response) {
                    console.log('json', jsonFile);
                    console.log('response', response);
                    saveAsTemplate('newsletter-template.json', jsonFile);
                },
                onSave: function (jsonFile, htmlFile) {
                    save('newsletter.html', htmlFile);
                },
                onSaveAsTemplate: function (jsonFile) { // + thumbnail?
                    saveAsTemplate('newsletter-template.json', jsonFile);
                },
                /*onAutoSave: function (jsonFile) { // + thumbnail?
                    console.log(new Date().toISOString() + ' autosaving...');
                    window.localStorage.setItem('newsletter.autosave', jsonFile);
                },*/
                /*onSend: function (htmlFile) {
                    //write your send test function here
                },*/
                /*onError: function (errorMessage) {
                    console.log('onError ', errorMessage);
                }*/
            }

            // Call the "create" method:
            // Tip:  window.BeePlugin is created automatically by the library...
            window.BeePlugin.create(token, config, function(instance) {
                bee = instance;
                // You may now use this instance...
                var template = <?php echo $contenttemplate; ?>; // Any valid template, as JSON object

                bee.start(template);
            });
        });


/*    // Set up BeeFree editor with the Newsletter plugin
    var bodytext = '';
    var m = (window.opener.mQuery('textarea.builder-html').val()).match(/<body[^>]*>([^<]*(?:(?!<\/?body)<[^<]*)*)<\/body\s*>/i);
    if (m) {
        bodytext = m[1];
    }
    //console.log(bodytext);
    var editor = grapesjs.init({
        height: '100%',
        noticeOnUnload: 0,
        storageManager: {type: null},
        container: '#gjs',
        components: bodytext,

        assetManager: {
            assets: <?php echo json_encode($images); ?>,
            upload: '<?php echo $view['router']->generate('mautic_beefree_upload', [], true) ?>',
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
                var newContent = ($('textarea#templateBuilder').val()).replace('||BODY||', editor.runCommand('gjs-get-inlined-html'));
                console.log(newContent);
                window.opener.mQuery('textarea.builder-html').val(newContent);
                window.close();
            },
            attributes: {title: 'Save and close'}
        }
        ]);


    let iFrame = mQuery("#gjs iframe.gjs-frame");
    editor.on('rte:enable', (some, argument) => {
        let elem = iFrame.contents().find(some.$el[0]);
        elem.atwho('setIframe', iFrame[0]);
        Mautic.initAtWho(elem, 'email:getBuilderTokens', false);
    });*/
</script>