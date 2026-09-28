<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}?>

<?// options from \Aspro\Functions\CAsproMax::showBlockHtml?>
<?php

$arItem = $arConfig['ITEM'];

if (!$arItem) {
    return;
}?>

<div class="authors_block">
    <div class="side-block side-block--margined bordered box-shadow rounded2 colored_theme_hover_bg-block">
        <?if(!empty($arItem['UF_FILE']['src'])):?>
            <img src="<?= $arItem['UF_FILE']['src']; ?>" alt="<?= $arItem['UF_NAME']; ?>" class="side-block-author__img rounded">
        <?endif;?>
        <div class="side-block__text item-title">
            <div class="darken font_md option-font-bold props"><?= $arItem['UF_NAME']; ?></div>
            <div class="font_xs"><?= $arItem['UF_FULL_DESCRIPTION']; ?></div>
        </div>
        <div class="side-block__bottom  font_upper"><a href="<?= $arConfig['BACK_LINK']; ?>"><?= GetMessage('BACK_LINK_AUTHOR'); ?></a></div>
    </div>
</div>
