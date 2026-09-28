<?php
use Bitrix\Currency\CurrencyTable;
use Bitrix\Iblock;
use Bitrix\Main\Type\Collection;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$displayPreviewTextMode = [
    'H' => true,
    'E' => true,
    'S' => true,
];
$detailPictMode = [
    'IMG' => true,
    'POPUP' => true,
    'MAGNIFIER' => true,
    'GALLERY' => true,
];

$arDefaultParams = [
    'TYPE_SKU' => 'Y',
    'ADD_PICT_PROP' => '-',
    'OFFER_ADD_PICT_PROP' => '-',
    'OFFER_TREE_PROPS' => ['-'],
    'ADD_TO_BASKET_ACTION' => 'ADD',
    'DEFAULT_COUNT' => '1',
];
$arParams = array_merge($arDefaultParams, $arParams);
if ($arParams['TYPE_SKU'] != 'TYPE_1') {
    $arParams['TYPE_SKU'] = 'N';
}

/* OUT OF PRODUCTION */
$arResult['OUT_OF_PRODUCTION'] = isset($arResult['PROPERTIES']['OUT_OF_PRODUCTION']) && $arResult['PROPERTIES']['OUT_OF_PRODUCTION']['VALUE'] === 'Y';
$arResult['PRODUCT_ANALOG'] = isset($arResult['PROPERTIES']['PRODUCT_ANALOG']) && $arResult['PROPERTIES']['PRODUCT_ANALOG']['VALUE']
    ? [
        'ID' => $arResult['PROPERTIES']['PRODUCT_ANALOG']['VALUE'],
        'IBLOCK_ID' => $arResult['PROPERTIES']['PRODUCT_ANALOG']['LINK_IBLOCK_ID'],
    ]
    : false;
$arResult['PRODUCT_ANALOG_FILTER'] = isset($arResult['PROPERTIES']['PRODUCT_ANALOG_FILTER']) && $arResult['PROPERTIES']['PRODUCT_ANALOG_FILTER']['VALUE']
    ? $arResult['PROPERTIES']['PRODUCT_ANALOG_FILTER']['VALUE']
    : false;
if ($arResult['OUT_OF_PRODUCTION']) {
    $arParams['TYPE_SKU'] = 'N';
    unset($arResult['OFFERS']);
}

$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ($arParams['ADD_PICT_PROP'] == '-') {
    $arParams['ADD_PICT_PROP'] = '';
}
$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ($arParams['LABEL_PROP'] == '-') {
    $arParams['LABEL_PROP'] = '';
}
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ($arParams['OFFER_ADD_PICT_PROP'] == '-') {
    $arParams['OFFER_ADD_PICT_PROP'] = '';
}
if (!is_array($arParams['OFFER_TREE_PROPS'])) {
    $arParams['OFFER_TREE_PROPS'] = [$arParams['OFFER_TREE_PROPS']];
}
foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value) {
    $value = (string) $value;
    if ($value == '' || $value == '-') {
        unset($arParams['OFFER_TREE_PROPS'][$key]);
    }
}

/* stores product */
$arStores = CMax::CCatalogStore_GetList([], ['ACTIVE' => 'Y'], false, false, []);
$arResult['STORES_COUNT'] = count($arStores);

if($arResult['DISPLAY_PROPERTIES']['CML2_ARTICLE']['VALUE']) {
    if(is_array($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])) {
        $arResult['CML2_ARTICLE'] = $arResult['PROPERTIES']['CML2_ARTICLE'];
        $arResult['CML2_ARTICLE']['VALUE'] = reset($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']);
        $arResult['DISPLAY_PROPERTIES']['CML2_ARTICLE']['VALUE'] = reset($arResult['DISPLAY_PROPERTIES']['CML2_ARTICLE']['VALUE']);
    } else {
        $arResult['CML2_ARTICLE'] = $arResult['PROPERTIES']['CML2_ARTICLE'];
    }
}

if ($arParams['DISPLAY_NAME'] != 'N') {
    $arParams['DISPLAY_NAME'] = 'Y';
}
if (!isset($detailPictMode[$arParams['DETAIL_PICTURE_MODE']])) {
    $arParams['DETAIL_PICTURE_MODE'] = 'IMG';
}
if ($arParams['ADD_DETAIL_TO_SLIDER'] != 'Y') {
    $arParams['ADD_DETAIL_TO_SLIDER'] = 'N';
}
if (!isset($displayPreviewTextMode[$arParams['DISPLAY_PREVIEW_TEXT_MODE']])) {
    $arParams['DISPLAY_PREVIEW_TEXT_MODE'] = 'E';
}
if ($arParams['PRODUCT_SUBSCRIPTION'] != 'Y') {
    $arParams['PRODUCT_SUBSCRIPTION'] = 'N';
}
if ($arParams['SHOW_DISCOUNT_PERCENT'] != 'Y') {
    $arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
}
if ($arParams['SHOW_OLD_PRICE'] != 'Y') {
    $arParams['SHOW_OLD_PRICE'] = 'N';
}
if ($arParams['SHOW_MAX_QUANTITY'] != 'Y') {
    $arParams['SHOW_MAX_QUANTITY'] = 'N';
}
if ($arParams['SHOW_BASIS_PRICE'] != 'Y') {
    $arParams['SHOW_BASIS_PRICE'] = 'N';
}
if (!is_array($arParams['ADD_TO_BASKET_ACTION'])) {
    $arParams['ADD_TO_BASKET_ACTION'] = [$arParams['ADD_TO_BASKET_ACTION']];
}
$arParams['ADD_TO_BASKET_ACTION'] = array_filter($arParams['ADD_TO_BASKET_ACTION'], 'CIBlockParameters::checkParamValues');
if (empty($arParams['ADD_TO_BASKET_ACTION']) || (!in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']) && !in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']))) {
    $arParams['ADD_TO_BASKET_ACTION'] = ['BUY'];
}
if ($arParams['SHOW_CLOSE_POPUP'] != 'Y') {
    $arParams['SHOW_CLOSE_POPUP'] = 'N';
}

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
if ($arParams['USE_VOTE_RATING'] != 'Y') {
    $arParams['USE_VOTE_RATING'] = 'N';
}
if ($arParams['VOTE_DISPLAY_AS_RATING'] != 'vote_avg') {
    $arParams['VOTE_DISPLAY_AS_RATING'] = 'rating';
}
if ($arParams['USE_COMMENTS'] != 'Y') {
    $arParams['USE_COMMENTS'] = 'N';
}
if ($arParams['BLOG_USE'] != 'Y') {
    $arParams['BLOG_USE'] = 'N';
}
if ($arParams['VK_USE'] != 'Y') {
    $arParams['VK_USE'] = 'N';
}
if ($arParams['FB_USE'] != 'Y') {
    $arParams['FB_USE'] = 'N';
}
if ($arParams['USE_COMMENTS'] == 'Y') {
    if ($arParams['BLOG_USE'] == 'N' && $arParams['VK_USE'] == 'N' && $arParams['FB_USE'] == 'N') {
        $arParams['USE_COMMENTS'] = 'N';
    }
}

$arEmptyPreview = false;
$strEmptyPreview = SITE_TEMPLATE_PATH.'/images/svg/noimage_product.svg';
if(file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview)) {
    /*$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
    if (!empty($arSizes))
    {*/
    $arEmptyPreview = [
        'SRC' => $strEmptyPreview,
        /*'WIDTH' => (int)$arSizes[0],
        'HEIGHT' => (int)$arSizes[1]*/
    ];
    /*	}
        unset($arSizes);*/
}
unset($strEmptyPreview);

$arSKUPropList = [];
$arSKUPropIDs = [];
$arSKUPropKeys = [];
$boolSKU = false;
$strBaseCurrency = '';
$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);

$arParams['OFFERS_CART_PROPERTIES'] = isset($arParams['OFFERS_CART_PROPERTIES']) && is_array($arParams['OFFERS_CART_PROPERTIES']) ? $arParams['OFFERS_CART_PROPERTIES'] : [];

if ($arResult['MODULES']['catalog']) {
    if (!$boolConvert) {
        $strBaseCurrency = CCurrency::GetBaseCurrency();
    }

    $arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
    $boolSKU = !empty($arSKU) && is_array($arSKU);
    $bUseModuleProps = Bitrix\Main\Config\Option::get('iblock', 'property_features_enabled', 'Y') === 'Y';

    if ($bUseModuleProps) {
        $arParams['OFFERS_CART_PROPERTIES'] = (array) Bitrix\Catalog\Product\PropertyCatalogFeature::getBasketPropertyCodes($arSKU['IBLOCK_ID'], ['CODE' => 'Y']);
    }

    if (empty($arParams['OFFER_TREE_PROPS']) && isset($arParams['OFFERS_CART_PROPERTIES']) && is_array($arParams['OFFERS_CART_PROPERTIES'])) {
        $arParams['OFFER_TREE_PROPS'] = $arParams['OFFERS_CART_PROPERTIES'];
        foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value) {
            $value = (string) $value;
            if ($value == '' || $value == '-') {
                unset($arParams['OFFER_TREE_PROPS'][$key]);
            }
        }
    }

    if ($bUseModuleProps && $boolSKU && $featureProps = Bitrix\Catalog\Product\PropertyCatalogFeature::getOfferTreePropertyCodes($arSKU['IBLOCK_ID'], ['CODE' => 'Y'])) {
        $arParams['OFFER_TREE_PROPS'] = $featureProps;
    }
    if ($boolSKU && $featureProps = Iblock\Model\PropertyFeature::getDetailPageShowPropertyCodes($arSKU['IBLOCK_ID'], ['CODE' => 'Y'])) {
        $arParams['OFFERS_PROPERTY_CODE'] = $featureProps;
    }

    if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS'])) {
        $arSKUPropList = CIBlockPriceTools::getTreeProperties(
            $arSKU,
            $arParams['OFFER_TREE_PROPS'],
            [
                // 'PICT' => $arEmptyPreview,
                'NAME' => '-',
            ]
        );
        $arResult['SKU_IBLOCK_ID'] = $arSKU['IBLOCK_ID'];
        $arSKUPropIDs = array_keys($arSKUPropList);
    }
}

$arConvertParams = [];
if ($arParams['CONVERT_CURRENCY'] == 'Y') {
    if (!CModule::IncludeModule('currency')) {
        $arParams['CONVERT_CURRENCY'] = 'N';
        $arParams['CURRENCY_ID'] = '';
    } else {
        $arResultModules['currency'] = true;
        $arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
        if (!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo))) {
            $arParams['CONVERT_CURRENCY'] = 'N';
            $arParams['CURRENCY_ID'] = '';
        } else {
            $arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
            $arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
        }
    }
}

$arResult['CHECK_QUANTITY'] = false;
if (!isset($arResult['CATALOG_MEASURE_RATIO'])) {
    $arResult['CATALOG_MEASURE_RATIO'] = 1;
}
if (!isset($arResult['CATALOG_QUANTITY'])) {
    $arResult['CATALOG_QUANTITY'] = 0;
}
$arResult['CATALOG_QUANTITY'] = (
    $arResult['CATALOG_QUANTITY'] > 0 && is_float($arResult['CATALOG_MEASURE_RATIO'])
    ? (float) $arResult['CATALOG_QUANTITY']
    : (int) $arResult['CATALOG_QUANTITY']
);
$arResult['CATALOG'] = false;
if (!isset($arResult['CATALOG_SUBSCRIPTION']) || $arResult['CATALOG_SUBSCRIPTION'] != 'Y') {
    $arResult['CATALOG_SUBSCRIPTION'] = 'N';
}

// CIBlockPriceTools::getLabel($arResult, $arParams['LABEL_PROP']);

if($arParams['ADD_DETAIL_TO_SLIDER'] !== 'Y' && $arResult['DETAIL_PICTURE']) {
    // CIBlockPriceTools :: getSliderForItem() gibt ein Array DETAIL_PICTURE wenn WEITERE FOTOS leer, auch wenn ADD_DETAIL_TO_SLIDER == N
    // unset($arResult['DETAIL_PICTURE']);
}
$arResult['ALT_TITLE_GET'] = $arParams['ALT_TITLE_GET'];
$productSlider = CMax::getSliderForItemExt($arResult, $arParams['ADD_PICT_PROP'], $arParams['ADD_DETAIL_TO_SLIDER'] == 'Y');
$bEmptyPictureProduct = false;

if (empty($productSlider)) {
    if($arResult['PREVIEW_PICTURE'] && $arParams['ADD_DETAIL_TO_SLIDER'] == 'Y') {
        $productSlider = [
            0 => $arResult['PREVIEW_PICTURE'],
        ];
    } else {
        $productSlider = [
            0 => $arEmptyPreview,
        ];
        $bEmptyPictureProduct = true;
    }
}

if(!$arParams['PICTURE_RATIO']) {
    $arParams['PICTURE_RATIO'] = 'square';
}

$arSizePict = [
    'square' => [450, 450],
    'horizontal' => [600, 450],
    'vertical' => [450, 600],
    'square_big' => [800, 800],
];

if(!in_array($arParams['PICTURE_RATIO'], array_keys($arSizePict))) {
    $arParams['PICTURE_RATIO'] = 'square';
}

if($arParams['USE_CUSTOM_RESIZE'] == 'Y') {
    $arIBlockFields = CIBlock::GetFields($arParams['IBLOCK_ID']);
    if($arIBlockFields['DETAIL_PICTURE'] && $arIBlockFields['DETAIL_PICTURE']['DEFAULT_VALUE']) {
        if($arIBlockFields['DETAIL_PICTURE']['DEFAULT_VALUE']['WIDTH'] && $arIBlockFields['DETAIL_PICTURE']['DEFAULT_VALUE']['HEIGHT']) {
            $arSizePict[$arParams['PICTURE_RATIO']][0] = $arIBlockFields['DETAIL_PICTURE']['DEFAULT_VALUE']['WIDTH'];
            $arSizePict[$arParams['PICTURE_RATIO']][1] = $arIBlockFields['DETAIL_PICTURE']['DEFAULT_VALUE']['HEIGHT'];
        } else {
            $arSizePict[$arParams['PICTURE_RATIO']][0] = 9999;
            $arSizePict[$arParams['PICTURE_RATIO']][1] = 9999;
        }
    }
}

$arResult['SHOW_SLIDER'] = true;
if($productSlider) {
    foreach($productSlider as $i => $arImage) {
        $productSlider[$i] = array_merge(
            $arImage, [
                'BIG' => ['src' => CFile::GetPath($arImage['ID']), 'width' => $arImage['WIDTH'], 'height' => $arImage['HEIGHT']],
                'SMALL' => CFile::ResizeImageGet($arImage['ID'], ['width' => $arSizePict[$arParams['PICTURE_RATIO']][0], 'height' => $arSizePict[$arParams['PICTURE_RATIO']][1]], BX_RESIZE_IMAGE_PROPORTIONAL, true, []),
                'THUMB' => CFile::ResizeImageGet($arImage['ID'], ['width' => 100, 'height' => 100], BX_RESIZE_IMAGE_PROPORTIONAL, true, []),
            ]
        );
    }
}

$productSliderCount = count($productSlider);
$arResult['MORE_PHOTO'] = $productSlider;
$arResult['MORE_PHOTO_COUNT'] = count($productSlider);

if ($arResult['MODULES']['catalog']) {
    $arResult['CATALOG'] = true;
    if (!isset($arResult['CATALOG_TYPE'])) {
        $arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
    }
    if (
        ($arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT || $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SKU)
        && !empty($arResult['OFFERS'])
    ) {
        $arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
    }
    switch ($arResult['CATALOG_TYPE']) {
        case CCatalogProduct::TYPE_SET:
            $arResult['OFFERS'] = [];
            $arResult['CHECK_QUANTITY'] = ($arResult['CATALOG_QUANTITY_TRACE'] == 'Y' && $arResult['CATALOG_CAN_BUY_ZERO'] == 'N');
            break;
        case CCatalogProduct::TYPE_SKU:
            break;
        case CCatalogProduct::TYPE_PRODUCT:
        default:
            $arResult['CHECK_QUANTITY'] = ($arResult['CATALOG_QUANTITY_TRACE'] == 'Y' && $arResult['CATALOG_CAN_BUY_ZERO'] == 'N');
            break;
    }
} else {
    $arResult['CATALOG_TYPE'] = 0;
    $arResult['OFFERS'] = [];
}

/* ADDITIONAL GALLERY */
if ($arParams['USE_ADDITIONAL_GALLERY'] === 'Y') {
    $arResult['ADDITIONAL_GALLERY'] = [];

    if ($arResult['OFFERS'] && $arParams['ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE']) {
        foreach ($arResult['OFFERS'] as $offer) {
            $imageIDs = $offer['PROPERTIES'][$arParams['ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE']]['VALUE'];
            if($arParams['TYPE_SKU'] === 'TYPE_1') {
                $arResult['ADDITIONAL_GALLERY'][$offer['ID']] = [];
            }
            if (!empty($imageIDs)) {
                foreach ($imageIDs as $imgID) {
                    $arFileImage = CFile::GetFileArray($imgID);

                    $alt = trim($arFileImage['DESCRIPTION']) ?: trim($arResult['DETAIL_PICTURE']['ALT']) ?: trim($arFileImage['ALT']) ?: trim($arResult['NAME']);
                    $title = trim($arFileImage['DESCRIPTION']) ?: trim($arResult['DETAIL_PICTURE']['TITLE']) ?: trim($arFileImage['TITLE']) ?: trim($arResult['NAME']);

                    if ($arParams['ALT_TITLE_GET'] == 'SEO') {
                        $alt = trim($arResult['DETAIL_PICTURE']['ALT']) ?: trim($arFileImage['ALT']) ?: trim($arResult['NAME']);
                        $title = trim($arResult['DETAIL_PICTURE']['TITLE']) ?: trim($arFileImage['TITLE']) ?: trim($arResult['NAME']);
                    }

                    $arImageGallery = [
                        'DETAIL' => $arFileImage,
                        'PREVIEW' => CFile::ResizeImageGet($imgID, ['width' => 1500, 'height' => 1500], BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
                        'THUMB' => CFile::ResizeImageGet($imgID, ['width' => 60, 'height' => 60], BX_RESIZE_IMAGE_EXACT, true),
                        'TITLE' => $title,
                        'ALT' => $alt,
                    ];

                    if ($arParams['TYPE_SKU'] === 'TYPE_1') {
                        $arResult['ADDITIONAL_GALLERY'][$offer['ID']][] = $arImageGallery;
                    } else {
                        $arResult['ADDITIONAL_GALLERY'][] = $arImageGallery;
                    }
                }
            }
        }
    }

    $imageIDs = $arResult['PROPERTIES'][$arParams['ADDITIONAL_GALLERY_PROPERTY_CODE']]['VALUE'];
    if (!empty($imageIDs)) {
        foreach ($imageIDs as $imgID) {
            $arFileImage = CFile::GetFileArray($imgID);

            $alt = trim($arFileImage['DESCRIPTION']) ?: trim($arResult['DETAIL_PICTURE']['ALT']) ?: trim($arFileImage['ALT']) ?: trim($arResult['NAME']);
            $title = trim($arFileImage['DESCRIPTION']) ?: trim($arResult['DETAIL_PICTURE']['TITLE']) ?: trim($arFileImage['TITLE']) ?: trim($arResult['NAME']);

            if ($arParams['ALT_TITLE_GET'] == 'SEO') {
                $alt = trim($arResult['DETAIL_PICTURE']['ALT']) ?: trim($arFileImage['ALT']) ?: trim($arResult['NAME']);
                $title = trim($arResult['DETAIL_PICTURE']['TITLE']) ?: trim($arFileImage['TITLE']) ?: trim($arResult['NAME']);
            }

            $arImageGallery = [
                'DETAIL' => $arFileImage,
                'PREVIEW' => CFile::ResizeImageGet($imgID, ['width' => 1500, 'height' => 1500], BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
                'THUMB' => CFile::ResizeImageGet($imgID, ['width' => 60, 'height' => 60], BX_RESIZE_IMAGE_EXACT, true),
                'TITLE' => $title,
                'ALT' => $alt,
            ];

            if ($arResult['OFFERS'] && ($arParams['TYPE_SKU'] === 'TYPE_1')) {
                foreach ($arResult['ADDITIONAL_GALLERY'] as $key => $gallery) {
                    $arResult['ADDITIONAL_GALLERY'][$key][] = $arImageGallery;
                }
            } else {
                $arResult['ADDITIONAL_GALLERY'][] = $arImageGallery;
            }
        }
    }
}

$arResult['TMP_OFFERS_PROP'] = [];
if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])) {
    $boolSKUDisplayProps = false;

    $arResultSKUPropIDs = [];
    $arFilterProp = [];
    $arNeedValues = [];
    if($arParams['TYPE_SKU'] == 'TYPE_1' && $arResult['OFFERS']) {
        foreach ($arResult['OFFERS'] as &$arOffer) {
            foreach ($arSKUPropIDs as &$strOneCode) {
                if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode])) {
                    $arResultSKUPropIDs[$strOneCode] = true;
                    if (!isset($arNeedValues[$arSKUPropList[$strOneCode]['ID']])) {
                        $arNeedValues[$arSKUPropList[$strOneCode]['ID']] = [];
                    }
                    $valueId = (
                        $arSKUPropList[$strOneCode]['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST
                        ? $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']
                        : $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']
                    );
                    $arNeedValues[$arSKUPropList[$strOneCode]['ID']][$valueId] = $valueId;
                    unset($valueId);
                    if (!isset($arFilterProp[$strOneCode])) {
                        $arFilterProp[$strOneCode] = $arSKUPropList[$strOneCode];
                    }
                }
                // var_dump($arOffer['DISPLAY_PROPERTIES']);die();
            }
            unset($strOneCode);
        }
        unset($arOffer);

        CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
        $arResult['TMP_OFFERS_PROP'] = $arSKUPropList;

        if(!$arResult['DISPLAY_PROPERTIES']) {
            foreach($arResult['OFFERS'] as $arOffer) {
                foreach($arOffer['DISPLAY_PROPERTIES'] as $arProp) {
                    if(!$arResult['TMP_OFFERS_PROP'][$arProp['CODE']]) {
                        if(!is_array($arProp['DISPLAY_VALUE'])) {
                            $arProp['DISPLAY_VALUE'] = [$arProp['DISPLAY_VALUE']];
                        }

                        foreach($arProp['DISPLAY_VALUE'] as $value) {
                            if(strlen($value)) {
                                $arResult['DISPLAY_PROPERTIES_OFFERS'] = true;
                                break 3;
                            }
                        }
                    }
                }
            }
        }
    }

    $arSKUPropIDs = array_keys($arSKUPropList);
    $arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);

    $arMatrixFields = $arSKUPropKeys;
    $arMatrix = [];

    $arNewOffers = [];

    $arIDS = [$arResult['ID']];
    $offerSet = [];
    $arResult['OFFER_GROUP'] = false;
    $arResult['OFFERS_PROP'] = false;

    $arDouble = [];

    foreach ($arResult['OFFERS'] as $keyOffer => $arOffer) {
        $arOffer['ID'] = (int) $arOffer['ID'];
        if (isset($arDouble[$arOffer['ID']])) {
            continue;
        }
        $arIDS[] = $arOffer['ID'];
        $boolSKUDisplayProperties = false;
        $arOffer['OFFER_GROUP'] = false;
        $arRow = [];
        foreach ($arSKUPropIDs as $propkey => $strOneCode) {
            $arCell = [
                'VALUE' => 0,
                'SORT' => PHP_INT_MAX,
                'NA' => true,
            ];
            if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]) && (!$bUseModuleProps || in_array($strOneCode, $arParams['OFFERS_PROPERTY_CODE']))) {
                $arMatrixFields[$strOneCode] = true;
                $arCell['NA'] = false;
                if ($arSKUPropList[$strOneCode]['USER_TYPE'] == 'directory') {
                    $intValue = $arSKUPropList[$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
                    $arCell['VALUE'] = $intValue;
                } elseif ($arSKUPropList[$strOneCode]['PROPERTY_TYPE'] == 'L') {
                    $arCell['VALUE'] = (int) $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID'];
                } elseif ($arSKUPropList[$strOneCode]['PROPERTY_TYPE'] == 'E') {
                    $arCell['VALUE'] = (int) $arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE'];
                }
                $arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
            }

            $arRow[$strOneCode] = $arCell;
        }
        $arMatrix[$keyOffer] = $arRow;

        CIBlockPriceTools::setRatioMinPrice($arOffer, false);

        $arOffer['MORE_PHOTO'] = [];
        $arOffer['MORE_PHOTO_COUNT'] = 0;
        $arOffer['ALT_TITLE_GET'] = $arParams['ALT_TITLE_GET'];
        $offerSlider = CMax::getSliderForItemExt($arOffer, $arParams['OFFER_ADD_PICT_PROP'], true); // $arParams['ADD_DETAIL_TO_SLIDER'] == 'Y'

        $arOffer['MORE_PHOTO'] = $offerSlider;

        if($arOffer['MORE_PHOTO']) {
            foreach($arOffer['MORE_PHOTO'] as $i => $arImage) {
                if($arImage['ID']) {
                    $arOffer['MORE_PHOTO'][$i]['BIG']['src'] = CFile::GetPath($arImage['ID']);
                    $arOffer['MORE_PHOTO'][$i]['BIG']['width'] = $arImage['WIDTH'];
                    $arOffer['MORE_PHOTO'][$i]['BIG']['height'] = $arImage['HEIGHT'];
                    $arOffer['MORE_PHOTO'][$i]['SMALL'] = CFile::ResizeImageGet($arImage['ID'], ['width' => $arSizePict[$arParams['PICTURE_RATIO']][0], 'height' => $arSizePict[$arParams['PICTURE_RATIO']][1]], BX_RESIZE_IMAGE_PROPORTIONAL, true, []);
                    $arOffer['MORE_PHOTO'][$i]['THUMB'] = CFile::ResizeImageGet($arImage['ID'], ['width' => 100, 'height' => 100], BX_RESIZE_IMAGE_PROPORTIONAL, true, []);
                    $arOffer['MORE_PHOTO'][$i]['ALT'] = $arImage['ALT'] ?: $arOffer['NAME'];
                    $arOffer['MORE_PHOTO'][$i]['TITLE'] = $arImage['TITLE'] ?: $arOffer['NAME'];
                }
            }
        }

        $arOffer['MORE_PHOTO_COUNT'] = count($arOffer['MORE_PHOTO']);

        $boolSKUDisplayProps = !empty($arOffer['DISPLAY_PROPERTIES']);

        $arDouble[$arOffer['ID']] = true;
        $arNewOffers[$keyOffer] = $arOffer;
    }
    $arResult['OFFERS'] = $arNewOffers;
    $arResult['SHOW_OFFERS_PROPS'] = $boolSKUDisplayProps;

    $arUsedFields = $arSortFields = $arPropSKU = [];

    foreach ($arSKUPropIDs as $propkey => $strOneCode) {
        $boolExist = $arMatrixFields[$strOneCode];
        foreach ($arMatrix as $keyOffer => $arRow) {
            if ($boolExist) {
                if (!isset($arResult['OFFERS'][$keyOffer]['TREE'])) {
                    $arResult['OFFERS'][$keyOffer]['TREE'] = [];
                }
                $arResult['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$strOneCode]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
                $arResult['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
                $arUsedFields[$strOneCode] = true;
                $arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;

                $arPropSKU[$strOneCode][$arMatrix[$keyOffer][$strOneCode]['VALUE']] = $arSKUPropList[$strOneCode]['VALUES'][$arMatrix[$keyOffer][$strOneCode]['VALUE']];
            } else {
                unset($arMatrix[$keyOffer][$strOneCode]);
            }
        }

        if($arPropSKU[$strOneCode]) {
            // sort sku prop values
            Collection::sortByColumn($arPropSKU[$strOneCode], ['SORT' => [SORT_NUMERIC, SORT_ASC], 'NAME' => [SORT_STRING, SORT_ASC]]);
            $arSKUPropList[$strOneCode]['VALUES'] = $arPropSKU[$strOneCode];
        }
    }
    $arResult['OFFERS_PROP'] = $arUsedFields;
    $arResult['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

    unset($arPropSKU);

    if ($arParams['TYPE_SKU'] != 'TYPE_1') {
        if ($arParams['OFFERS_SORT_FIELD'] == 'REGION_PRICE' || $arParams['OFFERS_SORT_FIELD2'] == 'REGION_PRICE') {
            global $arRegion;
            if ($arRegion) {
                if(!$arRegion['PROPERTY_SORT_REGION_PRICE_VALUE'] || $arRegion['PROPERTY_SORT_REGION_PRICE_VALUE'] == 'component') {
                    $price = CCatalogGroup::GetList([], ['NAME' => $arParams['SORT_REGION_PRICE']], false, false, ['ID', 'NAME'])->GetNext();
                    $sortPrice = 'CATALOG_PRICE_'.$price['ID'];
                } else {
                    $sortPrice = 'CATALOG_PRICE_'.$arRegion['PROPERTY_SORT_REGION_PRICE_VALUE'];
                }
            } else {
                $price_name = ($arParams['SORT_REGION_PRICE'] ? $arParams['SORT_REGION_PRICE'] : 'BASE');
                $price = CCatalogGroup::GetList([], ['NAME' => $price_name], false, false, ['ID', 'NAME'])->GetNext();
                $sortPrice = 'CATALOG_PRICE_'.$price['ID'];
            }
            if ($arParams['OFFERS_SORT_FIELD'] == 'REGION_PRICE') {
                $arParams['OFFERS_SORT_FIELD'] = $sortPrice;
            }
            if ($arParams['OFFERS_SORT_FIELD2'] == 'REGION_PRICE') {
                $arParams['OFFERS_SORT_FIELD2'] = $sortPrice;
            }
        }

        $arSortFields[strtoupper($arParams['OFFERS_SORT_FIELD'])] = ($arParams['OFFERS_SORT_ORDER'] == 'asc' ? SORT_ASC : SORT_DESC);
        $arSortFields[strtoupper($arParams['OFFERS_SORT_FIELD2'])] = ($arParams['OFFERS_SORT_ORDER2'] == 'asc' ? SORT_ASC : SORT_DESC);
    }
    Collection::sortByColumn($arResult['OFFERS'], $arSortFields);

    /* offers & nabor */
    $offerSet = [];
    if (!empty($arIDS) && CBXFeatures::IsFeatureEnabled('CatCompleteSet')) {
        $offerSet = array_fill_keys($arIDS, false);
        $rsSets = CCatalogProductSet::getList(
            [],
            [
                '@OWNER_ID' => $arIDS,
                '=SET_ID' => 0,
                '=TYPE' => CCatalogProductSet::TYPE_GROUP,
            ],
            false,
            false,
            ['ID', 'OWNER_ID']
        );
        while ($arSet = $rsSets->Fetch()) {
            $arSet['OWNER_ID'] = (int) $arSet['OWNER_ID'];
            $offerSet[$arSet['OWNER_ID']] = true;
            $arResult['OFFER_GROUP'] = true;
        }
        if ($offerSet[$arResult['ID']]) {
            foreach ($offerSet as &$setOfferValue) {
                if ($setOfferValue === false) {
                    $setOfferValue = true;
                }
            }
            unset($setOfferValue);
            unset($offerSet[$arResult['ID']]);
        }
        if ($arResult['OFFER_GROUP']) {
            $offerSet = array_filter($offerSet);
            $arResult['OFFER_GROUP_VALUES'] = array_keys($offerSet);
        }
    }

    $arMatrix = [];
    $intSelected = -1;
    $arResult['MIN_PRICE'] = false;
    $arResult['MIN_BASIS_PRICE'] = false;
    $arPropsSKU = [];
    $arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);

    $postfix = '';
    global $arSite;
    if(Bitrix\Main\Config\Option::get('aspro.max', 'HIDE_SITE_NAME_TITLE', 'N') == 'N') {
        $postfix = ' - '.$arSite['SITE_NAME'];
    }

    $bChangeTitleItem = Bitrix\Main\Config\Option::get('aspro.max', 'CHANGE_TITLE_ITEM_DETAIL', 'N') === 'Y';

    if($arParams['TYPE_SKU'] == 'TYPE_1' && $arResult['OFFERS']) {
        if ($arParams['OID']) {
            $arResult['OFFER_ID_SELECTED'] = $arParams['OID'];
        }
        foreach ($arResult['OFFERS'] as $keyOffer => $arOffer) {
            if ($arResult['OFFER_ID_SELECTED'] > 0) {
                $foundOffer = ($arResult['OFFER_ID_SELECTED'] == $arOffer['ID']);
            } else {
                $foundOffer = $arOffer['CAN_BUY'];
            }

            if ($foundOffer) {
                $intSelected = $keyOffer;
            }
            if (empty($arResult['MIN_PRICE']) /* && $arOffer['CAN_BUY'] */) {
                // $arResult['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
                $arResult['MIN_PRICE'] = $arOffer['MIN_PRICE'];
                $arResult['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
            }

            if (isset($offerSet[$arOffer['ID']])) {
                $arOffer['OFFER_GROUP'] = true;
                $arResult['OFFERS'][$keyOffer]['OFFER_GROUP'] = true;
            }
            reset($arOffer['MORE_PHOTO']);
        }

        if ($intSelected == -1) {
            $intSelected = 0;
        }
        $arResult['OFFERS_SELECTED'] = $intSelected;

        foreach ($arResult['OFFERS'] as $keyOffer => $arOffer) {
            $arResult['OFFERS'][$keyOffer]['PREVIEW_PICTURE_FIELD'] = $arOffer['PREVIEW_PICTURE'];
            /* if($arResult['OFFERS_SELECTED'] !== $keyOffer) {
                continue;
            }
            $arSKUProps = false;
            if (!empty($arOffer['DISPLAY_PROPERTIES'])) {
                $boolSKUDisplayProps = true;
                $arSKUProps = [];
                foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp) {
                    if ($arOneProp['PROPERTY_TYPE'] == 'F' || ($arParams['OFFER_TREE_PROPS'] && in_array($arOneProp['CODE'], $arParams['OFFER_TREE_PROPS']))) {
                        continue;
                    }
                    $arOneProp['SHOW_HINTS'] = $arParams['SHOW_HINTS'];
                    $arSKUProps[] = [
                        'NAME' => $arOneProp['NAME'],
                        'VALUE' => (is_array($arOneProp['DISPLAY_VALUE']) && count($arOneProp['DISPLAY_VALUE']) > 1 ? implode(', ', $arOneProp['DISPLAY_VALUE']) : $arOneProp['DISPLAY_VALUE']),
                        'CODE' => $arOneProp['CODE'],
                        'SHOW_HINTS' => $arParams['SHOW_HINTS'],
                        'HINT' => $arOneProp['HINT'],
                    ];
                    $arPropsSKU[] = $arOneProp;
                }
                unset($arOneProp);
            }

            $totalCount = CMax::GetTotalCount($arOffer, $arParams);
            $arOffer['IS_OFFER'] = 'Y';
            $arOffer['IBLOCK_ID'] = $arResult['IBLOCK_ID'];
            $arPriceTypeID = [];

            // get additional query for OFFER price when PRICE_RANGE will start not from 1
            if (!$arOffer['PRICES'] && $arResult['CAT_PRICES']) {
                if ($arOffer['ITEM_PRICE_MODE'] === 'Q') {
                    $arOfferPrices = CIBlockElement::GetList($arOrder, ['ID' => $arOffer['ID']], false, false, array_merge(['ID', 'NAME'], array_column($arResult['CAT_PRICES'], 'SELECT')))->Fetch();
                    $arOffer['PRICES'] = CIBlockPriceTools::GetItemPrices($arOffer['IBLOCK_ID'], $arResult['CAT_PRICES'], $arOfferPrices, 'Y', $arConvertParams);
                    if (!empty($arOffer['PRICES'])) {
                        foreach ($arOffer['PRICES'] as &$arOnePrice) {
                            if ($arOnePrice['MIN_PRICE'] == 'Y') {
                                $arOffer['MIN_PRICE'] = $arOnePrice;
                                break;
                            }
                        }
                        unset($arOnePrice);
                    }
                }
            }

            if($arOffer['PRICES']) {
                foreach($arOffer['PRICES'] as $priceKey => $arOfferPrice) {
                    if($arOfferPrice['CAN_BUY'] == 'Y') {
                        $arPriceTypeID[] = $arOfferPrice['PRICE_ID'];
                    }
                    if($arOffer['CATALOG_GROUP_NAME_'.$arOfferPrice['PRICE_ID']]) {
                        $arOffer['PRICES'][$priceKey]['GROUP_NAME'] = $arOffer['CATALOG_GROUP_NAME_'.$arOfferPrice['PRICE_ID']];
                    }
                }
            }
            // format offer prices when USE_PRICE_COUNT
            $sPriceMatrix = '';
            if($arParams['USE_PRICE_COUNT']) {
                if(function_exists('CatalogGetPriceTableEx') && (isset($arOffer['PRICE_MATRIX'])) && !$arOffer['PRICE_MATRIX'] && $arPriceTypeID) {
                    $arOffer['PRICE_MATRIX'] = CatalogGetPriceTableEx($arOffer['ID'], 0, $arPriceTypeID, 'Y', $arConvertParams);
                    if(count($arOffer['PRICE_MATRIX']['ROWS']) <= 1) {
                        $arOffer['PRICE_MATRIX'] = '';
                    }
                    $arResult['OFFERS'][$keyOffer]['PRICE_MATRIX'] = $arOffer['PRICE_MATRIX'];
                }

                $arOffer = array_merge($arOffer, CMax::formatPriceMatrix($arOffer));
                $sPriceMatrix = CMax::showPriceMatrix($arOffer, $arParams, $arOffer['~CATALOG_MEASURE_NAME']);
            }

            $arAddToBasketData = CMax::GetAddToBasketArray($arOffer, $totalCount, $arParams['DEFAULT_COUNT'], $arParams['BASKET_URL'], true, $arItemIDs['ALL_ITEM_IDS'], 'btn-lg', $arParams);
            $arAddToBasketData['HTML'] = str_replace('data-item', 'data-props="'.$arOfferProps.'" data-item', $arAddToBasketData['HTML']);

            $firstPhoto = current($arOffer['MORE_PHOTO']);
            $arOneRow = [
                'ID' => $arOffer['ID'],
                'NAME' => $arOffer['~NAME'],
                'IBLOCK_ID' => $arOffer['IBLOCK_ID'],
                'TREE' => $arOffer['TREE'],
                'PRICE' => $arOffer['MIN_PRICE'],
                'PRICES' => $arOffer['PRICES'],
                'POSTFIX' => $postfix,
                'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                'SHOW_DISCOUNT_TIME_EACH_SKU' => $arParams['SHOW_DISCOUNT_TIME_EACH_SKU'],
                'SHOW_ARTICLE_SKU' => $arParams['SHOW_ARTICLE_SKU'],
                'ARTICLE_SKU' => ($arParams['SHOW_ARTICLE_SKU'] == 'Y' ? (isset($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']) && $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] ? '<span class="block_title" itemprop="name">'.$arResult['PROPERTIES']['CML2_ARTICLE']['NAME'].': </span><span class="value" itemprop="value">'.$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'].'</span>' : '') : ''),
                'PRICE_MATRIX' => $sPriceMatrix,
                'BASIS_PRICE' => $arOffer['MIN_PRICE'],
                'PRICES_HTML' => Aspro\Functions\CAsproMaxItem::showItemPrices($arParams, $arOffer['PRICES'], $arOffer['~CATALOG_MEASURE_NAME'], $arOffer['MIN_PRICE']['ID'], $arParams['SHOW_DISCOUNT_PERCENT_NUMBER'] == 'Y' ? 'N' : 'Y', false, true),
                'POPUP_VIDEO' => (isset($arOffer['PROPERTIES']['POPUP_VIDEO']) && $arOffer['PROPERTIES']['POPUP_VIDEO']['VALUE'] ? $arOffer['PROPERTIES']['POPUP_VIDEO']['VALUE'] : $arResult['PROPERTIES']['POPUP_VIDEO']['VALUE']),
                'DISPLAY_PROPERTIES' => $arSKUProps,
                'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
                'DETAIL_PICTURE' => $firstPhoto,
                'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
                'MAX_QUANTITY' => $totalCount,
                'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
                'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
                'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
                'OFFER_GROUP' => (isset($offerSet[$arOffer['ID']]) && $offerSet[$arOffer['ID']]),
                'CAN_BUY' => ($arAddToBasketData['CAN_BUY'] ? 'Y' : $arOffer['CAN_BUY']),
                'CATALOG_SUBSCRIBE' => $arOffer['CATALOG_SUBSCRIBE'],
                'SLIDER' => $arOffer['MORE_PHOTO'],
                'SLIDER_COUNT' => $arOffer['MORE_PHOTO_COUNT'],
                'AVAILIABLE' => CMax::GetQuantityArray($totalCount, [], $arResult['STORES_COUNT'] ? 'Y' : 'N'),
                'URL' => $arOffer['DETAIL_PAGE_URL'],
                'CONFIG' => $arAddToBasketData,
                'HTML' => $arAddToBasketData['HTML'],
                'ACTION' => $arAddToBasketData['ACTION'],
                'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                'TYPE_SKU' => $arParams['TYPE_SKU'],
                'SHOW_ONE_CLICK_BUY' => $arParams['SHOW_ONE_CLICK_BUY'],
                'ONE_CLICK_BUY' => GetMessage('ONE_CLICK_BUY'),
                'ONE_CLICK_BUY_HTML' => Aspro\Functions\CAsproMax::showItemOCB($arAddToBasketData, $arOffer, $arParams, true, ''),
                'OFFER_PROPS' => $arOfferProps,
                'TYPE_PROP' => $arParams['PROPERTIES_DISPLAY_TYPE'],
                'NO_PHOTO' => $arEmptyPreview,
                'SHOW_MEASURE' => ($arParams['SHOW_MEASURE'] == 'Y' ? 'Y' : 'N'),
                'SHOW_POPUP_PRICE' => $arParams['SHOW_POPUP_PRICE'],
                'PRODUCT_ID' => $arResult['ID'],
                'PARENT_PICTURE' => $arResult['PREVIEW_PICTURE'],
                'ACTIVE' => $arOffer['ACTIVE'],
                'SUBSCRIPTION' => true,
                'ITEM_PRICE_MODE' => $arOffer['ITEM_PRICE_MODE'],
                'ITEM_PRICES' => $arOffer['ITEM_PRICES'],
                'ITEM_PRICE_SELECTED' => $arOffer['ITEM_PRICE_SELECTED'],
                'ITEM_QUANTITY_RANGES' => $arOffer['ITEM_QUANTITY_RANGES'],
                'ITEM_QUANTITY_RANGE_SELECTED' => $arOffer['ITEM_QUANTITY_RANGE_SELECTED'],
                'ITEM_MEASURE_RATIOS' => $arOffer['ITEM_MEASURE_RATIOS'],
                'ITEM_MEASURE_RATIO_SELECTED' => $arOffer['ITEM_MEASURE_RATIO_SELECTED'],
                'ADDITIONAL_GALLERY' => $arResult['ADDITIONAL_GALLERY'][$arOffer['ID']],
            ];
            if($arOneRow['PRICE']['DISCOUNT_DIFF']) {
                $percent = round(($arOneRow['PRICE']['DISCOUNT_DIFF'] / $arOneRow['PRICE']['VALUE']) * 100, 2);
                $arOneRow['PRICE']['DISCOUNT_DIFF_PERCENT_RAW'] = '-'.$percent.'%';
            }

            $arMatrix[$keyOffer] = $arOneRow; */
        }
    }
    /* set min_price_id */
    if($arParams['TYPE_SKU'] != 'TYPE_1' && $arResult['OFFERS']) {
        // $arResult['MIN_PRICE'] = CMax::getMinPriceFromOffersExt(
        // 	$arResult['OFFERS'],
        // 	$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
        // );
        $arTmpProps = [];

        $minItemPriceID = 0;
        $minItemPrice = 0;
        $minItemPriceFormat = '';
        $imgOffers = true;

        $iconSize = (CMax::GetFrontParametrValue('CATALOG_COMPARE') != 'N' ? 1 : 0);

        $bCheckDelay = false;
        foreach ($arResult['OFFERS'] as $keyOffer => $arOffer) {
            $imgID = ($arOffer['PREVIEW_PICTURE'] ? $arOffer['PREVIEW_PICTURE'] : ($arOffer['DETAIL_PICTURE'] ? $arOffer['DETAIL_PICTURE'] : false));
            if(!$imgID) {
                $imgOffers = false;
            }

            if (TSolution\Product\Price::isRangePriceMode($arOffer)) {
                $arOffer = array_merge($arOffer, TSolution\Product\Price::resolveWhenEmptyPriceMatrix($arResult, $arOffer, $arParams));
            }

            if($arOffer['MIN_PRICE']['CAN_ACCESS']) {
                if($arOffer['MIN_PRICE']['DISCOUNT_VALUE'] < $arOffer['MIN_PRICE']['VALUE']) {
                    $minOfferPrice = $arOffer['MIN_PRICE']['DISCOUNT_VALUE'];
                    $minOfferPriceFormat = $arOffer['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
                    $minOfferPriceID = $arOffer['MIN_PRICE']['PRICE_ID'];
                } else {
                    $minOfferPrice = $arOffer['MIN_PRICE']['VALUE'];
                    $minOfferPriceFormat = $arOffer['MIN_PRICE']['PRINT_VALUE'];
                    $minOfferPriceID = $arOffer['MIN_PRICE']['PRICE_ID'];
                }

                if($minItemPrice > 0 && $minOfferPrice < $minItemPrice) {
                    $minItemPrice = $minOfferPrice;
                    $minItemPriceFormat = $minOfferPriceFormat;
                    $minItemPriceID = $minOfferPriceID;
                    $minItemID = $arOffer['ID'];
                } elseif($minItemPrice == 0) {
                    $minItemPrice = $minOfferPrice;
                    $minItemPriceFormat = $minOfferPriceFormat;
                    $minItemPriceID = $minOfferPriceID;
                    $minItemID = $arOffer['ID'];
                }
            }
            if($arParams['OFFERS_PROPERTY_CODE']) {
                foreach($arParams['OFFERS_PROPERTY_CODE'] as $code) {
                    if(!isset($arTmpProps[$code])) {
                        $arTmpProps[$code] = [
                            'NAME' => $arOffer['PROPERTIES'][$code]['NAME'],
                            'CODE' => $code,
                            'HINT' => $arOffer['PROPERTIES'][$code]['HINT'],
                            'ID' => $arOffer['PROPERTIES'][$code]['ID'],
                            'PROPERTY_TYPE' => $arOffer['PROPERTIES'][$code]['PROPERTY_TYPE'],
                            'IS_EMPTY' => true,
                        ];
                    }

                    if (!$arOffer['PROPERTIES'][$code]['VALUE']) {
                        $arResult['OFFERS'][$keyOffer][] = GetMessage('EMPTY_VALUE_SKU');
                        continue;
                    }

                    $arTmpProps[$code]['IS_EMPTY'] = false;

                    if(is_array($arOffer['PROPERTIES'][$code]['VALUE'])) {
                        if($arOffer['PROPERTIES'][$code]['PROPERTY_TYPE'] == 'E') {
                            $arResult['OFFERS'][$keyOffer][] = implode('/', $arOffer['PROPERTIES'][$code]['DISPLAY_VALUE']);
                        } else {
                            $arResult['OFFERS'][$keyOffer][] = implode('/', $arOffer['PROPERTIES'][$code]['VALUE']);
                        }
                    } else {
                        if($arOffer['PROPERTIES'][$code]['PROPERTY_TYPE'] == 'E') {
                            $arResult['OFFERS'][$keyOffer][] = $arOffer['PROPERTIES'][$code]['DISPLAY_VALUE'];
                        } else {
                            $arResult['OFFERS'][$keyOffer][] = $arOffer['PROPERTIES'][$code]['VALUE'];
                        }
                    }
                }
            }

            // format offer prices when USE_PRICE_COUNT
            if($arParams['USE_PRICE_COUNT']) {
                $arPriceTypeID = [];
                if($arOffer['PRICES']) {
                    foreach($arOffer['PRICES'] as $priceKey => $arOfferPrice) {
                        if($arOffer['CATALOG_GROUP_NAME_'.$arOfferPrice['PRICE_ID']]) {
                            $arPriceTypeID[] = $arOfferPrice['PRICE_ID'];
                            $arOffer['PRICES'][$priceKey]['GROUP_NAME'] = $arOffer['CATALOG_GROUP_NAME_'.$arOfferPrice['PRICE_ID']];
                        }
                    }
                }
                if(function_exists('CatalogGetPriceTableEx') && (isset($arOffer['PRICE_MATRIX'])) && !$arOffer['PRICE_MATRIX']) {
                    $arOffer['PRICE_MATRIX'] = CatalogGetPriceTableEx($arOffer['ID'], 0, $arPriceTypeID, 'Y', $arConvertParams);
                }

                if(count($arOffer['PRICE_MATRIX']['ROWS']) <= 1) {
                    $arOffer['PRICE_MATRIX'] = '';
                }

                $arResult['OFFERS'][$keyOffer] = array_merge($arOffer, CMax::formatPriceMatrix($arOffer));
            }
            $arOffer['IS_OFFER'] = 'Y';
            $arOffer['IBLOCK_ID'] = $arResult['IBLOCK_ID'];
            $arResult['OFFERS'][$keyOffer]['TOTAL_COUNT'] = CMax::GetTotalCount($arOffer, $arParams);
            $arResult['OFFERS'][$keyOffer]['ADD_TO_BASKET_DATA'] = CMax::GetAddToBasketArray($arOffer, $arResult['OFFERS'][$keyOffer]['TOTAL_COUNT'], $arParams['DEFAULT_COUNT'], $arParams['BASKET_URL'], false, [], 'small', $arParams);

            if($arParams['DISPLAY_WISH_BUTTONS'] != 'N') {
                if($arResult['OFFERS'][$keyOffer]['ADD_TO_BASKET_DATA']['CAN_BUY']) {
                    if(CMax::checkShowDelay($arParams, $arResult['OFFERS'][$keyOffer]['TOTAL_COUNT'], $arOffer)) {
                        $bCheckDelay = true;
                    }
                }
            }
        }
        if($bCheckDelay) {
            ++$iconSize;
        }

        $arResult['ICONS_SIZE'] = $iconSize;

        $arResult['MIN_PRICE']['MIN_PRICE_ID'] = $minItemPriceID;
        $arResult['MIN_PRICE']['MIN_ITEM_ID'] = $minItemID;

        $arResult['SKU_PROPERTIES'] = $arTmpProps;
        $arResult['SKU_IMD'] = $imgOffers;
    }

    if ($intSelected == -1) {
        $intSelected = 0;
    }

    $arResult['JS_OFFERS'] = $arMatrix;
    $arResult['OFFERS_SELECTED'] = $intSelected;

    $arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];

    if($arParams['TYPE_SKU'] == 'TYPE_1' && $arResult['OFFERS']) {
        // more photo for current offer
        if(isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]) && is_array($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO'])) {
            $arResult['MORE_PHOTO'] = $bEmptyPictureProduct && $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO_COUNT'] > 0 ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO'] : array_merge($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO'], $arResult['MORE_PHOTO']);
            $arResult['MORE_PHOTO_COUNT'] = $bEmptyPictureProduct && $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO_COUNT'] > 0 ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO_COUNT'] : $arResult['MORE_PHOTO_COUNT'] + $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO_COUNT'];
        }

        $ipropValues = new Iblock\InheritedProperty\ElementValues($arResult['SKU_IBLOCK_ID'], $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']);
        $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['IPROPERTY_VALUES'] = $ipropValues->getValues();
    }
}

if ($arResult['MODULES']['catalog'] && $arResult['CATALOG']) {
    if ($arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT || $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET) {
        CIBlockPriceTools::setRatioMinPrice($arResult, false);
        $arResult['MIN_BASIS_PRICE'] = $arResult['MIN_PRICE'];
    }
    if (CBXFeatures::IsFeatureEnabled('CatCompleteSet') && $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT) {
        $rsSets = CCatalogProductSet::getList(
            [],
            [
                '@OWNER_ID' => $arResult['ID'],
                '=SET_ID' => 0,
                '=TYPE' => CCatalogProductSet::TYPE_GROUP,
            ],
            false,
            false,
            ['ID', 'OWNER_ID']
        );
        if ($arSet = $rsSets->Fetch()) {
            $arResult['OFFER_GROUP'] = true;
        }
    }

    if($arResult['OFFERS']) {
        foreach($arResult['OFFERS'] as $keyOffer => $arOffer) {
            if (TSolution\Product\Price::isRangePriceMode($arOffer)) {
                $arOffer = array_merge($arOffer, TSolution\Product\Price::resolveWhenEmptyPriceMatrix($arResult, $arOffer, $arParams));
            }

            // format prices when USE_PRICE_COUNT
            $arPriceTypeID = [];
            if($arOffer['PRICES']) {
                foreach($arOffer['PRICES'] as $priceKey => $arOfferPrice) {
                    if($arOffer['CATALOG_GROUP_NAME_'.$arOfferPrice['PRICE_ID']]) {
                        $arPriceTypeID[] = $arOfferPrice['PRICE_ID'];
                        $arOffer['PRICES'][$priceKey]['GROUP_NAME'] = $arOffer['CATALOG_GROUP_NAME_'.$arOfferPrice['PRICE_ID']];
                    }
                }
            }
            if(function_exists('CatalogGetPriceTableEx') && (isset($arOffer['PRICE_MATRIX'])) && !$arOffer['PRICE_MATRIX']) {
                $arOffer['PRICE_MATRIX'] = CatalogGetPriceTableEx($arOffer['ID'], 0, $arPriceTypeID, 'Y', $arConvertParams);
            }
            $arOffer['FIX_PRICE_MATRIX'] = CMax::checkPriceRangeExt($arOffer);
            $arResult['OFFERS'][$keyOffer] = array_merge($arOffer, CMax::formatPriceMatrix($arOffer));
        }
    }
    if (TSolution\Product\Price::isRangePriceMode($arResult)) {
        $arResult = array_merge($arResult, TSolution\Product\Price::resolveWhenEmptyPriceMatrix($arResult, params: $arParams));
        $arResult['FIX_PRICE_MATRIX'] = CMax::checkPriceRangeExt($arResult);
    }

    if($arResult['OFFERS']) {
        $arResult['MAX_PRICE'] = CMax::getMaxPriceFromOffersExt(
            $arResult['OFFERS'],
            $boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
        );
        $arResult['MIN_PRICE'] = CMax::getMinPriceFromOffersExt(
            $arResult['OFFERS'],
            $boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
        );

        $arFirstSkuPicture = [];
        $bNeedFindPicture = (CMax::GetFrontParametrValue('SHOW_FIRST_SKU_PICTURE') == 'Y') && $bEmptyPictureProduct;
        if($bNeedFindPicture) {
            $bFindPicture = false;

            foreach ($arResult['OFFERS'] as $keyOffer => $arOffer) {
                if (($arOffer['DETAIL_PICTURE'] && $arOffer['PREVIEW_PICTURE']) || (!$arOffer['DETAIL_PICTURE'] && $arOffer['PREVIEW_PICTURE'])) {
                    $arOffer['DETAIL_PICTURE'] = $arOffer['PREVIEW_PICTURE'];
                }

                if ($arOffer['DETAIL_PICTURE'] && !$bFindPicture) {
                    $arResult['FIRST_SKU_PICTURE'] = CFile::ResizeImageGet($arOffer['DETAIL_PICTURE'], ['width' => 350, 'height' => 350], BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    $bFindPicture = true;
                    break;
                }
            }
        }
    }

    // format prices when USE_PRICE_COUNT
    $arResult = array_merge($arResult, CMax::formatPriceMatrix($arResult));
}

/* complect */
$arResult['SET_ITEMS_QUANTITY'] = $arResult['SET_ITEMS'] = [];

if($arParams['SHOW_KIT_PARTS'] == 'Y' && $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET) {
    // const TYPE_SET = 1;
    // const TYPE_GROUP = 2;
    $arSetItems = $arSetItemsOtherID = [];

    $arSets = CCatalogProductSet::getAllSetsByProduct($arResult['ID'], 1);

    if (is_array($arSets) && !empty($arSets)) {
        foreach($arSets as $key => $set) {
            Collection::sortByColumn($set['ITEMS'], ['SORT' => SORT_ASC]);
            foreach($set['ITEMS'] as $i => $val) {
                $arSetItems[] = $val['ITEM_ID'];
                $arSetItemsOtherID[$val['ITEM_ID']]['SORT'] = $val['SORT'];
                $arSetItemsOtherID[$val['ITEM_ID']]['QUANTITY'] = $val['QUANTITY'];
            }
        }
    }
    $arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);

    $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'];
    $arPriceTypeID = [];
    foreach($arResultPrices as &$value) {
        if($value['CAN_VIEW'] && $value['CAN_BUY']) {
            $arSelect[] = $value['SELECT'];
            $arPriceTypeID[] = $value['ID'];
        }
    }
    if (!empty($arSetItems)) {
        $db_res = CIBlockElement::GetList(['SORT' => 'ASC'], ['ID' => $arSetItems], false, false, $arSelect);
        $bShowQuantity = false;
        while ($res = $db_res->GetNext()) {
            $res['SORT'] = $arSetItemsOtherID[$res['ID']]['SORT'];
            $res['QUANTITY'] = $arSetItemsOtherID[$res['ID']]['QUANTITY'];

            if(($res['DETAIL_PICTURE'] && $res['PREVIEW_PICTURE']) || (!$res['DETAIL_PICTURE'] && $res['PREVIEW_PICTURE'])) {
                $res['DETAIL_PICTURE'] = $res['PREVIEW_PICTURE'];
            }

            $arResult['SET_ITEMS'][$res['ID']] = $res;
            if($arSetItemsOtherID[$res['ID']]['QUANTITY'] > 1) {
                $bShowQuantity = true;
            }
        }
        $arResult['SET_ITEMS_QUANTITY'] = $bShowQuantity;
        $arResult['SET_ITEMS'] = array_values($arResult['SET_ITEMS']);
        Collection::sortByColumn($arResult['SET_ITEMS'], ['SORT' => SORT_ASC]);
    }

    $bCatalog = CModule::IncludeModule('catalog');

    if (is_array($arResult['SET_ITEMS']) && !empty($arResult['SET_ITEMS'])) {
        foreach($arResult['SET_ITEMS'] as $key => $setItem) {
            $arResult['SET_ITEMS'][$key]['MEASURE'] = Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure($setItem['ID']);
            if($arParams['USE_PRICE_COUNT']) {
                if($bCatalog) {
                    $arResult['SET_ITEMS'][$key]['PRICE_MATRIX'] = CatalogGetPriceTableEx($arResult['SET_ITEMS'][$key]['ID'], 0, $arPriceTypeID, 'Y', $arConvertParams);
                    foreach($arResult['SET_ITEMS'][$key]['PRICE_MATRIX']['COLS'] as $keyColumn => $arColumn) {
                        $arResult['SET_ITEMS'][$key]['PRICE_MATRIX']['COLS'][$keyColumn]['NAME_LANG'] = htmlspecialcharsbx($arColumn['NAME_LANG']);
                        /*if(count($arResult["SET_ITEMS"][$key]['PRICE_MATRIX']['ROWS']) > 1)
                        {*/
                        $setItem = array_merge($arResult['SET_ITEMS'][$key], CMax::formatPriceMatrix($arResult['SET_ITEMS'][$key]));
                        $arResult['SET_ITEMS'][$key] = $setItem;
                        // }
                    }

                    $arResult['SET_ITEMS'][$key]['FIX_PRICE_MATRIX']['PRICE_SELECT'] = 0;
                    // $arResult["SET_ITEMS"][$key]["FIX_PRICE_MATRIX"] = CMax::checkPriceRangeExt($setItem);

                    foreach($arResult['SET_ITEMS'][$key]['PRICE_MATRIX']['ROWS'] as $range => $arInterval) {
                        $minimalPrice = null;
                        foreach($arResult['SET_ITEMS'][$key]['PRICE_MATRIX']['MATRIX'] as $arPrice) {
                            if($arPrice[$range]) {
                                if($minimalPrice === null || $minimalPrice['DISCOUNT_PRICE'] > $arPrice[$range]['DISCOUNT_PRICE']) {
                                    if($arPrice[$range]['PRICE'] > $arPrice[$range]['DISCOUNT_PRICE']) {
                                        $arPrice[$range]['PERCENT'] = round((($arPrice[$range]['PRICE'] - $arPrice[$range]['DISCOUNT_PRICE']) / $arPrice[$range]['PRICE']) * 100);
                                        $arPrice[$range]['DISCOUNT'] = ($arPrice[$range]['PRICE'] - $arPrice[$range]['DISCOUNT_PRICE']);
                                        $arPrice[$range]['PRINT_DISCOUNT'] = CCurrencyLang::CurrencyFormat($arPrice[$range]['PRICE'] - $arPrice[$range]['DISCOUNT_PRICE'], $arPrice[$range]['CURRENCY'], true);
                                        $arPrice[$range]['BASE_PRICE'] = $arPrice[$range]['PRICE'];
                                        $arPrice[$range]['PRICE'] = $arPrice[$range]['DISCOUNT_PRICE'];
                                        unset($arPrice[$range]['PRINT_PRICE']);
                                    }
                                    $minimalPrice = $arPrice[$range];
                                }
                            }
                        }
                        $arResult['SET_ITEMS'][$key]['ITEM_PRICES'][$range] = $minimalPrice;
                    }
                }
            } else {
                $arResult['SET_ITEMS'][$key]['PRICES'] = CIBlockPriceTools::GetItemPrices($arParams['IBLOCK_ID'], $arResultPrices, $arResult['SET_ITEMS'][$key], $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);
                if (!empty($arResult['SET_ITEMS'][$key]['PRICES'])) {
                    foreach ($arResult['SET_ITEMS'][$key]['PRICES'] as &$arOnePrice) {
                        if ($arOnePrice['MIN_PRICE'] == 'Y') {
                            $arResult['SET_ITEMS'][$key]['MIN_PRICE'] = $arOnePrice;
                            break;
                        }
                    }
                    unset($arOnePrice);
                }
            }
        }
    }
}

if(!empty($arResult['DISPLAY_PROPERTIES'])) {
    foreach($arResult['DISPLAY_PROPERTIES'] as $propKey => $arDispProp) {
        if($arDispProp['PROPERTY_TYPE'] == 'F') {
            unset($arResult['DISPLAY_PROPERTIES'][$propKey]);
        }
    }
}

if($arSKUPropList) {
    foreach($arSKUPropList as $keySKU => $arPropSKU) {
        if(!$arPropSKU['HINT']) {
            $arTmp = CIBlockProperty::GetByID($arPropSKU['ID'], $arResult['SKU_IBLOCK_ID'])->Fetch();
            $arSKUPropList[$keySKU]['HINT'] = $arTmp['HINT'];
        }
        $arSKUPropList[$keySKU]['SHOW_HINTS'] = $arParams['SHOW_HINTS'];
    }
}

$arResult['SKU_PROPS'] = $arSKUPropList;
$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;

$arResult['CURRENCIES'] = [];
if ($arResult['MODULES']['currency']) {
    if ($boolConvert) {
        $currencyFormat = CCurrencyLang::GetFormatDescription($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
        $arResult['CURRENCIES'] = [
            [
                'CURRENCY' => $arResult['CONVERT_CURRENCY']['CURRENCY_ID'],
                'FORMAT' => [
                    'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
                    'DEC_POINT' => $currencyFormat['DEC_POINT'],
                    'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
                    'DECIMALS' => $currencyFormat['DECIMALS'],
                    'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
                    'HIDE_ZERO' => $currencyFormat['HIDE_ZERO'],
                ],
            ],
        ];
        unset($currencyFormat);
    } else {
        $currencyIterator = CurrencyTable::getList([
            'select' => ['CURRENCY'],
            'filter' => ['BASE' => 'Y'],
        ]);
        while ($currency = $currencyIterator->fetch()) {
            $currencyFormat = CCurrencyLang::GetFormatDescription($currency['CURRENCY']);
            $arResult['CURRENCIES'][] = [
                'CURRENCY' => $currency['CURRENCY'],
                'FORMAT' => [
                    'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
                    'DEC_POINT' => $currencyFormat['DEC_POINT'],
                    'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
                    'DECIMALS' => $currencyFormat['DECIMALS'],
                    'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
                    'HIDE_ZERO' => $currencyFormat['HIDE_ZERO'],
                ],
            ];
        }
        unset($currencyFormat, $currency, $currencyIterator);
    }
}

/* akc */
$arResult['STOCK'] = Aspro\Functions\CAsproMax::getLinkedItems($arResult, 'LINK_SALE', $arParams);

/* services */
$arResult['SERVICES'] = Aspro\Functions\CAsproMax::getLinkedItems($arResult, 'SERVICES', $arParams);

/* news */
$arResult['NEWS'] = Aspro\Functions\CAsproMax::getLinkedItems($arResult, 'LINK_NEWS', $arParams);

/* blog */
$arResult['BLOG'] = Aspro\Functions\CAsproMax::getLinkedItems($arResult, 'LINK_BLOG', $arParams);
if ($arParams['LINKED_BLOG']) {
    $arResult['BLOG'] = array_merge((array) $arResult['BLOG'], (array) $arParams['LINKED_BLOG']);
}

/* associated */
if($arParams['LINKED_FILTER_BY_PROP']['ASSOCIATED'] || $arParams['LINKED_FILTER_BY_FILTER']['ASSOCIATED']) {
    if($arParams['LINKED_FILTER_BY_PROP']['ASSOCIATED']) {
        $arResult['ASSOCIATED'] = ['ID' => $arParams['LINKED_FILTER_BY_PROP']['ASSOCIATED']];
    }

    $arResult['ASSOCIATED']['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
    CMax::makeElementFilterInRegion($arResult['ASSOCIATED']);

    if($arParams['LINKED_FILTER_BY_FILTER']['ASSOCIATED']['FILTER']) {
        $arResult['ASSOCIATED'][] = $arParams['LINKED_FILTER_BY_FILTER']['ASSOCIATED']['FILTER'];
    }
}

/* similar */
if($arParams['LINKED_FILTER_BY_PROP']['EXPANDABLES'] || $arParams['LINKED_FILTER_BY_FILTER']['EXPANDABLES']) {
    if($arParams['LINKED_FILTER_BY_PROP']['EXPANDABLES']) {
        $arResult['EXPANDABLES'] = ['ID' => $arParams['LINKED_FILTER_BY_PROP']['EXPANDABLES']];
    }

    $arResult['EXPANDABLES']['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
    CMax::makeElementFilterInRegion($arResult['EXPANDABLES']);

    if($arParams['LINKED_FILTER_BY_FILTER']['EXPANDABLES']['FILTER']) {
        $arResult['EXPANDABLES'][] = $arParams['LINKED_FILTER_BY_FILTER']['EXPANDABLES']['FILTER'];
    }
}

/* brand item */
$arBrand = [];
if(($arResult['DISPLAY_PROPERTIES']['BRAND']['VALUE'] ?? false) && $arResult['PROPERTIES']['BRAND']['LINK_IBLOCK_ID']) {
    $arBrand = CMaxCache::CIBLockElement_GetList(['CACHE' => ['MULTI' => 'N', 'TAG' => CMaxCache::GetIBlockCacheTag($arResult['PROPERTIES']['BRAND']['LINK_IBLOCK_ID'])]], ['IBLOCK_ID' => $arResult['PROPERTIES']['BRAND']['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y', 'ID' => $arResult['DISPLAY_PROPERTIES']['BRAND']['VALUE']]);

    $arBrand['CATALOG_PAGE_URL'] = $arResult['SECTION']['SECTION_PAGE_URL'].'filter/brand-is-'.$arBrand['CODE'].'/apply/';

    if(CMax::isSmartSeoInstalled() && class_exists('\Aspro\Smartseo\General\Smartseo')) {
        $arBrand['CATALOG_PAGE_URL'] = Aspro\Smartseo\General\Smartseo::replaceRealUrlByNew($arBrand['CATALOG_PAGE_URL']);
    }

    if($arBrand) {
        if($arParams['SHOW_BRAND_PICTURE'] == 'Y' && ($arBrand['PREVIEW_PICTURE'] || $arBrand['DETAIL_PICTURE'])) {
            $picture = ($arBrand['PREVIEW_PICTURE'] ? $arBrand['PREVIEW_PICTURE'] : $arBrand['DETAIL_PICTURE']);
            $arBrand['IMAGE'] = CFile::ResizeImageGet($picture, ['width' => 200, 'height' => 40], BX_RESIZE_IMAGE_PROPORTIONAL, true);
            $arBrand['IMAGE']['ALT'] = $arBrand['IMAGE']['TITLE'] = $arBrand['NAME'];
            if($arBrand['DETAIL_PICTURE']) {
                $arBrand['IMAGE']['INFO'] = CFile::GetFileArray($arBrand['DETAIL_PICTURE']);

                $ipropValues = new Iblock\InheritedProperty\ElementValues($arBrand['IBLOCK_ID'], $arBrand['ID']);
                $arBrand['IMAGE']['IPROPERTY_VALUES'] = $ipropValues->getValues();
                if($arBrand['IMAGE']['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']) {
                    $arBrand['IMAGE']['TITLE'] = $arBrand['IMAGE']['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'];
                }
                if($arBrand['IMAGE']['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']) {
                    $arBrand['IMAGE']['ALT'] = $arBrand['IMAGE']['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'];
                }

                if($arBrand['IMAGE']['INFO']['DESCRIPTION']) {
                    $arBrand['IMAGE']['ALT'] = $arBrand['IMAGE']['TITLE'] = $arBrand['IMAGE']['INFO']['DESCRIPTION'];
                }
            }
        }
    }
}

$arResult['BRAND_ITEM'] = $arBrand;

/* get section table sizes */

if($arResult['SECTION']) {
    $arSectionSelect = [
        'UF_TABLE_SIZES',
        'UF_VIDEO',
        'UF_VIDEO_IFRAME',
    ];

    $arInherite = TSolution\Functions::getSectionInheritedUF([
        'sectionId' => $arResult['IBLOCK_SECTION_ID'],
        'iblockId' => $arParams['IBLOCK_ID'],
        'select' => $arSectionSelect,
        'filter' => [
            'GLOBAL_ACTIVE' => 'Y',
        ],
        'enums' => [
            'UF_TABLE_SIZES',
        ],
    ]);

    $arResult['SIZE_PATH'] = TSolution\Functions\Item::getTableSizesPath($arResult, $arInherite);
}

if(in_array('HELP_TEXT', $arParams['PROPERTY_CODE'])) {
    $arResult['HELP_TEXT_FILE'] = false;
    if($arResult['DISPLAY_PROPERTIES']['HELP_TEXT']['~VALUE']):
        $arResult['HELP_TEXT'] = $arResult['DISPLAY_PROPERTIES']['HELP_TEXT']['~VALUE'];
    elseif($arParams['HELP_TEXT']):
        $arResult['HELP_TEXT'] = $arParams['HELP_TEXT'];
    else:?>
        <?ob_start(); ?>
            <?$APPLICATION->IncludeComponent(
                'bitrix:main.include',
                '',
                [
                    'AREA_FILE_SHOW' => 'page',
                    'AREA_FILE_SUFFIX' => 'help_text',
                    'EDIT_TEMPLATE' => '',
                ]
            ); ?>
        <?$help_text = ob_get_contents();
        ob_end_clean();
        $bshowHelpTextFromFile = true;
        if(strlen(trim($help_text)) < 1) {
            $bshowHelpTextFromFile = false;
        } else {
            $bIsBitrixDiv = (strpos($help_text, 'bx_incl_area') !== false);
            $textWithoutTags = strip_tags($help_text);
            if($bIsBitrixDiv && (strlen(trim($textWithoutTags)) < 1)) {
                $bshowHelpTextFromFile = false;
            }
        }

        if($bshowHelpTextFromFile) {
            $arResult['HELP_TEXT'] = $help_text;
            $arResult['HELP_TEXT_FILE'] = true;
        }
        ?>
    <?endif; ?>
<?}

/* video block */
$arResult['VIDEO'] = Aspro\Max\Video\Inline::getFilesFromProperties($arResult['DISPLAY_PROPERTIES']);

if($arInherite['UF_VIDEO']) {
    $arResult['VIDEO']['VIDEO_FILE'] = array_merge((array) $arResult['VIDEO']['VIDEO_FILE'], array_map(fn ($arVideoFile) => TSolution::unserialize($arVideoFile), $arInherite['UF_VIDEO']));
}

if($arInherite['UF_VIDEO_IFRAME']) {
    $arResult['VIDEO']['VIDEO_YOUTUBE'] = array_merge((array) $arResult['VIDEO']['VIDEO_YOUTUBE'], $arInherite['UF_VIDEO_IFRAME']);
}

if(!empty($arResult['DISPLAY_PROPERTIES'])) {
    $arResult['LINK_STAFF'] = $arResult['DISPLAY_PROPERTIES']['LINK_STAFF']['VALUE'] ?? '';
    $arResult['LINK_VACANCY'] = $arResult['DISPLAY_PROPERTIES']['LINK_VACANCY']['VALUE'] ?? '';

    $arResult['DISPLAY_PROPERTIES'] = TSolution::PrepareItemProps($arResult['DISPLAY_PROPERTIES']);
    TSolution\LinkableProperty::resolve($arResult['DISPLAY_PROPERTIES'], $arResult['IBLOCK_ID'], $arResult['IBLOCK_SECTION_ID']);

    $arGroupsProp = [];
    if($arResult['DISPLAY_PROPERTIES']) {
        foreach($arResult['DISPLAY_PROPERTIES'] as $propCode => $arProp) {
            $arGroupsProp[$propCode] = $arProp;
        }
    }
    $arResult['GROUPS_PROPS'] = $arGroupsProp;
}

if($arParams['TYPE_SKU'] == 'TYPE_1' && $arResult['OFFERS']) {
    // for ajax offers

    $arResult['SKU_CONFIG'] = [
        'ALT_TITLE_GET' => $arParams['ALT_TITLE_GET'],
        'SHOW_ABSENT' => $arParams['SHOW_ABSENT'],
        'HIDE_NOT_AVAILABLE_OFFERS' => $arParams['HIDE_NOT_AVAILABLE_OFFERS'],
        'PRICE_CODE' => $arParams['PRICE_CODE'],
        'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
        'OFFER_SHOW_PREVIEW_PICTURE_PROPS' => $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
        'OFFERS_SORT_FIELD' => $arParams['OFFERS_SORT_FIELD'],
        'OFFERS_SORT_ORDER' => $arParams['OFFERS_SORT_ORDER'],
        'OFFERS_SORT_FIELD2' => $arParams['OFFERS_SORT_FIELD2'],
        'OFFERS_SORT_ORDER2' => $arParams['OFFERS_SORT_ORDER2'],
        'LIST_OFFERS_LIMIT' => $arParams['OFFERS_LIMIT'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'LIST_OFFERS_PROPERTY_CODE' => explode(',', CMax::GetFrontParametrValue('CATALOG_SKU_PROPERTY_CODE')),
        'LIST_OFFERS_PROPERTY_CODE_DETAIL' => $arParams['OFFERS_PROPERTY_CODE'],
        'SHOW_DISCOUNT_TIME' => $arParams['SHOW_DISCOUNT_TIME'],
        'SHOW_COUNTER_LIST' => $arParams['SHOW_COUNTER_LIST'],
        'PRICE_VAT_INCLUDE' => $arParams['PRICE_VAT_INCLUDE'],
        'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'] ? 'Y' : 'N',
        'SHOW_MEASURE' => $arParams['SHOW_MEASURE'],
        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
        'SHOW_DISCOUNT_PERCENT_NUMBER' => $arParams['SHOW_DISCOUNT_PERCENT_NUMBER'],
        'USE_REGION' => $arParams['USE_REGION'],
        'STORES' => $arParams['STORES'],
        'DEFAULT_COUNT' => $arParams['DEFAULT_COUNT'],
        'BASKET_URL' => $arParams['BASKET_URL'],
        'SHOW_GALLERY' => 'Y',
        'MAX_GALLERY_ITEMS' => '0',
        'OFFERS_CART_PROPERTIES' => $arParams['OFFERS_CART_PROPERTIES'],
        'PRODUCT_PROPERTIES' => $arParams['PRODUCT_PROPERTIES'],
        'PARTIAL_PRODUCT_PROPERTIES' => $arParams['PARTIAL_PRODUCT_PROPERTIES'],
        'ADD_PROPERTIES_TO_BASKET' => $arParams['ADD_PROPERTIES_TO_BASKET'],
        'SHOW_ONE_CLICK_BUY' => $arParams['SHOW_ONE_CLICK_BUY'],
        'SHOW_DISCOUNT_TIME_EACH_SKU' => $arParams['SHOW_DISCOUNT_TIME_EACH_SKU'],
        'SHOW_ARTICLE_SKU' => $arParams['SHOW_ARTICLE_SKU'],
        'SHOW_POPUP_PRICE' => CMax::GetFrontParametrValue('SHOW_POPUP_PRICE'),
        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
        'ADD_DETAIL_TO_SLIDER' => $arParams['ADD_DETAIL_TO_SLIDER'],
        'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
        'PRODUCT_QUANTITY_VARIABLE' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
        // "IBINHERIT_TEMPLATES" => $arSeoItem ? $arIBInheritTemplates : array(),
        'DISPLAY_COMPARE' => CMax::GetFrontParametrValue('CATALOG_COMPARE'),
        'DISPLAY_WISH_BUTTONS' => $arParams['DISPLAY_WISH_BUTTONS'],
        'IS_DETAIL' => 'Y',
        'SKU_DETAIL_ID' => $arParams['SKU_DETAIL_ID'],
        'OCB_CLASS' => '',
        'CART_CLASS' => 'btn-lg',
        'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
        'GALLERY_WIDTH' => $arSizePict[$arParams['PICTURE_RATIO']][0],
        'GALLERY_HEIGHT' => $arSizePict[$arParams['PICTURE_RATIO']][1],
        'USE_ADDITIONAL_GALLERY' => $arParams['USE_ADDITIONAL_GALLERY'],
        'ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE' => $arParams['ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE'],
        'ADDITIONAL_GALLERY_PROPERTY_CODE' => $arParams['ADDITIONAL_GALLERY_PROPERTY_CODE'],
        'USE_STORE_CLICK' => ($arParams['USE_STORE'] == 'Y' && $arResult['STORES_COUNT'] && $arResult['CATALOG_TYPE'] != CCatalogProduct::TYPE_SET ? 'Y' : 'N'),
        'SHOW_HINTS' => $arParams['SHOW_HINTS'],
        'CATALOG_DETAIL_SHOW_AMOUNT_STORES' => $arParams['CATALOG_DETAIL_SHOW_AMOUNT_STORES'],
        'IBLOCK_ID_PARENT' => $arParams['IBLOCK_ID'],
        'IBLOCK_ID' => $arResult['SKU_IBLOCK_ID'],
    ];
}
?>
