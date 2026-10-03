<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$item = &$arResult['ITEM'];

$haveOffers = !empty($item['OFFERS']);

$object = !empty($item['PROPERTIES']['OBJECT']['FULL_VALUE']) ? $item['PROPERTIES']['OBJECT']['FULL_VALUE'] : false;
$objectContacts = $object['PHONE_SMS'] || $object['EMAIL_EMAIL'] ? true : false;

$partnersUrl = !empty($item['PROPERTIES']['PARTNERS_URL']['VALUE']) ? true : false;

//TARGET//
if(($object && !$objectContacts) || $partnersUrl)
	$item['TARGET'] = '_blank';

//OFFERS_VIEW//
if($item['OFFERS_OBJECTS'])
	$arParams['OFFERS_VIEW'] = 'OBJECTS';

if($haveOffers && (!$object || ($object && $objectContacts)) && !$partnersUrl && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y') {
	//PRODUCT_DISPLAY_MODE//
	$numOffersPartnersUrl = 0;
	foreach($item['OFFERS'] as $offer) {
		if(!empty($offer['PROPERTIES']['PARTNERS_URL']['VALUE']))
			$numOffersPartnersUrl++;
	}
	unset($offer);

	if($numOffersPartnersUrl == count($item['OFFERS'])) {
		$arParams['PRODUCT_DISPLAY_MODE'] = 'N';
		$item['TARGET'] = '_blank';
	}

	//JS_OFFERS//
	if($arParams['PRODUCT_DISPLAY_MODE'] == 'Y') {
		foreach($item['JS_OFFERS'] as $ind => &$jsOffer) {
			if(!empty($item['OFFERS'][$ind]['PROPERTIES']['PARTNERS_URL']['VALUE']))
				$jsOffer['PARTNERS_URL'] = true;
			elseif(!empty($item['PROPERTIES']['PARTNERS_URL']['VALUE']))
				$jsOffer['PARTNERS_URL'] = true;

			$jsOffer['ARTICLE'] = !empty($item['OFFERS'][$ind]['PROPERTIES']['ARTNUMBER']['VALUE']) ? $item['OFFERS'][$ind]['PROPERTIES']['ARTNUMBER']['VALUE'] : false;
		}
		unset($ind, $jsOffer);
	}
}

//ITEM_START_PRICE//
if($haveOffers && (($object && !$objectContacts) || $partnersUrl || ($arParams['OFFERS_VIEW'] != 'PROPS' && $arParams['OFFERS_VIEW'] != 'DROPDOWN_LIST') || $arParams['PRODUCT_DISPLAY_MODE'] == 'N')) {
	$item['OFFERS_SELECTED'] = null;

	$minPrice = null;
	$minPriceIndex = null;
	foreach($item['OFFERS'] as $key => $arOffer) {
		if(!$arOffer['CAN_BUY'] || $arOffer['ITEM_PRICE_SELECTED'] === null)
			continue;

		$priceScale = $arOffer['ITEM_PRICES'][$arOffer['ITEM_PRICE_SELECTED']]['PRICE'];		
		if($priceScale <= 0)
			continue;
		
		if($minPrice === null || $minPrice > $priceScale) {
			$minPrice = $priceScale;
			$minPriceIndex = $key;
		}
		unset($priceScale);
	}
	unset($arOffer, $key);
	
	if($minPriceIndex !== null) {
		$item['OFFERS_SELECTED'] = $minPriceIndex;
		
		$minOffer = $item['OFFERS'][$minPriceIndex];
		if(!empty($minOffer['PREVIEW_PICTURE']))
			$item['PREVIEW_PICTURE'] = $minOffer['PREVIEW_PICTURE'];
	}
	unset($minOffer, $minPriceIndex, $minPrice);
}

unset($item, $haveOffers, $object, $objectContacts, $partnersUrl);