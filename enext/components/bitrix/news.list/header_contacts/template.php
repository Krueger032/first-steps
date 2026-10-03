<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

global $arSettings;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('HEADER_CONTACTS_ITEM_DELETE_CONFIRM'));

$signer = new Bitrix\Main\Security\Sign\Signer;
$signerParams = $signer->sign(base64_encode(serialize($arParams)), "news.list");?>

<script type="text/javascript">
	BX.message({		
		HEADER_CONTACTS_TITLE: '<?=GetMessageJS("HEADER_CONTACTS_TITLE")?>',
		HEADER_CONTACTS_ITEM_TODAY: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_TODAY")?>',
		HEADER_CONTACTS_ITEM_24_HOURS: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_24_HOURS")?>',
		HEADER_CONTACTS_ITEM_OFF: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_OFF")?>',
		HEADER_CONTACTS_ITEM_BREAK: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_BREAK")?>',
		HEADER_CONTACTS_SEE_REVIEWS: '<?=GetMessageJS("HEADER_CONTACTS_SEE_REVIEWS")?>',
		HEADER_CONTACTS_ADD_REVIEW: '<?=GetMessageJS("HEADER_CONTACTS_ADD_REVIEW")?>',
		HEADER_CONTACTS_CALLBACK: '<?=GetMessageJS("HEADER_CONTACTS_CALLBACK")?>',
		HEADER_CONTACTS_TEMPLATE_PATH: '<?=CUtil::JSEscape($templateFolder)?>'
	});
</script>

<?foreach($arResult["ITEMS"] as $arItem) {
	$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
	$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);
	
	$strMainID = $this->GetEditAreaId($arItem["ID"]);	
	$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>

	<div class="top-panel__col top-panel__contacts">
		<a class="top-panel__contacts-block" id="<?=$strMainID?>" href="javascript:void(0)">
			<span class="top-panel__contacts-icon"><i class="icon-phone-call"></i></span>
			<span class="top-panel__contacts-caption hidden-xs hidden-sm<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' ? ' hidden-md' : '')?>">
				<?if(!empty($arItem["PREVIEW_TEXT"])) {?>
					<span class="top-panel__contacts-title"><?=$arItem["PREVIEW_TEXT"]?></span>
				<?}
				if(!empty($arItem["DETAIL_TEXT"])) {?>
					<span class="top-panel__contacts-descr"><?=$arItem["DETAIL_TEXT"]?></span>
				<?}?>
			</span>
			<span class="top-panel__contacts-icon hidden-xs hidden-sm<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' ? ' hidden-md' : '')?>"><i class="icon-arrow-down"></i></span>
		</a>
		<?$arJSParams = array(				
			"ITEM" => array(
				"NAME" => $arItem["NAME"],
				"PREVIEW_PICTURE" => is_array($arItem["PREVIEW_PICTURE"]) ? $arItem["PREVIEW_PICTURE"]["SRC"] : "",
				"ADDRESS" => $arItem["ADDRESS"],
				"MAP" => $arItem["MAP"],
				"TIMEZONE" => $arItem["TIMEZONE"],
				"WORKING_HOURS" => $arItem["WORKING_HOURS"],			
				"PHONE" => $arItem["PHONE"],
				"WHATSAPP" => $arItem["WHATSAPP"],
				"VIBER" => $arItem["VIBER"],
				"TELEGRAM" => $arItem["TELEGRAM"],
				"INSTAGRAM" => $arItem["INSTAGRAM"],
				"EMAIL" => $arItem["EMAIL"],
				"SKYPE" => $arItem["SKYPE"]
			),
			"VISUAL" => array(
				"ID" => $strMainID
			),
			"PARAMETERS" => $signerParams,
			"BTN_CALLBACK" => $arParams["SHOW_OBJECTS"] != "N" ? true : false
		);
		if($arParams["USE_REVIEW"] != "N") {
			$arJSParams["BTN_ADD_REVIEW"] = true;
			$arJSParams["REVIEWS_PAGE_LINK"] = $arResult["REVIEWS_PAGE_LINK"];
			if(isset($arResult["REVIEWS_COUNT"]) && $arResult["REVIEWS_COUNT"] > 0) {
				$arJSParams["RATING_VALUE"] = $arResult["RATING_VALUE"];
				$arJSParams["REVIEWS_COUNT"] = $arResult["REVIEWS_COUNT"];
				$arJSParams["REVIEWS_DECLENSION"] = $arResult["REVIEWS_DECLENSION"];
			}
		}?>
		<script type="text/javascript">
			var <?=$strObName;?> = new JCNewsListHeaderContacts(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		</script>
	</div>
<?}?>