<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

$this->setFrameMode(true);

// get element
$arItemFilter = TSolution::GetCurrentElementFilter($arResult['VARIABLES'], $arParams);
$arElement = TSolution\Cache::CIblockElement_GetList(['CACHE' => ['TAG' => TSolution\Cache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'N']], $arItemFilter, false, false, ['ID', 'NAME', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']);

$bHideBackUrl = false;
global $arTheme, $isHideLeftBlock, $isWidePage;

if (isset($arParams['TYPE_LEFT_BLOCK_DETAIL']) && $arParams['TYPE_LEFT_BLOCK_DETAIL'] != 'FROM_MODULE') {
    $arTheme['LEFT_BLOCK']['VALUE'] = $arParams['TYPE_LEFT_BLOCK_DETAIL'];
}

if (isset($arParams['SIDE_LEFT_BLOCK_DETAIL']) && $arParams['SIDE_LEFT_BLOCK_DETAIL'] != 'FROM_MODULE') {
    $arTheme['SIDE_MENU']['VALUE'] = $arParams['SIDE_LEFT_BLOCK_DETAIL'];
}

if ($arTheme['HIDE_SUBSCRIBE']['VALUE'] == 'Y') {
    $arParams['USE_SUBSCRIBE_IN_TOP'] = 'N';
}

if (
    (
        $arParams['LINKED_ELEMENT_TAB_SORT_FIELD'] === 'REGION_PRICE'
        || $arParams['LINKED_ELEMENT_TAB_SORT_FIELD2'] === 'REGION_PRICE'
    )
    && $arParams['SORT_REGION_PRICE']
) {
    global $arRegion;
    $priceSort = '';

    if ($arRegion) {
        if (!$arRegion['PROPERTY_SORT_REGION_PRICE_VALUE'] || $arRegion['PROPERTY_SORT_REGION_PRICE_VALUE'] == 'component') {
            $price = CCatalogGroup::GetList([], ['NAME' => $arParams['SORT_REGION_PRICE']], false, false, ['ID', 'NAME'])->GetNext();
            $priceSort = 'CATALOG_PRICE_'.$price['ID'];
        } else {
            $priceSort = 'CATALOG_PRICE_'.$arRegion['PROPERTY_SORT_REGION_PRICE_VALUE'];
        }
    }

    if ($priceSort) {
        if ($arParams['LINKED_ELEMENT_TAB_SORT_FIELD'] == 'REGION_PRICE') {
            $arParams['LINKED_ELEMENT_TAB_SORT_FIELD'] = $priceSort;
        }

        if ($arParams['LINKED_ELEMENT_TAB_SORT_FIELD2'] == 'REGION_PRICE') {
            $arParams['LINKED_ELEMENT_TAB_SORT_FIELD2'] = $priceSort;
        }
    }
}

if (!$isHideLeftBlock && $APPLICATION->GetProperty('HIDE_LEFT_BLOCK_DETAIL') == 'Y') {
    $APPLICATION->SetPageProperty('HIDE_LEFT_BLOCK', 'Y');
    $APPLICATION->AddViewContent('container_inner_class', ' contents_page ');
    $APPLICATION->AddViewContent('wrapper_inner_class', ' wide_page ');

    if (!$isWidePage) {
        $APPLICATION->AddViewContent('right_block_class', ' maxwidth-theme ');
    }
}
?>

<?if (!$arElement && $arParams['SET_STATUS_404'] !== 'Y'):?>
    <div class="alert alert-warning"><?=GetMessage('ELEMENT_NOTFOUND');?></div>
<?elseif (!$arElement && $arParams['SET_STATUS_404'] === 'Y'):?>
    <?TSolution::goto404Page();?>
<?else:?>
    <?php
    TSolution::AddMeta(
        [
            'og:description' => $arElement['PREVIEW_TEXT'],
            'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE']) : false),
        ]
    );

    /* hide compare link from module options */
    if (TSolution::GetFrontParametrValue('CATALOG_COMPARE') == 'N') {
        $arParams['DISPLAY_COMPARE'] = 'N';
    }
    ?>
    <div class="detail <?= $templateName = $component->{'__template'}->{'__name'};?> fixed_wrapper <?$APPLICATION->ShowProperty('LEFT_BLOCK_CLASSES', '');?>">
        <?if ($arElement):?>
            <?$this->SetViewTarget('product_share');?>

                <?if ($arParams['USE_SHARE'] == 'Y'):?>
                    <?Aspro\Functions\CAsproMax::showShareBlock('top');?>
                <?endif;?>

                <?if ($arParams['USE_RSS'] !== 'N'):?>
                    <div class="colored_theme_hover_bg-block">
                        <?=TSolution::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);?>
                    </div>
                <?endif;?>

                <?if ($arParams['USE_SUBSCRIBE_IN_TOP'] == 'Y'):?>
                    <div>
                        <div class="colored_theme_hover_bg-block dark_link animate-load"
                            data-event="jqm"
                            data-param-type="subscribe"
                            data-name="subscribe"
                            title="<?=GetMessage('SUBSCRIBE_TEXT');?>"
                            >
                            <?=TSolution::showIconSvg('subscribe', SITE_TEMPLATE_PATH.'/images/svg/subscribe_insidepages.svg', '', 'colored_theme_hover_bg-el-svg', true, false);?>
                        </div>
                    </div>
                <?endif;?>
            <?$this->EndViewTarget();?>
        <?endif;?>

        <?// element?>
        <?$sViewElementTemplate = ($arParams['ELEMENT_TYPE_VIEW'] == 'FROM_MODULE' ? $arTheme['PARTNERS_PAGE_DETAIL']['VALUE'] : $arParams['ELEMENT_TYPE_VIEW']);?>
        <?@include_once 'page_blocks/'.$sViewElementTemplate.'.php';?>
    </div>
    <?php
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/animation/animate.min.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/animation/animation_ext.css');
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/jquery.history.js');
    ?>
<?endif;?>
<div style="clear:both"></div>
<?php
if ($arElement['IBLOCK_SECTION_ID']) {
    $arSection = TSolution\Cache::CIBlockSection_GetList(['CACHE' => ['TAG' => TSolution\Cache::GetIBlockCacheTag($arElement['IBLOCK_ID']), 'MULTI' => 'N']], ['ID' => $arElement['IBLOCK_SECTION_ID'], 'ACTIVE' => 'Y'], false, ['ID', 'NAME', 'SECTION_PAGE_URL']);
}
?>
<?$this->SetViewTarget('bottom_links_block');?>
    <div class="bottom-links-block">
        <?// back url?>
        <?TSolution\Functions::showBackUrl([
            'URL' => ((isset($arSection) && $arSection) ? $arSection['SECTION_PAGE_URL'] : $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news']),
            'TEXT' => ($arParams['T_PREV_LINK'] ? $arParams['T_PREV_LINK'] : GetMessage('BACK_LINK')),
        ]);?>

        <?if ($arParams['SHOW_MAX_ELEMENT'] == 'Y' && $arElementNext):?>
            <a class="muted next-url url-block" href="<?=$arElementNext['DETAIL_PAGE_URL'];?>">
                <span class="font_upper next-url-text"><?= $arParams['T_MAX_LINK'] ? $arParams['T_MAX_LINK'] : GetMessage('MAX_LINK');?></span>
                <?=TSolution::showIconSvg('next_element', SITE_TEMPLATE_PATH.'/images/svg/return_to_the_list.svg', '');?>
            </a>
        <?endif;?>

        <?if ($arParams['USE_SHARE'] == 'Y' && $arElement):?>
            <?TSolution\Functions::showShareBlock('bottom');?>
        <?endif;?>
    </div>
<?$this->EndViewTarget();?>

<?if (Bitrix\Main\Loader::includeModule('sotbit.seometa')):?>
    <?$APPLICATION->IncludeComponent(
        'sotbit:seo.meta',
        '.default',
        [
            'FILTER_NAME' => $arParams['FILTER_NAME'],
            'SECTION_ID' => $arSection['ID'],
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
        ]
    );?>
<?endif;?>
