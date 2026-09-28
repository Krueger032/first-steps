<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?if ($arParams['SHOW_PROJECTS_MAP'] == 'Y'):?>
    <?
    $mapLAT = $mapLON = $iCountShops =0;
    $arItems = $arSections = $arPlacemarks = [];
    $arItems = CMaxCache::CIblockElement_GetList(
        array(
            "CACHE" => array(
                "TAG" => CMaxCache::GetIBlockCacheTag($arParams["IBLOCK_ID"])
            )
        ), 
        $arItemFilter,
        false, 
        false, 
        array('ID', 'NAME', 'PREVIEW_PICTURE', 'PROPERTY_MAP', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID', 'PROPERTY_INFO')
    );

    foreach ($arItems as $arItem) {
        if (!in_array($arItem['IBLOCK_SECTION_ID'] ,$arSections)) {
            if (is_array($arItem['IBLOCK_SECTION_ID'])) {
                foreach ($arItem['IBLOCK_SECTION_ID'] as $tmpSection) {
                    if (!in_array($tmpSection,$arSections)) {
                        $arSections[] = $tmpSection;
                    }
                }
            } else {
                $arSections[] = $arItem['IBLOCK_SECTION_ID'];
            }
        }
    }

    if ($arSections) {
        $arSections = CMaxCache::CIBlockSection_GetList(
            array(
                "CACHE" => array(
                    "TAG" => CMaxCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), 
                    "MULTI" => "N", 
                    'GROUP' => 'ID'
                )
            ), 
            array(
                'IBLOCK_ID' => $arParams['IBLOCK_ID'], 
                'ID' => $arSections
            ), 
            false,
            array("ID", 'NAME')
        );
    }
    if ($arItems) {  
        foreach ($arItems as $arItem) {
            if ($arItem['PROPERTY_MAP_VALUE']) {
                $arCoords = explode(',', $arItem['PROPERTY_MAP_VALUE']);
                $mapLAT += $arCoords[0];
                $mapLON += $arCoords[1];		
 	         
                ob_start();
					\Aspro\Functions\CAsproMax::showBlockHtml(
						[
							'FILE' => 'projects/balloon_map.php',
							'ITEM' => \Aspro\Functions\CAsproMax::prepareValuesForMapInProjects([
                                'ITEM' => $arItem,
                                'SECTIONS' => $arSections,
                            ]),
						]
					);

					$html = ob_get_clean();	

                $arPlacemarks[] = array(
                    "ID" => $arItem["ID"],
                    "LAT" => $arCoords[0],
                    "LON" => $arCoords[1],
                    "TEXT" => $arItem['NAME'],
                    "TEXT" => $html,
                    "HTML" => $html
                );
                ++$iCountShops;
            }
        }
        if ($iCountShops) {
            $mapLAT = floatval($mapLAT / $iCountShops);
            $mapLON = floatval($mapLON / $iCountShops);?>
            <?ob_start()?>
                <div class="contacts-page-map projects">
                        <div class="bordered rounded-4 map-view">
                 
                                <?$APPLICATION->IncludeComponent(
                                    "bitrix:map.yandex.view",
                                    "map",
                                    array(
                                        "INIT_MAP_TYPE" => "MAP",
                                        "MAP_DATA" => serialize(array("yandex_lat" => $mapLAT, "yandex_lon" => $mapLON, "yandex_scale" => 19, "PLACEMARKS" => $arPlacemarks)),
                                        "MAP_WIDTH" => "100%",
                                        "MAP_HEIGHT" => "550",
                                        "CONTROLS" => array(
                                            0 => "ZOOM",
                                            1 => "TYPECONTROL",
                                            2 => "SCALELINE",
                                        ),
                                        "OPTIONS" => array(
                                            0 => "ENABLE_DBLCLICK_ZOOM",
                                            1 => "ENABLE_DRAGGING",
                                        ),
                                        "MAP_ID" => "MAP_project",
                                        "COMPONENT_TEMPLATE" => "map"
                                    ),
                                    false, ['HIDE_ICONS' => 'Y']
                                );?>
                        </div>
                </div>
            <?$html=ob_get_clean();?>
            <?$APPLICATION->AddViewContent('top_section_filter_content', $html);?>
        <?}?>
    <?}?>
<?endif;?>