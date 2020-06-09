Mautic.lanunchBuilderCore = Mautic.launchBuilder;

Mautic.launchBuilderCustom = function (formName, actionName) {
    var currentActiveTemplate = mQuery('.theme-selected').find('.select-theme-link').attr('data-theme');
    var builderUrl = (mQuery('#builder_url').val()).replace('s/emails/','s/beefree/email/')+'?template=' + currentActiveTemplate;
    if ( (mQuery('#builder_url').val()).indexOf('pages') !== -1) {
        builderUrl = (mQuery('#builder_url').val()).replace('s/pages/','s/beefree/page/')+'?template=' + currentActiveTemplate;
    }
    Mautic.loadNewWindowTemp({
        "windowUrl": builderUrl+"&t="+new Date().getTime(),
        "popupName": "beefreePopup"
    });
}

// launch custom builder for emails and pages without code mode
Mautic.launchBuilder = function (formName, actionName) {
    var isCodeMode = mQuery('.theme-selected').find('.select-theme-link').attr('data-theme') === 'mautic_code_mode';
    if((formName === 'emailform' || formName === 'page' ) && !isCodeMode) {
        Mautic.launchBuilderCustom(formName, actionName);
        return;
    }
    Mautic.lanunchBuilderCore(formName, actionName);
};

/**
 * Open a popup
 * @param options
 */
Mautic.loadNewWindowTemp =  function (options) {
    if (options.windowUrl) {
        Mautic.startModalLoadingBar();

        var popupName = 'mauticpopup';
        if (options.popupName) {
            popupName = options.popupName;
        }

        setTimeout(function () {
            var opener = window.open(options.windowUrl, popupName, 'height=600,width=1100');

            if (!opener || opener.closed || typeof opener.closed == 'undefined') {
                alert(mauticLang.popupBlockerMessage);
            } else {
                opener.onload = function () {
                    Mautic.stopModalLoadingBar();
                    Mautic.stopIconSpinPostEvent();
                };
            }
        }, 100);
    }
}