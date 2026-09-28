<?php
global $arRegion;
if( isset($arParams["ASPRO_COUNT_ELEMENTS"]) && $arParams["ASPRO_COUNT_ELEMENTS"] === "Y" ){
	foreach($arResult["SECTIONS"] as &$arSection){
		$arOrder = [
			'CACHE' => [
				'MULTI' => 'N', 
				'TAG' => CMaxCache::GetIBlockCacheTag($arParams["IBLOCK_ID"])
			]
		];

		$elementCountFilter = [
			'IBLOCK_ID' => $arSection['IBLOCK_ID'],
			'SECTION_ID' => $arSection['ID'],
			'CHECK_PERMISSIONS' => 'Y',
			'MIN_PERMISSION' => 'R',
			'INCLUDE_SUBSECTIONS' => 'Y',
			'ACTIVE' => 'Y',
		];

		if( $arParams['HIDE_NOT_AVAILABLE'] === 'Y' )
			$elementCountFilter['AVAILABLE'] = 'Y';

		$arFilter = $elementCountFilter;

		CMax::makeElementFilterInRegion($arFilter);

		if( is_array($GLOBALS['arRegionLink']) && CMax::GetFrontParametrValue('REGIONALITY_FILTER_ITEM') == 'Y' && CMax::GetFrontParametrValue('REGIONALITY_FILTER_CATALOG') == 'Y' ){
			$arFilter = array_merge($GLOBALS['arRegionLink'], $arFilter);
		}

		if( $arRegion ){			
			if( $arRegion['LIST_STORES'] && $arParams['HIDE_NOT_AVAILABLE'] === 'Y' ){
				$arStoresFilter = TSolution\Filter::getAvailableByStores($arParams['STORES']);
				if($arStoresFilter){
					$arFilter[] = $arStoresFilter;
				}
			}
		}

		$countElements = CMaxCache::CIBlockElement_GetList($arOrder, $arFilter, []);

		$arSection['ELEMENT_CNT'] = $countElements;
	}
}
?>