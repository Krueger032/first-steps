<?

if ($arParams['USE_FILTER'] == 'Y') {
    $hideLeftBlock = $APPLICATION->GetProperty('HIDE_LEFT_BLOCK');
    $viewFilter = TSolution::GetFrontParametrValue('FILTER_VIEW');
    $bSmartFilterExpanded = TSolution::GetFrontParametrValue('COMPACT_FILTER_HIDE_LEFT_BLOCK') === 'Y';

    $template_filter = 'main';
    if ($viewFilter === 'COMPACT') {
        $template_filter .= '_compact';

        if ($hideLeftBlock === 'Y' && !$bSmartFilterExpanded) {
            $template_filter = 'main';
        }
    } elseif ($viewFilter === 'VERTICAL') {
        if ($bSmartFilterExpanded && $hideLeftBlock === 'Y') {
            $template_filter .= '_compact';
        }
    }

    if ($arParams['AJAX_FILTER_CATALOG'] === 'Y') {
        $template_filter .= '_ajax';
    }

    $TOP_VERTICAL_FILTER_PANEL = $arTheme['LEFT_BLOCK_CATALOG_SECTIONS']['VALUE'] == 'Y' ? $arTheme['FILTER_VIEW']['DEPENDENT_PARAMS']['TOP_VERTICAL_FILTER_PANEL']['VALUE'] : 'N';

    TSolution\CacheableUrl::addSmartFilterNameParam($arParams['FILTER_NAME']);
    $GLOBALS['preFilterCatalog'] = TSolution\Regionality::mergeSmartPreFilterWithRegionFilter($GLOBALS['preFilterCatalog'] ?? []);
    TSolution::makeElementFilterInRegion($GLOBALS['preFilterCatalog']);

    $APPLICATION->IncludeComponent(
        'bitrix:catalog.smart.filter',
        $template_filter,
        [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'AJAX_FILTER_FLAG' => (isset($isAjaxFilter) ? $isAjaxFilter : ''),
            'SECTION_ID' => (isset($arSection['ID']) ? $arSection['ID'] : ''),
            'FILTER_NAME' => $arParams['FILTER_NAME'],
            'PREFILTER_NAME' => 'preFilterCatalog',
            'PRICE_CODE' => ($arParams['USE_FILTER_PRICE'] == 'Y' ? $arParams['FILTER_PRICE_CODE'] : $arParams['PRICE_CODE']),
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'CACHE_NOTES' => '',
            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
            'SAVE_IN_SESSION' => 'N',
            'XML_EXPORT' => 'Y',
            'SECTION_TITLE' => 'NAME',
            'SECTION_DESCRIPTION' => 'DESCRIPTION',
            'SHOW_HINTS' => $arParams['SHOW_HINTS'],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'DISPLAY_ELEMENT_COUNT' => $arParams['DISPLAY_ELEMENT_COUNT'],
            'INSTANT_RELOAD' => 'Y',
            'VIEW_MODE' => strtolower($viewFilter),
            'SEF_MODE' => (strlen($arResult['URL_TEMPLATES']['smart_filter']) ? 'Y' : 'N'),
            'SEF_RULE' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['smart_filter'],
            'SMART_FILTER_PATH' => $arResult['VARIABLES']['SMART_FILTER_PATH'],
            'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
            'SEF_RULE_FILTER' => $arResult['URL_TEMPLATES']['smart_filter'],
            'SORT_BUTTONS' => $arParams['SORT_BUTTONS'],
            'SORT_PRICES' => $arParams['SORT_PRICES'],
            'AVAILABLE_SORT' => $arAvailableSort,
            'SORT' => $sort,
            'SORT_ORDER' => $sort_order,
            'TOP_VERTICAL_FILTER_PANEL' => $TOP_VERTICAL_FILTER_PANEL,
            'SHOW_SORT' => ($arParams['SHOW_SORT_IN_FILTER'] != 'N'),
            'STORES' => $arParams['STORES'],
        ],
        $component);
}
