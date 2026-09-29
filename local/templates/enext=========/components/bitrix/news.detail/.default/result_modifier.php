<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

ob_start();

//PROPERTIES//
foreach($arResult["DISPLAY_PROPERTIES"] as &$arProp) {
	//GALLERY//
	if($arProp["CODE"] == "GALLERY" && !empty($arProp["FILE_VALUE"])) {
		$ibFields = CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "FIELDS");
		$ibFieldsDetailPic = $ibFields["DETAIL_PICTURE"]["DEFAULT_VALUE"];
		if(isset($arProp["FILE_VALUE"]["ID"])) {
			$arTmp = $arProp["FILE_VALUE"];
			unset($arProp["FILE_VALUE"]);
			$arProp["FILE_VALUE"][0] = $arTmp;
			unset($arTmp);
		} 
		foreach($arProp["FILE_VALUE"] as $val) {		
			if($ibFieldsDetailPic["SCALE"] == "Y") {
				$arFileTmp = CFile::ResizeImageGet(
					$val,
					array(
						"width" => !empty($ibFieldsDetailPic["WIDTH"]) ? $ibFieldsDetailPic["WIDTH"] : 10000,
						"height" => !empty($ibFieldsDetailPic["HEIGHT"]) ? $ibFieldsDetailPic["HEIGHT"] : 10000
					),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arProp["FULL_VALUE"][] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
					"DESCRIPTION" => $val["DESCRIPTION"]
				);
				unset($arFileTmp);
			} else {
				$arProp["FULL_VALUE"][] = $val;
			}
		}
		unset($val, $ibFieldsDetailPic, $ibFields);
	//FILES_DOCS//
	} elseif($arProp["CODE"] == "FILES_DOCS" && !empty($arProp["FILE_VALUE"])) {
		if(isset($arProp["FILE_VALUE"]["ID"])) {
			$arTmp = $arProp["FILE_VALUE"];
			unset($arProp["FILE_VALUE"]);
			$arProp["FILE_VALUE"][0] = $arTmp;
			unset($arTmp);
		} 
		foreach($arProp["FILE_VALUE"] as $val) {
			$fileTypePos = strrpos($val["FILE_NAME"], ".");		
			$fileType = substr($val["FILE_NAME"], $fileTypePos + 1);
			$fileTypeFull = substr($val["FILE_NAME"], $fileTypePos);
			
			$fileName = str_replace($fileTypeFull, "", $val["ORIGINAL_NAME"]);		
			
			$fileSize = $val["FILE_SIZE"];
			$metrics = array(
				0 => Loc::getMessage("NEWS_ITEM_DETAIL_SIZE_B"),
				1 => Loc::getMessage("NEWS_ITEM_DETAIL_SIZE_KB"),
				2 => Loc::getMessage("NEWS_ITEM_DETAIL_SIZE_MB"),
				3 => Loc::getMessage("NEWS_ITEM_DETAIL_SIZE_GB")
			);
			$metric = 0;
			while(floor($fileSize / 1024) > 0) {
				$metric ++;
				$fileSize /= 1024;
			}
			$fileSizeFormat = round($fileSize, 1)." ".$metrics[$metric];

			$arProp["FULL_VALUE"][] = array(
				"NAME" => $fileName,
				"DESCRIPTION" => $val["DESCRIPTION"],
				"TYPE" => $fileType,
				"SIZE" => $fileSizeFormat,
				"SRC" => $val["SRC"]			
			);
		}
		unset($val);
	}
}
unset($arProp);