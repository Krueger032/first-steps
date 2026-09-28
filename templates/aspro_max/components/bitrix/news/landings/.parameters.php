<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

use Bitrix\Main\Web\Json;

CBitrixComponent::includeComponentClass('bitrix:catalog.section');

$siteId = '';

$arProperty_S = $arProperty_XL = $arProperty_N = $arProperty_ALL = [];
if (Bitrix\Main\Loader::includeModule('iblock')) {
    $arProperty = $arPropertyF = [];
    $rsProp = CIBlockProperty::GetList(['sort' => 'asc', 'name' => 'asc'], ['IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y']);
    while ($arr = $rsProp->Fetch()) {
        if ($arr['PROPERTY_TYPE'] != 'F') {
            $arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
        } else {
            $arPropertyF[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
        }
    }

    if (intval($arCurrentValues['IBLOCK_CATALOG_ID']) > 0) {
        $rsProp = CIBlockProperty::GetList(
            ['sort' => 'asc', 'name' => 'asc'],
            ['IBLOCK_ID' => $arCurrentValues['IBLOCK_CATALOG_ID'], 'ACTIVE' => 'Y']
        );
        while ($arProp = $rsProp->Fetch()) {
            if (!$arProp['CODE']) {
                $arProp['CODE'] = $arProp['ID'];
            }

            $propValue = '['.$arProp['CODE'].'] '.$arProp['NAME'];

            $arProperty_ALL[$arProp['CODE']] = $propValue;
            if ($arProp['PROPERTY_TYPE'] == 'S') {
                $arProperty_S[$arProp['CODE']] = $propValue;
            } elseif ($arProp['MULTIPLE'] == 'Y' && $arProp['PROPERTY_TYPE'] == 'L') {
                $arProperty_XL[$arProp['CODE']] = $propValue;
            }

            if ($arProp['PROPERTY_TYPE'] == 'F') {
                $arFilePropListCatalog[$arProp['CODE']] = '['.$arProp['ID'].']'.($arProp['CODE'] != '' ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
            }
        }
    }

    $arIBlocks = [];
    $db_iblock = CIBlock::GetList(['SORT' => 'ASC'], ['SITE_ID' => $_REQUEST['site'], 'TYPE' => ($arCurrentValues['IBLOCK_CATALOG_TYPE'] != '-' ? $arCurrentValues['IBLOCK_CATALOG_TYPE'] : '')]);
    while ($arRes = $db_iblock->Fetch()) {
        $arIBlocks[$arRes['ID']] = $arRes['NAME'].' ['.$arRes['CODE'].']';
    }

    $arTypesEx = CIBlockParameters::GetIBlockTypes(['-' => ' ']);
    $request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    $siteId = $request->get('src_site') ?? $request->get('site');
}

$arPrice = [];
$arSort = CIBlockParameters::GetElementSortFields(
    ['SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'],
    ['KEY_LOWERCASE' => 'Y']
);
if (Bitrix\Main\Loader::includeModule('catalog')) {
    $arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
    $rsPrice = CCatalogGroup::GetList($v1 = 'sort', $v2 = 'asc');
    while ($arr = $rsPrice->Fetch()) {
        $arPrice[$arr['NAME']] = '['.$arr['NAME'].'] '.$arr['NAME_LANG'];
    }
    if ((isset($arCurrentValues['IBLOCK_CATALOG_ID']) && (int) $arCurrentValues['IBLOCK_CATALOG_ID']) > 0) {
        $arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_CATALOG_ID']);
        $boolSKU = !empty($arSKU) && is_array($arSKU);
    }
} else {
    $arPrice = $arProperty_N;
}

$arAscDesc = [
    'asc' => GetMessage('IBLOCK_SORT_ASC'),
    'desc' => GetMessage('IBLOCK_SORT_DESC'),
];

$arRegionPrice = $arPrice;
$arPrice = array_merge(['MINIMUM_PRICE' => GetMessage('SORT_PRICES_MINIMUM_PRICE'), 'MAXIMUM_PRICE' => GetMessage('SORT_PRICES_MAXIMUM_PRICE'), 'REGION_PRICE' => GetMessage('SORT_PRICES_REGION_PRICE')], $arPrice);

// get offers iblock properties and group by types
if ($boolSKU) {
    $arAllOfferPropList = [];
    $arFileOfferPropList = [
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY'),
    ];
    $arTreeOfferPropList = [
        '-' => GetMessage('CP_BC_TPL_PROP_EMPTY'),
    ];
    $rsProps = CIBlockProperty::GetList(
        ['SORT' => 'ASC', 'ID' => 'ASC'],
        ['IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y']
    );
    while ($arProp = $rsProps->Fetch()) {
        if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID']) {
            continue;
        }
        $arProp['USER_TYPE'] = (string) $arProp['USER_TYPE'];
        $strPropName = '['.$arProp['ID'].']'.($arProp['CODE'] != '' ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
        if ($arProp['CODE'] == '') {
            $arProp['CODE'] = $arProp['ID'];
        }

        $arProperty_Offers[$arProp['CODE']] = $strPropName;

        if ($arProp['PROPERTY_TYPE'] == 'F') {
            $arFileOfferPropList[$arProp['CODE']] = $strPropName;
        }
        if ($arProp['MULTIPLE'] != 'N') {
            continue;
        }
        if (
            $arProp['PROPERTY_TYPE'] == 'L'
            || $arProp['PROPERTY_TYPE'] == 'E'
            || ($arProp['PROPERTY_TYPE'] == 'S' && $arProp['USER_TYPE'] == 'directory' && CIBlockPriceTools::checkPropDirectory($arProp))
        ) {
            $arTreeOfferPropList[$arProp['CODE']] = $strPropName;
        }
    }
}

/* get component template pages & params array */
$arPageBlocksParams = [];
if (Bitrix\Main\Loader::includeModule('aspro.max')) {
    $arPageBlocks = CMax::GetComponentTemplatePageBlocks(__DIR__);
    $arPageBlocksParams = CMax::GetComponentTemplatePageBlocksParams($arPageBlocks);
    $arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'] = $arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'] === 'FROM_MODULE' ? CMax::GetFrontParametrValue('LANDINGS_PAGE', $siteId) : $arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'];
    CMax::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams); // add option value FROM_MODULE
}

$arListView = [
    'slider' => GetMessage('SLIDER_VIEW'),
    'block' => GetMessage('BLOCK_VIEW'),
];

$arTemplateParameters = array_merge($arPageBlocksParams, [
    'T_GOODS' => [
        'SORT' => 704,
        'NAME' => GetMessage('T_GOODS'),
        'TYPE' => 'TEXT',
        'DEFAULT' => '',
    ],
    'USE_SUBSCRIBE_IN_TOP' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'SORT' => 600,
        'NAME' => GetMessage('USE_SUBSCRIBE_IN_TOP'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ],
    'T_GOODS_SECTION' => [
        'SORT' => 704,
        'NAME' => GetMessage('T_GOODS_SECTION'),
        'TYPE' => 'TEXT',
        'DEFAULT' => '',
    ],
    'T_GALLERY' => [
        'SORT' => 704,
        'NAME' => GetMessage('T_GALLERY'),
        'TYPE' => 'TEXT',
        'DEFAULT' => '',
    ],
    'LINKED_PRODUCTS_PROPERTY' => [
        'NAME' => GetMessage('LINKED_PRODUCTS_PROPERTY'),
        'TYPE' => 'LIST',
        'PARENT' => 'DETAIL_SETTINGS',
        'VALUES' => $arProperty,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'BRAND',
    ],
    'SHOW_LINKED_PRODUCTS' => [
        'NAME' => GetMessage('SHOW_LINKED_PRODUCTS'),
        'TYPE' => 'CHECKBOX',
        'PARENT' => 'DETAIL_SETTINGS',
        'DEFAULT' => 'N',
    ],
    'LINKED_ELEMENST_PAGE_COUNT' => [
        'SORT' => 704,
        'NAME' => GetMessage('LINKED_ELEMENST_PAGE_COUNT'),
        'TYPE' => 'TEXT',
        'PARENT' => 'DETAIL_SETTINGS',
        'DEFAULT' => '20',
    ],
    'SHOW_MEASURE' => [
        'NAME' => GetMessage('SHOW_MEASURE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'AJAX_FILTER_CATALOG' => [
        'NAME' => GetMessage('AJAX_FILTER_CATALOG_TITLE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'DEFAULT_LIST_TEMPLATE' => [
        'NAME' => GetMessage('DEFAULT_LIST_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => ['block' => GetMessage('DEFAULT_LIST_TEMPLATE_BLOCK'), 'list' => GetMessage('DEFAULT_LIST_TEMPLATE_LIST'), 'table' => GetMessage('DEFAULT_LIST_TEMPLATE_TABLE')],
        'DEFAULT' => 'block',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_UNABLE_SKU_PROPS' => [
        'NAME' => GetMessage('SHOW_UNABLE_SKU_PROPS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_ARTICLE_SKU' => [
        'NAME' => GetMessage('SHOW_ARTICLE_SKU'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_MEASURE_WITH_RATIO' => [
        'PARENT' => 'VISUAL',
        'NAME' => GetMessage('SHOW_MEASURE_WITH_RATIO'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_DISCOUNT_PERCENT' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ],
    'SHOW_DISCOUNT_PERCENT_NUMBER' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT_NUMBER'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ],
    'ALT_TITLE_GET' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('ALT_TITLE_GET_TITLE'),
        'VALUES' => ['SEO' => GetMessage('ALT_TITLE_GET_SEO'), 'NORMAL' => GetMessage('ALT_TITLE_GET_NORMAL')],
        'TYPE' => 'LIST',
        'DEFAULT' => 'NORMAL',
    ],
    'SHOW_DISCOUNT_TIME' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('SHOW_DISCOUNT_TIME'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ],
    'SHOW_DISCOUNT_TIME_EACH_SKU' => [
        'NAME' => GetMessage('SHOW_DISCOUNT_TIME_EACH_SKU'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'SORT' => 100,
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_RATING' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('SHOW_RATING'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ],
    'DISPLAY_COMPARE' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('DISPLAY_COMPARE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ],
    'DISPLAY_WISH_BUTTONS' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('DISPLAY_WISH_BUTTONS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ],
    'SHOW_OLD_PRICE' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ],
    'ADD_PROPERTIES_TO_BASKET' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_ADD_PROPERTIES_TO_BASKET'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y',
    ],
    'PRODUCT_PROPS_VARIABLE' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_PRODUCT_PROPS_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'prop',
        'HIDDEN' => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
    ],
    'PARTIAL_PRODUCT_PROPERTIES' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_PARTIAL_PRODUCT_PROPERTIES'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'HIDDEN' => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
    ],
    'PRODUCT_PROPERTIES' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_PRODUCT_PROPERTIES'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arProperty_ALL,
        'HIDDEN' => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
    ],
    'LIST_PROPERTY_CATALOG_CODE' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'SORT' => 100,
        'NAME' => GetMessage('CP_BC_LIST_PRODUCT_PROPERTY_CODE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => $arProperty_ALL,
    ],
    'SORT_BUTTONS' => [
        'SORT' => 100,
        'NAME' => GetMessage('SORT_BUTTONS'),
        'VALUES' => [
            'POPULARITY' => GetMessage('SORT_BUTTONS_POPULARITY'),
            'NAME' => GetMessage('SORT_BUTTONS_NAME'),
            'PRICE' => GetMessage('SORT_BUTTONS_PRICE'),
            'QUANTITY' => GetMessage('SORT_BUTTONS_QUANTITY'),
            'CUSTOM' => GetMessage('SORT_BUTTONS_CUSTOM'),
        ] + $arProperty_ALL,
        'DEFAULT' => ['POPULARITY', 'NAME', 'PRICE'],
        'PARENT' => 'DETAIL_SETTINGS',
        'TYPE' => 'LIST',
        'REFRESH' => 'Y',
        'MULTIPLE' => 'Y',
    ],
    'USE_SHARE' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'SORT' => 600,
        'NAME' => GetMessage('USE_SHARE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ],
    'SIDE_LEFT_BLOCK' => [
        'PARENT' => 'BASE',
        'NAME' => GetMessage('SIDE_LEFT_BLOCK_NAME'),
        'TYPE' => 'LIST',
        'VALUES' => ['LEFT' => GetMessage('T_LEFT'), 'RIGHT' => GetMessage('T_RIGHT'), 'FROM_MODULE' => GetMessage('FROM_MODULE_PARAMS')],
        'DEFAULT' => 'FROM_MODULE',
    ],
    'TYPE_LEFT_BLOCK' => [
        'PARENT' => 'BASE',
        'NAME' => GetMessage('TYPE_LEFT_BLOCK_NAME'),
        'TYPE' => 'LIST',
        'VALUES' => ['FROM_MODULE' => GetMessage('FROM_MODULE_PARAMS'), '1' => GetMessage('T_FULL'), '2' => GetMessage('T_TYPE_LEFT_BLOCK_2'), '3' => GetMessage('T_TYPE_LEFT_BLOCK_3'), '4' => GetMessage('T_TYPE_LEFT_BLOCK_4')],
        'DEFAULT' => 'FROM_MODULE',
    ],
    'SIDE_LEFT_BLOCK_DETAIL' => [
        'PARENT' => 'BASE',
        'NAME' => GetMessage('SIDE_LEFT_BLOCK_DETAIL_NAME'),
        'TYPE' => 'LIST',
        'VALUES' => ['LEFT' => GetMessage('T_LEFT'), 'RIGHT' => GetMessage('T_RIGHT'), 'FROM_MODULE' => GetMessage('FROM_MODULE_PARAMS')],
        'DEFAULT' => 'FROM_MODULE',
    ],
    'TYPE_LEFT_BLOCK_DETAIL' => [
        'PARENT' => 'BASE',
        'NAME' => GetMessage('TYPE_LEFT_BLOCK_DETAIL_NAME'),
        'TYPE' => 'LIST',
        'VALUES' => ['FROM_MODULE' => GetMessage('FROM_MODULE_PARAMS'), '1' => GetMessage('T_FULL'), '2' => GetMessage('T_TYPE_LEFT_BLOCK_2'), '3' => GetMessage('T_TYPE_LEFT_BLOCK_3'), '4' => GetMessage('T_TYPE_LEFT_BLOCK_4')],
        'DEFAULT' => 'FROM_MODULE',
    ],
    'GALLERY_TYPE' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('GALLERY_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => ['small' => GetMessage('GALLERY_SMALL'), 'big' => GetMessage('GALLERY_BIG')],
        'DEFAULT' => 'small',
    ],
    'STAFF_TYPE_DETAIL' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('STAFF_TYPE_DETAIL_NAME'),
        'TYPE' => 'LIST',
        'VALUES' => ['list' => GetMessage('T_LIST'), 'block' => GetMessage('T_BLOCK')],
        'DEFAULT' => 'list',
    ],
    'IBLOCK_LINK_NEWS_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_NEWS_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_SERVICES_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_SERVICES_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_TIZERS_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_TIZERS_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_REVIEWS_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_REVIEWS_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_STAFF_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_STAFF_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_VACANCY_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_VACANCY_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_BLOG_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_BLOG_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_PROJECTS_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_PROJECTS_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_BRANDS_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_BRANDS_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'IBLOCK_LINK_LANDINGS_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_LANDINGS_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_SERVICES_NAME' => [
        'NAME' => GetMessage('BLOCK_SERVICES_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_NEWS_NAME' => [
        'NAME' => GetMessage('BLOCK_NEWS_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_TIZERS_NAME' => [
        'NAME' => GetMessage('BLOCK_TIZERS_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_REVIEWS_NAME' => [
        'NAME' => GetMessage('BLOCK_REVIEWS_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_STAFF_NAME' => [
        'NAME' => GetMessage('BLOCK_STAFF_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_VACANCY_NAME' => [
        'NAME' => GetMessage('BLOCK_VACANCY_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_PROJECTS_NAME' => [
        'NAME' => GetMessage('BLOCK_PROJECTS_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_BRANDS_NAME' => [
        'NAME' => GetMessage('BLOCK_BRANDS_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_BLOG_NAME' => [
        'NAME' => GetMessage('BLOCK_BLOG_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_LANDINGS_NAME' => [
        'NAME' => GetMessage('BLOCK_LANDINGS_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'DETAIL_LINKED_GOODS_SLIDER' => [
        'NAME' => GetMessage('DETAIL_LINKED_GOODS_SLIDER_TITLE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'IBLOCK_LINK_PARTNERS_ID' => [
        'NAME' => GetMessage('IBLOCK_LINK_PARTNERS_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'BLOCK_PARTNERS_NAME' => [
        'NAME' => GetMessage('BLOCK_PARTNERS_NAME_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => '',
    ],
    'SHOW_ICONS_SECTION' => [
        'NAME' => GetMessage('SHOW_ICONS_SECTION_TITLE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_COUNT_ELEMENTS' => [
        'NAME' => GetMessage('SHOW_COUNT_ELEMENTS_TITLE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_GALLERY_GOODS' => [
        'NAME' => GetMessage('SHOW_GALLERY_GOODS_NAME'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'MAX_GALLERY_GOODS_ITEMS' => [
        'NAME' => GetMessage('MAX_GALLERY_GOODS_ITEMS_NAME'),
        'TYPE' => 'LIST',
        'VALUES' => [2 => 2, 3 => 3, 4 => 4, 5 => 5],
        'DEFAULT' => '5',
        'HIDDEN' => ($arCurrentValues['SHOW_GALLERY_GOODS'] == 'Y' ? 'N' : 'Y'),
    ],
    'ADD_DETAIL_TO_SLIDER' => [
        'NAME' => GetMessage('ADD_DETAIL_TO_SLIDER_NAME'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'ONLY_ELEMENT_DISPLAY_VARIANT' => [
        'NAME' => GetMessage('ONLY_ELEMENT_DISPLAY_VARIANT'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'SHOW_ONE_CLICK_BUY' => [
        'NAME' => GetMessage('SHOW_ONE_CLICK_BUY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'N',
        'PARENT' => 'DETAIL_SETTINGS',
    ],
    'LINKED_ELEMENT_TAB_SORT_FIELD' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('LINKED_ELEMENT_TAB_SORT_FIELD'),
        'TYPE' => 'LIST',
        'VALUES' => $arSort,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'sort',
    ],
    'LINKED_ELEMENT_TAB_SORT_ORDER' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('LINKED_ELEMENT_TAB_SORT_ORDER'),
        'TYPE' => 'LIST',
        'VALUES' => $arAscDesc,
        'DEFAULT' => 'asc',
        'ADDITIONAL_VALUES' => 'Y',
    ],
    'LINKED_ELEMENT_TAB_SORT_FIELD2' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('LINKED_ELEMENT_TAB_SORT_FIELD2'),
        'TYPE' => 'LIST',
        'VALUES' => $arSort,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'id',
    ],
    'LINKED_ELEMENT_TAB_SORT_ORDER2' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('LINKED_ELEMENT_TAB_SORT_ORDER2'),
        'TYPE' => 'LIST',
        'VALUES' => $arAscDesc,
        'DEFAULT' => 'desc',
        'ADDITIONAL_VALUES' => 'Y',
    ],
    'HIDE_NOT_AVAILABLE' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE'),
        'TYPE' => 'LIST',
        'DEFAULT' => 'N',
        'VALUES' => [
            'Y' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_HIDE'),
            'L' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_LAST'),
            'N' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_SHOW'),
        ],
        'ADDITIONAL_VALUES' => 'N',
    ],
    'HIDE_NOT_AVAILABLE_OFFERS' => [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS'),
        'TYPE' => 'LIST',
        'DEFAULT' => 'N',
        'VALUES' => [
            'Y' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS_HIDE'),
            'L' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS_SUBSCRIBE'),
            'N' => GetMessage('CP_BC_HIDE_NOT_AVAILABLE_OFFERS_SHOW'),
        ],
    ],
]);

if (is_array($arCurrentValues['SORT_BUTTONS'])) {
    if (in_array('PRICE', $arCurrentValues['SORT_BUTTONS'])) {
        $arTemplateParameters['SORT_PRICES'] = [
            'SORT' => 200,
            'NAME' => GetMessage('SORT_PRICES'),
            'TYPE' => 'LIST',
            'VALUES' => $arPrice,
            'DEFAULT' => ['MINIMUM_PRICE'],
            'PARENT' => 'DETAIL_SETTINGS',
            'MULTIPLE' => 'N',
        ];
        $arTemplateParameters['SORT_REGION_PRICE'] = [
            'SORT' => 200,
            'NAME' => GetMessage('SORT_REGION_PRICE'),
            'TYPE' => 'LIST',
            'VALUES' => $arRegionPrice,
            'DEFAULT' => ['BASE'],
            'PARENT' => 'DETAIL_SETTINGS',
            'MULTIPLE' => 'N',
        ];
    }
}

if ((strpos($arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'], 'list_elements_1') !== false)
    || (strpos($arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'], 'list_elements_2') !== false)
    || (strpos($arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'], 'list_elements_3') !== false)
    || (strpos($arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'], 'list_elements_4') !== false)) {
    $arTemplateParameters = array_merge($arTemplateParameters, [
        'SIZE_IN_ROW' => [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => GetMessage('SIZE_IN_ROW_NAME'),
            'TYPE' => 'LIST',
            'VALUES' => [5 => 5, 4 => 4, 3 => 3],
            'DEFAULT' => 4,
        ],
    ]);
}

if (strpos($arCurrentValues['SECTION_ELEMENTS_TYPE_VIEW'], 'list_elements_5') !== false) {
    $arTemplateParameters = array_merge($arTemplateParameters, [
        'IMAGE_POSITION' => [
            'PARENT' => 'LIST_SETTINGS',
            'SORT' => 250,
            'NAME' => GetMessage('IMAGE_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => GetMessage('IMAGE_POSITION_LEFT'),
                'right' => GetMessage('IMAGE_POSITION_RIGHT'),
            ],
            'DEFAULT' => 'left',
        ],
    ]);
}

$arTemplateParameters['IBLOCK_CATALOG_TYPE'] = [
    'SORT' => 200,
    'NAME' => GetMessage('IBLOCK_CATALOG_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTypesEx,
    'PARENT' => 'DETAIL_SETTINGS',
    'MULTIPLE' => 'N',
    'REFRESH' => 'Y',
];
// if($arCurrentValues["IBLOCK_CATALOG_TYPE"]){
$arTemplateParameters['IBLOCK_CATALOG_ID'] = [
    'SORT' => 200,
    'NAME' => GetMessage('IBLOCK_CATALOG_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks,
    'PARENT' => 'DETAIL_SETTINGS',
    'MULTIPLE' => 'N',
    'REFRESH' => 'Y',
];
// }

if ($arCurrentValues['IBLOCK_CATALOG_ID']) {
    $arTemplateParameters['SALE_STIKER'] = [
        'NAME' => GetMessage('SALE_STIKER'),
        'TYPE' => 'LIST',
        'DEFAULT' => '-',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => array_merge(['-' => ' '], $arProperty_S),
        'PARENT' => 'DETAIL_SETTINGS',
    ];
    $arTemplateParameters['STIKERS_PROP'] = [
        'NAME' => GetMessage('STIKERS_PROP_TITLE'),
        'TYPE' => 'LIST',
        'DEFAULT' => '-',
        'ADDITIONAL_VALUES' => 'Y',
        'VALUES' => array_merge(['-' => ' '], $arProperty_XL),
        'PARENT' => 'DETAIL_SETTINGS',
    ];
}
if ($boolSKU) {
    $arTemplateParameters['OFFER_HIDE_NAME_PROPS'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('OFFER_HIDE_NAME_PROPS_TITLE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];
    $arTemplateParameters['LIST_OFFERS_FIELD_CODE'] = CIBlockParameters::GetFieldCode(GetMessage('CP_BC_LIST_OFFERS_FIELD_CODE'), 'DETAIL_SETTINGS');

    $arTemplateParameters['OFFERS_CART_PROPERTIES'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_OFFERS_PROPERTIES'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arProperty_Offers,
        'HIDDEN' => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N'),
    ];

    $arTemplateParameters['OFFERS_SORT_FIELD'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_OFFERS_SORT_FIELD'),
        'TYPE' => 'LIST',
        'VALUES' => $arSort,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'sort',
    ];
    $arTemplateParameters['OFFERS_SORT_ORDER'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_OFFERS_SORT_ORDER'),
        'TYPE' => 'LIST',
        'VALUES' => $arAscDesc,
        'DEFAULT' => 'asc',
        'ADDITIONAL_VALUES' => 'Y',
    ];
    $arTemplateParameters['OFFERS_SORT_FIELD2'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_OFFERS_SORT_FIELD2'),
        'TYPE' => 'LIST',
        'VALUES' => $arSort,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => 'id',
    ];
    $arTemplateParameters['OFFERS_SORT_ORDER2'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_OFFERS_SORT_ORDER2'),
        'TYPE' => 'LIST',
        'VALUES' => $arAscDesc,
        'DEFAULT' => 'desc',
        'ADDITIONAL_VALUES' => 'Y',
    ];
}

$arTemplateParameters['SHOW_GALLERY'] = [
    'NAME' => GetMessage('SHOW_GALLERY'),
    'TYPE' => 'CHECKBOX',
    'SORT' => 707,
    'PARENT' => 'DETAIL_SETTINGS',
    'DEFAULT' => 'Y',
];
$arTemplateParameters['GALLERY_PRODUCTS_PROPERTY'] = [
    'NAME' => GetMessage('GALLERY_PRODUCTS_PROPERTY'),
    'TYPE' => 'LIST',
    'SORT' => 708,
    'PARENT' => 'DETAIL_SETTINGS',
    'VALUES' => $arPropertyF,
    'ADDITIONAL_VALUES' => 'Y',
    'DEFAULT' => 'PHOTOS',
];
$arTemplateParameters['DEPTH_LEVEL_BRAND'] = [
    'NAME' => GetMessage('DEPTH_LEVEL_BRAND'),
    'SORT' => 709,
    'TYPE' => 'TEXT',
    'PARENT' => 'DETAIL_SETTINGS',
    'DEFAULT' => '2',
];

$arTemplateParameters['DETAIL_BLOCKS_ALL_ORDER'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => GetMessage('CP_BC_TPL_CONTENT_BLOCKS_ALL_ORDER'),
    'TYPE' => 'CUSTOM',
    'JS_FILE' => CatalogSectionComponent::getSettingsScript('/bitrix/components/bitrix/catalog.section', 'dragdrop_order'),
    'JS_EVENT' => 'initDraggableOrderControl',
    'JS_DATA' => Json::encode([
        'docs' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_DOCS'),
        'brands' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_BRANDS'),
        'gallery' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_GALLERY'),
        'desc' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_DESC'),
        'char' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_CHAR'),
        'tizers' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_TIZERS'),
        'preview_text' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_PREVIEW_TEXT'),
        'services' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_SERVICES'),
        'news' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_NEWS'),
        'blog' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_BLOG'),
        'vacancy' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_VACANCY'),
        'reviews' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_REVIEWS'),
        'comments' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_COMMENTS'),
        'goods' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_GOODS'),
        'goods_sections' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_GOODS_SECTIONS'),
        'goods_catalog' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_GOODS_CATALOG'),
        'projects' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_PROJECTS'),
        'partners' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_PARTNERS'),
        'landings' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_LANDINGS'),
        'form_order' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_FORM_ORDER'),
        'staff' => GetMessage('CP_BC_TPL_CONTENT_BLOCK_STAFF'),
    ]),
    'DEFAULT' => 'tizers,preview_text,char,docs,services,news,vacancy,blog,projects,brands,staff,gallery,partners,form_order,landings,reviews,goods_sections,goods,goods_catalog,desc,comments',
];

$arTemplateParameters['DETAIL_USE_COMMENTS'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_COMMENTS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y',
];

$arPrice = [];
if (Bitrix\Main\Loader::includeModule('catalog')) {
    $arPrice = CCatalogIBlockParameters::getPriceTypesList();
    $arTemplateParameters['PRICE_CODE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('PRICE_CODE_TITLE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arPrice,
        'ADDITIONAL_VALUES' => 'Y',
    ];

    $arTemplateParameters['PRICE_VAT_INCLUDE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('IBLOCK_PRICE_VAT_INCLUDE'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'N',
        'DEFAULT' => 'Y',
    ];
    $arTemplateParameters['USE_PRICE_COUNT'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('IBLOCK_USE_PRICE_COUNT'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'N',
        'DEFAULT' => 'N',
    ];

    $arStore = [];
    global $USER_FIELD_MANAGER;
    $storeIterator = CCatalogStore::GetList(
        [],
        ['ISSUING_CENTER' => 'Y'],
        false,
        false,
        ['ID', 'TITLE']
    );
    while ($store = $storeIterator->GetNext()) {
        $arStore[$store['ID']] = '['.$store['ID'].'] '.$store['TITLE'];
    }

    $userFields = $USER_FIELD_MANAGER->GetUserFields('CAT_STORE', 0, LANGUAGE_ID);
    $propertyUF = [];

    foreach ($userFields as $fieldName => $userField) {
        $propertyUF[$fieldName] = $userField['LIST_COLUMN_LABEL'] ? $userField['LIST_COLUMN_LABEL'] : $fieldName;
    }

    $arTemplateParameters['STORES'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('STORES'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arStore,
        'ADDITIONAL_VALUES' => 'Y',
    ];
}

if ($arCurrentValues['DETAIL_USE_COMMENTS'] != 'N') {
    if (Bitrix\Main\ModuleManager::isModuleInstalled('blog')) {
        $arTemplateParameters['DETAIL_BLOG_USE'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y',
        ];
        if (isset($arCurrentValues['DETAIL_BLOG_USE']) && $arCurrentValues['DETAIL_BLOG_USE'] == 'Y') {
            $arTemplateParameters['DETAIL_BLOG_URL'] = [
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => GetMessage('CP_BC_DETAIL_TPL_BLOG_URL'),
                'TYPE' => 'STRING',
                'DEFAULT' => 'catalog_comments',
            ];
            $arTemplateParameters['COMMENTS_COUNT'] = [
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => GetMessage('T_COMMENTS_COUNT'),
                'TYPE' => 'STRING',
                'DEFAULT' => '5',
            ];
            $arTemplateParameters['BLOG_TITLE'] = [
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => GetMessage('BLOCK_TITLE_TAB'),
                'TYPE' => 'STRING',
                'DEFAULT' => GetMessage('S_COMMENTS_VALUE'),
            ];
            $arTemplateParameters['DETAIL_BLOG_EMAIL_NOTIFY'] = [
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_EMAIL_NOTIFY'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
            ];
        }
    }

    $boolRus = false;
    $langBy = 'id';
    $langOrder = 'asc';
    $rsLangs = CLanguage::GetList($langBy, $langOrder, ['ID' => 'ru', 'ACTIVE' => 'Y']);
    if ($arLang = $rsLangs->Fetch()) {
        $boolRus = true;
    }

    if ($boolRus) {
        $arTemplateParameters['DETAIL_VK_USE'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y',
        ];

        if (isset($arCurrentValues['DETAIL_VK_USE']) && $arCurrentValues['DETAIL_VK_USE'] == 'Y') {
            $arTemplateParameters['VK_TITLE'] = [
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => GetMessage('BLOCK_TITLE_TAB'),
                'TYPE' => 'STRING',
                'DEFAULT' => GetMessage('S_VK_VALUE'),
            ];
            $arTemplateParameters['DETAIL_VK_API_ID'] = [
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_API_ID'),
                'TYPE' => 'STRING',
                'DEFAULT' => 'API_ID',
            ];
        }
    }

    $arTemplateParameters['DETAIL_FB_USE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y',
    ];

    if (isset($arCurrentValues['DETAIL_FB_USE']) && $arCurrentValues['DETAIL_FB_USE'] == 'Y') {
        $arTemplateParameters['FB_TITLE'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => GetMessage('BLOCK_TITLE_TAB'),
            'TYPE' => 'STRING',
            'DEFAULT' => GetMessage('S_FB_VALUE'),
        ];
        $arTemplateParameters['DETAIL_FB_APP_ID'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_APP_ID'),
            'TYPE' => 'STRING',
            'DEFAULT' => '',
        ];
    }
}
