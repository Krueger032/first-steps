<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

$catalogIBlockID = TSolution::getFrontParametrValue('CATALOG_IBLOCK_ID');
$arSectionsFilter = ['IBLOCK_ID' => $catalogIBlockID, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', '<DEPTH_LEVEL' => $arParams['MAX_LEVEL']];
$arSections = TSolution\Cache::CIBlockSection_GetList(['SORT' => 'ASC', 'ID' => 'ASC', 'CACHE' => ['TAG' => TSolution\Cache::GetIBlockCacheTag($catalogIBlockID), 'GROUP' => ['ID']]], CMax::makeSectionFilterInRegion($arSectionsFilter), false, ['ID', 'IBLOCK_ID', 'NAME', 'PICTURE', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL', 'SECTION_PAGE_URL', 'IBLOCK_SECTION_ID', 'UF_CATALOG_ICON', 'UF_MENU_BANNER', 'UF_MENU_BRANDS']);
if ($arSections) {
    $arResult = [];
    $cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
    $cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);

    foreach ($arSections as $ID => $arSection) {
        $arSections[$ID]['SELECTED'] = CMenu::IsItemSelected($arSection['SECTION_PAGE_URL'], $cur_page, $cur_page_no_index);
        if ($arSection['UF_CATALOG_ICON']) {
            $img = CFile::ResizeImageGet($arSection['UF_CATALOG_ICON'], ['width' => 36, 'height' => 36], BX_RESIZE_IMAGE_PROPORTIONAL, true);
            $arSections[$ID]['IMAGES'] = $img;
        } elseif ($arSection['PICTURE']) {
            $img = CFile::ResizeImageGet($arSection['PICTURE'], ['width' => 50, 'height' => 50], BX_RESIZE_IMAGE_PROPORTIONAL, true);
            $arSections[$ID]['IMAGES'] = $img;
        }
        if ($arSection['IBLOCK_SECTION_ID']) {
            if (!isset($arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'])) {
                $arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'] = [];
            }
            $arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'][] = &$arSections[$arSection['ID']];
        }

        if ($arSection['DEPTH_LEVEL'] == 1) {
            $arResult[] = &$arSections[$arSection['ID']];
        }
    }

    if ((int) TSolution::getFrontParametrValue('MEGA_MENU_STRUCTURE') === 2) {
        if ($catalogIBlockID) {
            if ($arCatalogIblock = TSolution\Cache::$arIBlocksInfo[$catalogIBlockID]) {
                if ($catalogPageUrl = str_replace('#'.'SITE_DIR'.'#', SITE_DIR, $arCatalogIblock['LIST_PAGE_URL'])) {
                    $menuIblockId = TSolution\Cache::$arIBlocks[SITE_ID][TSolution::partnerName.'_'.TSolution::solutionName.'_catalog'][TSolution::partnerName.'_'.TSolution::solutionName.'_megamenu'][0];
                    if ($menuIblockId) {
                        $menuRootCatalogSectionId = TSolution\Cache::CIblockSection_GetList(['SORT' => 'ASC', 'CACHE' => ['TAG' => TSolution\Cache::GetIBlockCacheTag($menuIblockId), 'RESULT' => ['ID'], 'MULTI' => 'N']], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $menuIblockId, 'DEPTH_LEVEL' => 1, 'UF_MENU_LINK' => $catalogPageUrl], false, ['ID'], ['nTopCount' => 1]);
                        if ($menuRootCatalogSectionId) {
                            $arResult = [
                                [
                                    'LINK' => $catalogPageUrl,
                                    'PARAMS' => [
                                        'FROM_IBLOCK' => 1,
                                        'DEPTH_LEVEL' => 1,
                                        'MEGA_MENU_CHILDS' => 1,
                                    ],
                                    'CHILD' => $arResult,
                                    'IS_PARENT' => (bool) $arResult,
                                ],
                            ];
                        }
                    }
                }
            }
        }
        CMax::replaceMenuChilds($arResult, $arParams);
    }

    if (
        TSolution::getFrontParametrValue('SHOW_RIGHT_SIDE') === 'Y'
        && TSolution::getFrontParametrValue('RIGHT_CONTENT') === 'BRANDS'
    ) {
        $arBrandsID = [];
        foreach ($arResult as $key => $arItem) {
            if (isset($arItem['UF_MENU_BRANDS']) && $arItem['UF_MENU_BRANDS']) {
                $arBrandsID = array_merge($arBrandsID, $arItem['UF_MENU_BRANDS']);
            }
        }

        if ($arBrandsID) {
            $arBrandsID = array_unique($arBrandsID);

            $brandIblockId = TSolution\Cache::$arIBlocks[SITE_ID][TSolution::partnerName.'_'.TSolution::solutionName.'_content'][TSolution::partnerName.'_'.TSolution::solutionName.'_brands'][0];
            $arBrandFilter = ['ACTIVE' => 'Y', 'IBLOCK_ID' => $brandIblockId, 'ID' => $arBrandsID];
            $arBrandSelect = ['ID', 'PREVIEW_PICTURE', 'NAME', 'DETAIL_PAGE_URL', 'IBLOCK_ID'];
            $arBrands = TSolution\Cache::CIblockElement_GetList(['SORT' => 'ASC', 'CACHE' => ['GROUP' => 'ID', 'TAG' => TSolution\Cache::GetIBlockCacheTag($brandIblockId)]], $arBrandFilter, false, false, $arBrandSelect);

            if ($arBrands) {
                foreach ($arResult as $key => $arItem) {
                    if (isset($arItem['UF_MENU_BRANDS']) && $arItem['UF_MENU_BRANDS']) {
                        foreach ($arItem['UF_MENU_BRANDS'] as $brandID) {
                            if ($arBrands[$brandID]) {
                                $arResult[$key]['UF_MENU_BRANDS'][$brandID] = $arBrands[$brandID];
                            } else {
                                unset($arResult[$key]['UF_MENU_BRANDS'][$brandID]);
                            }
                        }
                    }
                }
            }
        }
    }
}
