<?php
/**
 * @package     Mautic
 * @copyright   2020 Enguerr. All rights reserved
 * @author      Enguerr
 * @link        https://www.enguer.com
 * @license     GNU/AGPLv3 http://www.gnu.org/licenses/agpl.html
 */


$codeMode   = 'mautic_code_mode';
$isCodeMode = ($active == $codeMode);
?>
<style>
    .bf-item {
        position: relative;
        width: 100%;
        height: 300px;
        background-color: #fff;
        overflow: hidden;
    }
    .bf-item:after {
        content: '';
        display: block;
        background-color: #fcfcfc;
        opacity: 0.9;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        -webkit-transform: scale(2) translateX(-75%) translateY(-75%) translateZ(0) rotate(-28deg);
        transform: scale(2) translateX(-75%) translateY(-75%) translateZ(0) rotate(-28deg);
        -webkit-transition: -webkit-transform 3s cubic-bezier(0.23, 1, 0.32, 1);
        transition: -webkit-transform 3s cubic-bezier(0.23, 1, 0.32, 1);
        transition: transform 3s cubic-bezier(0.23, 1, 0.32, 1);
        transition: transform 3s cubic-bezier(0.23, 1, 0.32, 1), -webkit-transform 3s cubic-bezier(0.23, 1, 0.32, 1);
    }
    .bf-item:hover:after {
        -webkit-transform: scale(2) translateX(0%) translateY(0%) translateZ(0) rotate(-28deg);
        transform: scale(2) translateX(0%) translateY(0%) translateZ(0) rotate(-28deg);
    }
    .bf-item:hover .bf-item-image {
        -webkit-transform: scale(1.2) translateZ(0);
        transform: scale(1.2) translateZ(0);
    }
    .bf-item:hover .bf-item-text {
        opacity: 1;
        -webkit-transform: translateY(0);
        transform: translateY(0);
    }

    .bf-item-image {
        height: auto;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
        -webkit-transition: -webkit-transform 750ms cubic-bezier(0.23, 1, 0.32, 1);
        transition: -webkit-transform 750ms cubic-bezier(0.23, 1, 0.32, 1);
        transition: transform 750ms cubic-bezier(0.23, 1, 0.32, 1);
        transition: transform 750ms cubic-bezier(0.23, 1, 0.32, 1), -webkit-transform 750ms cubic-bezier(0.23, 1, 0.32, 1);
    }
    .bf-item-image::before {
        content: "";
        display: block;
        padding-top: 75%;
        overflow: hidden;
    }
    .bf-item-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: auto;
        line-height: 0;
    }

    .bf-item-text {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        opacity: 0;
        text-align: center;
        z-index: 1;
        color: #666;
        -webkit-transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), -webkit-transform 500ms cubic-bezier(0.23, 1, 0.32, 1);
        transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), -webkit-transform 500ms cubic-bezier(0.23, 1, 0.32, 1);
        transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);
        transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1), -webkit-transform 500ms cubic-bezier(0.23, 1, 0.32, 1);
        -webkit-transition-delay: 300ms;
        transition-delay: 300ms;
        -webkit-transform: translateY(-20%);
        transform: translateY(-20%);
    }

    .bf-item-text-wrapper {
        width: 100%;
        position: absolute;
        top: 50%;
        -webkit-transform: translateY(-50%);
        transform: translateY(-50%);
    }

    .bf-item-text-title {
        font-weight: normal;
        font-style: 16px;
        padding: 0 15px;
        margin: 5px 0 0 0;
    }

    .bf-item-text-dek {
        text-transform: uppercase;
        font-style: 14px;
        opacity: 0.7;
        margin: 0;
    }

    .bf-item-link {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 2;
        line-height: 0;
        overflow: hidden;
        text-indent: -9999px;
    }
    .fb-icon {
        color: #ccc;
        position: absolute;
        top: 100px;
        left: calc( 50% - 40px );
        font-size: 8em;
    }
</style>
<div class="row">
    <?php if ($version) : ?>
    <div class="col-md-3 beefree-theme-list">
        <div class="panel panel-default">
            <div class="panel-body text-center">
                <div class="bf-item">
                    <div class="bf-item-image">
                        <i class="fa fa-edit text-muted fb-icon" aria-hidden="true" ></i>
                    </div>
                    <div class="bf-item-text">
                        <div class="bf-item-text-wrapper">
                            <p class="bf-item-text-dek"><?php echo $version->getName(); ?></p>
                            <h2 class="bf-item-text-title"><?php echo $view['translator']->trans('mautic.beefree.current.title'); ?></h2>
                            <a href="#" type="button" data-theme-beefree="current" class="btn btn-default bf-item-text-title" style="margin: 50px auto;background-color: rgba(255,255,255,0.8);padding: 20px;">
                                <?php echo $view['translator']->trans('mautic.beefree.current'); ?>
                            </a>
                        </div>
                    </div>
                    <a href="#" type="button" data-theme-beefree="current" class="bf-item-link select-theme-link btn-dnd btn-nospin text-success btn-builder btn-copy " onclick="Mautic.launchCustomBuilder('emailform', 'email',this);">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-md-3 beefree-theme-list">
        <div class="panel panel-default ">
            <div class="panel-body text-center">
                <div class="bf-item">
                    <div class="bf-item-image">
                        <i class="fa fa-file text-muted fb-icon" aria-hidden="true" ></i>
                    </div>
                    <div class="bf-item-text">
                        <div class="bf-item-text-wrapper">
                            <p class="bf-item-text-dek">empty-model</p>
                            <h2 class="bf-item-text-title"><?php echo $view['translator']->trans('mautic.beefree.from-scratch'); ?></h2>
                            <a href="#" type="button" data-theme-beefree="new" class="btn btn-default bf-item-text-title" style="margin: 50px auto;background-color: rgba(255,255,255,0.5);padding: 20px;">
                                <?php echo $view['translator']->trans('mautic.beefree.from-scratch'); ?>
                            </a>
                        </div>
                    </div>
                    <a href="#" type="button" data-theme-beefree="new" class="bf-item-link select-theme-link btn-dnd btn-nospin text-success btn-builder btn-copy " onclick="Mautic.launchCustomBuilder('emailform', 'email',this);">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php if ($bfthemes) : ?>
        <?php foreach ($bfthemes as $themeInfo) : ?>

            <?php
                $themeKey = $themeInfo->getName();
                $isSelected = ($active === $themeKey);
            ?>

            <?php $thumbnailUrl = '';//$view['assets']->getUrl($themeInfo['themesLocalDir'].'/'.$themeKey.'/'.$thumbnailName); ?>
            <div class="col-md-3 beefree-theme-list">
                <div class="panel panel-default <?php echo $isSelected ? 'beefree-selected' : ''; ?>">
                    <div class="panel-body text-center">

                        <div class="bf-item">
                            <div class="bf-item-image">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($themeInfo->getPreview()); ?>" alt="" />
                            </div>
                            <div class="bf-item-text">
                                <div class="bf-item-text-wrapper">
                                    <p class="bf-item-text-dek"><?php echo $themeInfo->getName(); ?></p>
                                    <h2 class="bf-item-text-title"><?php echo $themeInfo->getTitle(); ?></h2>
                                    <a href="#" type="button" data-theme-beefree="<?php echo $themeKey; ?>" class="btn btn-default bf-item-text-title" style="margin: 50px auto;background-color: rgba(255,255,255,0.5);padding: 20px;">
                                        <?php echo $view['translator']->trans('mautic.beefree.builder'); ?>
                                    </a>
                                </div>
                            </div>
                            <a href="#" type="button" data-theme-beefree="<?php echo $themeKey; ?>" class="bf-item-link select-theme-link btn-dnd btn-nospin text-success btn-builder btn-copy " onclick="Mautic.launchCustomBuilder('emailform', 'email',this);">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="clearfix"></div>
</div>
