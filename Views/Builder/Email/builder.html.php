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
    function base64encode(str) {
        return window.btoa(unescape(encodeURIComponent( str )));
    }
    function base64decode(str) {
        try {
            return decodeURIComponent(escape(window.atob(str)));
        } catch (err){
            console.log('legacy base64 format detected');
            return window.atob(str);
        }
    }
    var endpoint = "https://auth.getbee.io/apiauth";

    var payload = {
        client_id: "<?php echo $apikey; ?>", // Enter your client id
        client_secret: "<?php echo $apisecret; ?>", // Enter your secret key
        grant_type: "password" // Do not change
    };
    var specialLinks = [{
        type: 'close',
        label: 'SpecialLink.Unsubscribe',
        link: 'http://[unsubscribe]/'
    }];

    var save = function (content) {
        console.log('saving ',mQuery('textarea.builder-html', window.parent.document));
        mQuery('textarea.builder-html', window.parent.document).val(content);
    };
    function checksum(s) {
        var hash = 0, strlen = s.length, i, c;
        if ( strlen === 0 ) {
            return hash;
        }
        for ( i = 0; i < strlen; i++ ) {
            c = s.charCodeAt( i );
            hash = ((hash << 5) - hash) + c;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash;
    };
    var saveAsTemplate = function ( content) {
        //console.log('saving template',checksum(mQuery('textarea.template-builder-html', window.parent.document).val()),checksum(btoa(content)));
        mQuery('textarea.template-builder-html', window.parent.document).val(base64encode(content));
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
                language: 'en-US', // [optional, default:'en-US']
                trackChanges: false, // [optional, default: false]
                specialLinks: specialLinks, // [optional, default:[]]
                /*mergeTags: mergeTags, // [optional, default:[]]
                mergeContents: mergeContents, // [optional, default:[]]*/
                preventClose: true, // [optional, default:false]
                //editorFonts : {}, // [optional, default: see description]
                //contentDialog : {}, // [optional, default: see description]
                //defaultForm : {}, // [optional, default: {}]
                //roleHash : "", // [optional, default: ""]
                //rowDisplayConditions : {}, // [optional, default: {}]
                onChange: function (jsonFile, response) {
                    saveAsTemplate(jsonFile);
                },
                onSave: function (jsonFile, htmlFile) {
                    saveAsTemplate(jsonFile);
                    save(htmlFile);
                },
                onSaveAsTemplate: function (jsonFile) { // + thumbnail?
                    //saveAsTemplate(jsonFile);
                },
                onAutoSave: function (jsonFile) { // + thumbnail?
                    console.log(new Date().toISOString() + ' autosaving...');
                    //saveAsTemplate(jsonFile);
                },
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
                //var templatetempjs = atob(mQuery('textarea.template-builder-html', window.parent.document).html());
                //var templatetemp = <?php /*echo $activetemplate;*/ ?>;
                //var templatetempjs = atob(mQuery('textarea.template-builder-html', window.parent.document).val());

                //console.log('template temp',JSON.parse(templatetempjs));
                bee.start(template);
            });
        });

</script>