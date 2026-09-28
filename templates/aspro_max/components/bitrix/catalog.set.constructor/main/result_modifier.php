<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

use Bitrix\Main\Localization\Loc;

// prevent set to be purchased if primary product is not available
if (!$arResult['ELEMENT']['PRICE_VALUE']) {
    $arResult['ELEMENT']['CAN_BUY'] = false;
    $arResult['ERROR'] = Loc::getMessage('CATALOG_SET_MAIN_PRODUCT_HAS_NO_PRICE');

    return;
}

$arDefaultParams = [
    'TEMPLATE_THEME' => 'blue',
];
$arParams = array_merge($arDefaultParams, $arParams);

$arParams['TEMPLATE_THEME'] = (string) $arParams['TEMPLATE_THEME'];
if ($arParams['TEMPLATE_THEME'] != '') {
    $arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
    if ($arParams['TEMPLATE_THEME'] == 'site') {
        $templateId = COption::GetOptionString('main', 'wizard_template_id', 'eshop_bootstrap', SITE_ID);
        $templateId = (preg_match('/^eshop_adapt/', $templateId)) ? 'eshop_adapt' : $templateId;
        $arParams['TEMPLATE_THEME'] = COption::GetOptionString('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
    }
    if ($arParams['TEMPLATE_THEME'] != '') {
        if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css')) {
            $arParams['TEMPLATE_THEME'] = '';
        }
    }
}
if ($arParams['TEMPLATE_THEME'] == '') {
    $arParams['TEMPLATE_THEME'] = 'blue';
}

if ($arResult['ELEMENT']['DETAIL_PICTURE'] || $arResult['ELEMENT']['PREVIEW_PICTURE']) {
    $arResult['ELEMENT']['DETAIL_PICTURE'] = CFile::ResizeImageGet(
        $arResult['ELEMENT']['DETAIL_PICTURE'] ?: $arResult['ELEMENT']['PREVIEW_PICTURE'],
        ['width' => '150', 'height' => '180'],
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true
    );
}

$arDefaultSetIDs = [$arResult['ELEMENT']['ID']];

$canBuySet = false;
$unavailableItems = [];

foreach (['DEFAULT', 'OTHER'] as $type) {
    foreach ($arResult['SET_ITEMS'][$type] as $key => $arItem) {
        $currentItemType = $type;

        // prevent duplicate item check
        if (array_key_exists($arItem['ID'], $unavailableItems)) {
            continue;
        }

        // prevent set item to be purchased if it's not available
        if ($currentItemType === 'DEFAULT' && !$arItem['PRICE_VALUE']) {
            $currentItemType = 'OTHER';
            $arItem['CAN_BUY'] = false;

            $unavailableItems[$arItem['ID']] = $arItem['ID'];
        } else {
            $canBuySet = true;
        }

        $arElement = [
            'ID' => $arItem['ID'],
            'NAME' => $arItem['NAME'],
            'DETAIL_PAGE_URL' => $arItem['DETAIL_PAGE_URL'],
            'DETAIL_PICTURE' => $arItem['DETAIL_PICTURE'],
            'PREVIEW_PICTURE' => $arItem['PREVIEW_PICTURE'],
            'PRICE_CURRENCY' => $arItem['PRICE_CURRENCY'],
            'PRICE_DISCOUNT_VALUE' => $arItem['PRICE_DISCOUNT_VALUE'],
            'PRICE_PRINT_DISCOUNT_VALUE' => $arItem['PRICE_PRINT_DISCOUNT_VALUE'],
            'PRICE_VALUE' => $arItem['PRICE_VALUE'],
            'PRICE_PRINT_VALUE' => $arItem['PRICE_PRINT_VALUE'],
            'PRICE_DISCOUNT_DIFFERENCE_VALUE' => $arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'],
            'PRICE_DISCOUNT_DIFFERENCE' => $arItem['PRICE_DISCOUNT_DIFFERENCE'],
            'CAN_BUY' => $arItem['CAN_BUY'],
            'SET_QUANTITY' => $arItem['SET_QUANTITY'],
            'MEASURE_RATIO' => $arItem['MEASURE_RATIO'],
            'BASKET_QUANTITY' => $arItem['BASKET_QUANTITY'],
            'MEASURE' => $arItem['MEASURE'],
        ];

        if ($arItem['PRICE_CONVERT_DISCOUNT_VALUE']) {
            $arElement['PRICE_CONVERT_DISCOUNT_VALUE'] = $arItem['PRICE_CONVERT_DISCOUNT_VALUE'];
        }

        if ($arItem['PRICE_CONVERT_VALUE']) {
            $arElement['PRICE_CONVERT_VALUE'] = $arItem['PRICE_CONVERT_VALUE'];
        }

        if ($arItem['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE']) {
            $arElement['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE'] = $arItem['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE'];
        }

        if ($currentItemType === 'DEFAULT') {
            $arDefaultSetIDs[] = $arItem['ID'];
        }

        if ($arItem['DETAIL_PICTURE'] || $arItem['PREVIEW_PICTURE']) {
            $arElement['DETAIL_PICTURE'] = CFile::ResizeImageGet(
                $arItem['DETAIL_PICTURE'] ?: $arItem['PREVIEW_PICTURE'],
                ['width' => '150', 'height' => '180'],
                BX_RESIZE_IMAGE_PROPORTIONAL,
                true
            );
        }

        $arResult['SET_ITEMS'][$currentItemType][$key] = $arElement;
    }
}

// prevent set to be purchased if there are no available products in set
if (!$canBuySet) {
    $arResult['ERROR'] = Loc::getMessage('CATALOG_SET_MAIN_PRODUCT_NO_PRODUCTS_AVAILABLE');

    return;
}

$arResult['SET_ITEMS']['DEFAULT'] = array_filter(
    $arResult['SET_ITEMS']['DEFAULT'],
    fn ($value) => !array_key_exists($value['ID'], $unavailableItems)
);

$arResult['DEFAULT_SET_IDS'] = $arDefaultSetIDs;
