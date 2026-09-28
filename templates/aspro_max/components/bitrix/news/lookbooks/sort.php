<?php

use Bitrix\Main\Localization\Loc;

if (!include_once ($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new SystemException('Error include solution constants');
}

$arDisplays = ['block', 'list', 'table'];

$application = \Bitrix\Main\Application::getInstance();
$context = $application->getContext();
$request = $context->getRequest();
$session = $application->getSession();

$templateName = $this->__name;

if ($arParams['ONLY_ELEMENT_DISPLAY_VARIANT'] == 'Y' && $arElement['DISPLAY_TEMPLATE']) {
    $display = $arElement['DISPLAY_TEMPLATE'];
    $hideVariantButtons = true;
} else {
    $display = 'block';

    if ($request->get('display')) {
        $requestedDisplay = trim($request->get('display'));
        if (in_array($requestedDisplay, $arDisplays, true)) {
            $display = htmlspecialcharsbx($requestedDisplay);
            $session->set('display', $display);
        }
    } elseif ($session->has('display')) {
        $sessionDisplay = trim($session->get('display'));
        if (in_array($sessionDisplay, $arDisplays, true)) {
            $display = $sessionDisplay;
        }
    } elseif (!empty($arElement['DISPLAY_TEMPLATE'])) {
        $display = $arElement['DISPLAY_TEMPLATE'];
    } elseif (!empty($arParams['DEFAULT_LIST_TEMPLATE'])) {
        $display = $arParams['DEFAULT_LIST_TEMPLATE'];
    }
}

$template = 'catalog_'.$display;

$bShowFilter = false;
$arTheme['FILTER_VIEW']['VALUE'] = 'VERTICAL';
?>
<div class="filter-panel sort_header view_<?=$display;?>">
    <?if ($bShowFilter):?>
        <div class="filter-panel__filter pull-left filter-<?=strtolower($arTheme['FILTER_VIEW']['VALUE']);?> <?=$bHideLeftBlock ? 'filter-panel__filter--visible' : '';?>">
            <div class="bx-filter-title filter_title <?=$bActiveFilter && $bActiveFilter[1] != 'clear' ? 'active-filter' : '';?>">
                <?=TSolution::showIconSvg('icon', SITE_TEMPLATE_PATH.'/images/svg/catalog/filter.svg', '', '', true, false);?>
                <span class="font_upper_md font-bold darken dotted"><?=Loc::getMessage('CATALOG_SMART_FILTER_TITLE');?></span>
            </div>
            <div class="controls-hr"></div>
        </div>
    <?endif;?>
    <!--noindex-->
        <div class="filter-panel__sort pull-left">
            <?php
            $catalogSort = new TSolution\Template\CatalogSort(
                params: $arParams,
                template: $templateName,
                sortField: mb_strtoupper($arParams['LINKED_ELEMENT_TAB_SORT_FIELD'] ?? 'sort'),
                orderField: mb_strtolower($arParams['LINKED_ELEMENT_TAB_SORT_ORDER'] ?? 'asc')
            );

            $arAvailableSort = $catalogSort->getAvailableSort();

            $sort = $catalogSort->getSort();
            $sort_order = $catalogSort->getOrder();

            $arDelUrlParams = ['sort', 'order', 'control_ajax', 'ajax_get_filter', 'linerow', 'display'];
            ?>
            <?if ($arAvailableSort):?>
                <div class="dropdown-select">
                    <div class="dropdown-select__title font_xs darken">
                        <span><?=$catalogSort->getCurrentSortTitle();?></span>
                        <?=TSolution::showIconSvg('down', SITE_TEMPLATE_PATH.'/images/svg/trianglearrow_down.svg', '', '', true, false);?>
                    </div>
                    <div class="dropdown-select__list dropdown-menu-wrapper" role="menu">
                        <div class="dropdown-menu-inner rounded3">
                            <?foreach ($arAvailableSort as $key => $arSort):?>
                                <?foreach ($arSort['ORDER_VALUES'] as $order => $title):?>
                                    <div class="dropdown-select__list-item font_xs">
                                        <?if ($arSort['CURRENT'] && $arSort['CURRENT'] === $order):?>
                                            <span class="dropdown-select__list-link dropdown-select__list-link--current">
                                                <span><?=$arSort['ORDER_VALUES'][$order];?></span>
                                            </span>
                                        <?else:?>
                                            <?php
                                            $newSort = $sort_order == 'desc' ? 'asc' : 'desc';
                                            $current_url = $APPLICATION->GetCurPageParam('sort='.$arSort['KEY'].'&order='.$order, $arDelUrlParams);
                                            $url = $current_url;

                                            $linkClassList = [$sort_order, $key, 'darken'];
                                            if ($arParams['AJAX_FILTER_CATALOG'] === 'Y') {
                                                $linkClassList[] = 'js-load-link';
                                            }
                                            ?>
                                            <a class="dropdown-select__list-link <?=TSolution\Utils::implodeClasses($linkClassList);?>"
                                                href="<?=$url;?>"
                                                data-url="<?=$url;?>"
                                                rel="nofollow"
                                                >
                                                <span><?=$arSort['ORDER_VALUES'][$order];?></span>
                                            </a>
                                        <?endif;?>
                                    </div>
                                <?endforeach;?>
                            <?endforeach;?>
                        </div>
                    </div>
                </div>
            <?endif;?>
            <?$sort = $catalogSort->getSortForFilter();?>
        </div>
        <div class="filter-panel__view controls-view pull-right <?=$hideVariantButtons ? 'hidden' : '';?>">
            <?foreach ($arDisplays as $displayType):?>
                <?php
                $current_url = '';
                $current_url = $APPLICATION->GetCurPageParam('display='.$displayType, $arDelUrlParams);
                // $url = str_replace('+', '%2B', $current_url);
                $url = $current_url;
                ?>
                <?if ($display == $displayType):?>
                    <span title="<?=Loc::getMessage('SECT_DISPLAY_'.strtoupper($displayType));?>" class="controls-view__link controls-view__link--<?=$displayType;?> controls-view__link--current"><?=TSolution::showIconSvg('type', SITE_TEMPLATE_PATH.'/images/svg/catalog/'.$displayType.'type.svg', '', '', true, false);?></span>
                <?else:?>
                    <a rel="nofollow" href="<?=$url;?>" data-url="<?=$url;?>" title="<?=Loc::getMessage('SECT_DISPLAY_'.strtoupper($displayType));?>" class="controls-view__link controls-view__link--<?=$displayType;?> muted<?=$arParams['AJAX_FILTER_CATALOG'] == 'Y' ? ' js-load-link' : '';?>"><?=TSolution::showIconSvg('type', SITE_TEMPLATE_PATH.'/images/svg/catalog/'.$displayType.'type.svg', '', '', true, false);?></a>
                <?endif;?>
            <?endforeach;?>
        </div>
        <?if ($display === 'block'):?>
            <div class="filter-panel__view controls-linecount pull-right">
                <?php
                $arLineCount = [3, 4];
                $linerow = 4;

                if ($request->get('linerow')) {
                    $requestedLinerow = trim($request->get('linerow'));
                    if (in_array($requestedLinerow, $arLineCount)) {
                        $linerow = $requestedLinerow;
                        $session->set('linerow', $linerow);
                    }
                } elseif ($session->has('linerow')) {
                    $sessionLinerow = trim($session->get('linerow'));
                    if (in_array($sessionLinerow, $arLineCount)) {
                        $linerow = $sessionLinerow;
                    }
                } elseif (!empty($arParams['LINE_ELEMENT_COUNT']) && in_array($arParams['LINE_ELEMENT_COUNT'], $arLineCount)) {
                    $linerow = $arParams['LINE_ELEMENT_COUNT'];
                }
                ?>

                <?foreach ($arLineCount as $value):?>
                    <?php
                    $current_url = '';
                    $current_url = $APPLICATION->GetCurPageParam('linerow='.$value, $arDelUrlParams);
                    $url = str_replace('+', '%2B', $current_url);
                    ?>
                    <?if ($linerow == $value):?>
                        <span title="<?=Loc::getMessage('SECT_DISPLAY_'.$value);?>" class="controls-view__link controls-view__link--current"><?=TSolution::showIconSvg('type', SITE_TEMPLATE_PATH.'/images/svg/catalog/'.$value.'inarow.svg', '', '', true, false);?></span>
                    <?else:?>
                        <a rel="nofollow" href="<?=$url;?>" data-url="<?=$url;?>" title="<?=Loc::getMessage('SECT_DISPLAY_'.$value);?>" class="controls-view__link muted<?=$arParams['AJAX_FILTER_CATALOG'] == 'Y' ? ' js-load-link' : '';?>"><?=TSolution::showIconSvg('type', SITE_TEMPLATE_PATH.'/images/svg/catalog/'.$value.'inarow.svg', '', '', true, false);?></a>
                    <?endif;?>
                <?endforeach;?>
                <div class="controls-hr"></div>
            </div>
        <?endif;?>
        <div class="clearfix"></div>
    <!--/noindex-->
</div>
