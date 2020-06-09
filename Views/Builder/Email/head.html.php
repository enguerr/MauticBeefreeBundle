<?php
if (!isset($mauticContent)) {
    $mauticContent = '';
}
?>
<script>
    var mauticBasePath    = '<?php echo $app->getRequest()->getBasePath(); ?>';
    var mauticBaseUrl     = '<?php echo $view['router']->path('mautic_base_index'); ?>';
    var mauticAjaxUrl     = '<?php echo $view['router']->path('mautic_core_ajax'); ?>';
    var mauticAjaxCsrf    = '<?php echo $view['security']->getCsrfToken('mautic_ajax_post'); ?>';
    var mauticAssetPrefix = '<?php echo $view['assets']->getAssetPrefix(true); ?>';
    var mauticContent     = '<?php echo $mauticContent; ?>';
    var mauticEnv         = '<?php echo $app->getEnvironment(); ?>';
    var mauticLang        = <?php echo $view['translator']->getJsLang(); ?>;
</script>
<?php $view['assets']->outputSystemScripts(false); ?>

<link rel="stylesheet" href="<?php echo $view['assets']->getUrl('/plugins/MauticGrapeJsBundle/Assets/css/atwho.css') ?>">
<link rel="stylesheet" href="//unpkg.com/grapesjs@0.14.57/dist/css/grapes.min.css">
<link rel="stylesheet" href="https://unpkg.com/grapesjs-preset-newsletter@0.2.20/dist/grapesjs-preset-newsletter.css">

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="//unpkg.com/grapesjs@0.14.57/dist/grapes.min.js"></script>
<script src="https://unpkg.com/grapesjs-parser-postcss"></script>
<script src="https://unpkg.com/grapesjs-preset-newsletter@0.2.20/dist/grapesjs-preset-newsletter.min.js"></script>