Mautic.launchCustomBuilder = function (formName, actionName,elem) {
    var currentLink = mQuery(elem);
    var currentActiveTemplate = mQuery('.beefree-selected').find('.select-theme-link').attr('data-theme-beefree');
    console.log('custom builder ',currentActiveTemplate);
    var builderUrl = (mQuery('#builder_url').val()).replace('s/emails/','s/beefree/email/')+'?template=' + currentActiveTemplate;
    console.log('builder url ',builderUrl);
    if ( (mQuery('#builder_url').val()).indexOf('pages') !== -1) {
        builderUrl = (mQuery('#builder_url').val()).replace('s/pages/','s/beefree/page/')+'?template=' + currentActiveTemplate;
    }

    Mautic.loadNewWindowTemp({
        "windowUrl": builderUrl+"&t="+new Date().getTime(),
        "popupName": "beefreePopup"
    });
}

/**
 * Close iframe
 * @param options
 */
Mautic.closeNewWindowTemp =  function (options) {
    console.log('close editor');
    var beefree = document.getElementById('beefree');
    beefree.remove();
}

/**
 * Open an iframe
 * @param options
 */
Mautic.loadNewWindowTemp =  function (options) {
    var div = document.createElement('div');
    div.style.position = "fixed";
    div.style.top = "0";
    div.style.left = "0";
    div.style.bottom = "0";
    div.style.zIndex = "10000";
    div.style.width = "100%";
    div.style.height = "100%";
    div.style.border = "0";
    div.style.background = "#fff";
    div.className = "fullScreen";
    div.id = 'beefree';

    var toolbarbg = document.createElement('div');
    toolbarbg.style.position = 'fixed';
    toolbarbg.style.top = "0";
    toolbarbg.style.left = "0";
    toolbarbg.style.zIndex = "-1";
    toolbarbg.style.width = "100%";
    toolbarbg.style.height = "55px";
    toolbarbg.style.margin = "5px";
    toolbarbg.style.background = "transparent";
    toolbarbg.style.border = "0";

    var toolbar = document.createElement('div');
    toolbar.style.position = 'fixed';
    toolbar.style.top = "0";
    toolbar.style.left = "50%";
    toolbar.style.zIndex = "10000";
    toolbar.style.right = "150px";
    toolbar.style.height = "55px";
    toolbar.style.margin = "5px";
    toolbar.style.background = "transparent";
    toolbar.style.border = "0";

    var buttonexit = document.createElement('button');
    buttonexit.innerText = "FERMER";
    buttonexit.className = "btn btn-primary";
    buttonexit.style.float = 'right';
    buttonexit.style.margin = '12px';
    buttonexit.style.padding = '6px 12px';
    buttonexit.onclick = function () {
        Mautic.closeNewWindowTemp();
    };


    var iframe = document.createElement('iframe');
    //iframe.style.display = "none";
    iframe.style.width = "100%";
    iframe.style.height = "100%";
    iframe.style.border = "0";
    iframe.src = options.windowUrl;
    toolbar.appendChild(buttonexit);
    div.appendChild(toolbar);
    div.appendChild(toolbarbg);
    div.appendChild(iframe);
    document.body.appendChild(div);
}