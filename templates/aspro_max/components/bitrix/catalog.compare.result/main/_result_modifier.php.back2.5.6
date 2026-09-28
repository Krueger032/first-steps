<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
use Bitrix\Main\Type\Collection;

if ($arResult['ITEMS']) {

    $arSectionsIDs = [];
    $strBaseCurrency = $arParams['CURRENCY_ID'];
    if (!$arParams['CURRENCY_ID']) {
        $strBaseCurrency = CCurrency::GetBaseCurrency();
    }

    foreach ($arResult['ITEMS'] as $key => $arItem) {

        if ($arItem['CATALOG_TYPE'] == 3 && !$arItem['OFFER_FIELDS']) {
            $arResult['ITEMS'][$key]['MESSAGE_FROM'] = GetMessage('PRICE_FROM');
            $arResult['ITEMS'][$key]['MIN_PRICE'] = [
                'DISCOUNT_VALUE' => $arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'],
                'SUFFIX' => GetMessage('PRICE_FROM'),
                'PRINT_DISCOUNT_VALUE' => CCurrencyLang::CurrencyFormat($arItem['PROPERTIES']['MINIMUM_PRICE']['VALUE'], $strBaseCurrency, true),
            ];
        }
        if (!empty($arItem['OFFER_FIELDS'])) {
            if (!empty($arItem['OFFER_FIELDS']['PREVIEW_PICTURE']) && is_array($arItem['OFFER_FIELDS']['PREVIEW_PICTURE'])) {
                $arResult['ITEMS'][$key]['PREVIEW_PICTURE'] = $arItem['OFFER_FIELDS']['PREVIEW_PICTURE'];
            }
            if (!empty($arItem['OFFER_FIELDS']['DETAIL_PICTURE']) && is_array($arItem['OFFER_FIELDS']['DETAIL_PICTURE'])) {
                $arResult['ITEMS'][$key]['DETAIL_PICTURE'] = $arItem['OFFER_FIELDS']['DETAIL_PICTURE'];
            }
            if ($arItem['OFFER_FIELDS']['IBLOCK_ID']) {
                $arResult['ITEMS'][$key]['IBLOCK_ID'] = $arItem['OFFER_FIELDS']['IBLOCK_ID'];
            }
        }

        if ($arItem['IBLOCK_SECTION_ID']) {
            $arSectionsIDs[] = $arItem['IBLOCK_SECTION_ID'];
        }
    }
}

$arParams['TYPE_SKU'] = 'N';

$arResult['ALL_FIELDS'] = [];
$existShow = !empty($arResult['SHOW_FIELDS']);
$existDelete = !empty($arResult['DELETED_FIELDS']);
if ($existShow || $existDelete) {
    if ($existShow) {
        foreach ($arResult['SHOW_FIELDS'] as $propCode) {
            $arResult['SHOW_FIELDS'][$propCode] = [
                'CODE' => $propCode,
                'IS_DELETED' => 'N',
                'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_FIELD_TEMPLATE']),
                'SORT' => $arResult['FIELDS_SORT'][$propCode],
            ];
        }
        unset($propCode);
        $arResult['ALL_FIELDS'] = $arResult['SHOW_FIELDS'];
        if ($arResult['ALL_FIELDS']['PREVIEW_PICTURE'] || $arResult['ALL_FIELDS']['DETAIL_PICTURE']) {
            unset($arResult['ALL_FIELDS']['PREVIEW_PICTURE'],$arResult['ALL_FIELDS']['DETAIL_PICTURE']);
        }
    }
    if ($existDelete) {
        foreach ($arResult['DELETED_FIELDS'] as $propCode) {
            $arResult['ALL_FIELDS'][$propCode] = [
                'CODE' => $propCode,
                'IS_DELETED' => 'Y',
                'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_FIELD_TEMPLATE']),
                'SORT' => $arResult['FIELDS_SORT'][$propCode],
            ];
        }
        unset($propCode, $arResult['DELETED_FIELDS']);
    }
    Collection::sortByColumn($arResult['ALL_FIELDS'], ['SORT' => SORT_ASC]);
}

$arResult['ALL_PROPERTIES'] = [];
$existShow = !empty($arResult['SHOW_PROPERTIES']);
$existDelete = !empty($arResult['DELETED_PROPERTIES']);
if ($existShow || $existDelete) {
    if ($existShow) {
        foreach ($arResult['SHOW_PROPERTIES'] as $propCode => $arProp) {
            $arResult['SHOW_PROPERTIES'][$propCode]['IS_DELETED'] = 'N';
            $arResult['SHOW_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_PROPERTY_TEMPLATE']);
        }
        $arResult['ALL_PROPERTIES'] = $arResult['SHOW_PROPERTIES'];
    }
    unset($arProp, $propCode);
    if ($existDelete) {
        foreach ($arResult['DELETED_PROPERTIES'] as $propCode => $arProp) {
            $arResult['DELETED_PROPERTIES'][$propCode]['IS_DELETED'] = 'Y';
            $arResult['DELETED_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_PROPERTY_TEMPLATE']);
            $arResult['ALL_PROPERTIES'][$propCode] = $arResult['DELETED_PROPERTIES'][$propCode];
        }
        unset($arProp, $propCode, $arResult['DELETED_PROPERTIES']);
    }
    Collection::sortByColumn($arResult['ALL_PROPERTIES'], ['SORT' => SORT_ASC, 'ID' => SORT_ASC]);
}

$arResult['ALL_OFFER_FIELDS'] = [];
$existShow = !empty($arResult['SHOW_OFFER_FIELDS']);
$existDelete = !empty($arResult['DELETED_OFFER_FIELDS']);
if ($existShow || $existDelete) {
    if ($existShow) {
        foreach ($arResult['SHOW_OFFER_FIELDS'] as $propCode) {
            if ($propCode == 'PREVIEW_PICTURE' || $propCode == 'DETAIL_PICTURE' || $propCode == 'NAME' || $propCode == 'ID' || $propCode == 'IBLOCK_ID') {
                unset($arResult['SHOW_OFFER_FIELDS'][$propCode]);
            } else {
                $arResult['SHOW_OFFER_FIELDS'][$propCode] = [
                    'CODE' => $propCode,
                    'IS_DELETED' => 'N',
                    'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_OF_FIELD_TEMPLATE']),
                    'SORT' => $arResult['FIELDS_SORT'][$propCode],
                ];
            }
        }
        unset($propCode);
        $arResult['ALL_OFFER_FIELDS'] = $arResult['SHOW_OFFER_FIELDS'];
    }
    if ($existDelete) {
        foreach ($arResult['DELETED_OFFER_FIELDS'] as $propCode) {
            $arResult['ALL_OFFER_FIELDS'][$propCode] = [
                'CODE' => $propCode,
                'IS_DELETED' => 'Y',
                'ACTION_LINK' => str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_OF_FIELD_TEMPLATE']),
                'SORT' => $arResult['FIELDS_SORT'][$propCode],
            ];
        }
        unset($propCode, $arResult['DELETED_OFFER_FIELDS']);
    }
    Collection::sortByColumn($arResult['ALL_OFFER_FIELDS'], ['SORT' => SORT_ASC]);
}

$arResult['ALL_OFFER_PROPERTIES'] = [];
$existShow = !empty($arResult['SHOW_OFFER_PROPERTIES']);
$existDelete = !empty($arResult['DELETED_OFFER_PROPERTIES']);
if ($existShow || $existDelete) {
    if ($existShow) {
        foreach ($arResult['SHOW_OFFER_PROPERTIES'] as $propCode => $arProp) {
            $arResult['SHOW_OFFER_PROPERTIES'][$propCode]['IS_DELETED'] = 'N';
            $arResult['SHOW_OFFER_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~DELETE_FEATURE_OF_PROPERTY_TEMPLATE']);
        }
        unset($arProp, $propCode);
        $arResult['ALL_OFFER_PROPERTIES'] = $arResult['SHOW_OFFER_PROPERTIES'];
    }
    if ($existDelete) {
        foreach ($arResult['DELETED_OFFER_PROPERTIES'] as $propCode => $arProp) {
            $arResult['DELETED_OFFER_PROPERTIES'][$propCode]['IS_DELETED'] = 'Y';
            $arResult['DELETED_OFFER_PROPERTIES'][$propCode]['ACTION_LINK'] = str_replace('#CODE#', $propCode, $arResult['~ADD_FEATURE_OF_PROPERTY_TEMPLATE']);
            $arResult['ALL_OFFER_PROPERTIES'][$propCode] = $arResult['DELETED_OFFER_PROPERTIES'][$propCode];
        }
        unset($arProp, $propCode, $arResult['DELETED_OFFER_PROPERTIES']);
    }
    Collection::sortByColumn($arResult['ALL_OFFER_PROPERTIES'], ['SORT' => SORT_ASC, 'ID' => SORT_ASC]);
}

$arResult['SECTIONS'] = [];
if ($arParams['USE_COMPARE_GROUP'] === 'Y') {
    if ($arSectionsIDs) {
        $arResult['SECTIONS'] = CMaxCache::CIBLockSection_GetList(['SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => ['TAG' => CMaxCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => ['ID'], 'MULTI' => 'N']], ['ID' => $arSectionsIDs, 'ACTIVE' => 'Y'], false, ['ID', 'NAME', 'IBLOCK_ID', 'SECTION_PAGE_URL']);
    }

    foreach ($arResult['ITEMS'] as $arItem) {
        $SID = $arItem['IBLOCK_SECTION_ID'] ?: 0;
        if ($SID) {
            $arResult['SECTIONS'][$SID]['ITEMS'][$arItem['ID']] = $arItem;
        }
    }
} else {
    $arResult['SECTIONS'][0]['ITEMS'] = $arResult['ITEMS'];
    $arResult['SECTIONS'][0]['ID'] = '0';

    if (count($arResult['ITEMS']) === 1) {
        $arResult['SECTIONS'][0]['SECTION_PAGE_URL'] = CMaxCache::CIBLockSection_GetList(['SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => ['TAG' => CMaxCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), [], 'MULTI' => 'N']], ['ID' => $arSectionsIDs, 'ACTIVE' => 'Y'], false, ['SECTION_PAGE_URL'])['SECTION_PAGE_URL'] ?: CMax::GetFrontParametrValue('CATALOG_PAGE_URL');
    }
}
