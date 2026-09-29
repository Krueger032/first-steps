<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

global $arSettings;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('HEADER_CONTACTS_ITEM_DELETE_CONFIRM'));
$i = 0;
foreach($arResult["ITEMS"] as $arItem) {
	$i++;
	$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
	$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);
	
	$strMainID = $this->GetEditAreaId($arItem["ID"]);	
	$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>

	<div class="top-panel__col top-panel__contacts">
<!--			<span class="top-panel__contacts-icon"><i class="icon-phone-call"></i></span>-->
			<span class="top-panel__contacts-caption hidden-xs hidden-sm<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' ? ' hidden-md' : '')?>">
				<?if(!empty($arItem["DETAIL_TEXT"])) {?>
					<span class="top-panel__contacts-descr"><?=$arItem["DETAIL_TEXT"]?></span>
				<?}?>
			</span>
<!--			<span class="top-panel__contacts-icon hidden-xs hidden-sm--><?//=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' ? ' hidden-md' : '')?><!--"><i class="icon-arrow-down"></i></span>-->
	</div>

	<div class="top-panel__col top-panel__contacts" onclick="ym(78427446, 'reachGoal', 'zvonok'); return true;">
		<a class="top-panel__contacts-block <?=$i?>" id="<?=$strMainID?>" href="javascript:void(0)">
			<span class="top-panel__contacts-icon"><i class="icon-phone-call"></i></span>
			<span class="top-panel__contacts-caption hidden-xs hidden-sm<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' ? ' hidden-md' : '')?>">
				<?if(!empty($arItem["PREVIEW_TEXT"])) {?>
					<span class="top-panel__contacts-title"><?=$arItem["PREVIEW_TEXT"]?></span>
					<span class="top-panel__contacts-title">+7 (985)-288-34-57</span>
				<?}?>
			</span>
			<span class="top-panel__contacts-icon hidden-xs hidden-sm<?=($arSettings['CATALOG_MENU']['VALUE'] == 'OPTION-3' ? ' hidden-md' : '')?>"><i class="icon-arrow-down"></i></span>
		</a>
		<?$arJSParams = array(
			"ITEM" => array(
				"ADDRESS" => $arItem["ADDRESS"],
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
			)
		);?>
		<script type="text/javascript">
			BX.message({
				HEADER_CONTACTS_TITLE: '<?=GetMessageJS("HEADER_CONTACTS_TITLE");?>',
				HEADER_CONTACTS_ITEM_TODAY: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_TODAY");?>',
				HEADER_CONTACTS_ITEM_24_HOURS: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_24_HOURS");?>',
				HEADER_CONTACTS_ITEM_OFF: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_OFF");?>',
				HEADER_CONTACTS_ITEM_BREAK: '<?=GetMessageJS("HEADER_CONTACTS_ITEM_BREAK");?>',
				HEADER_CONTACTS_TEMPLATE_PATH: '<?=CUtil::JSEscape($templateFolder)?>'
			});
			var <?=$strObName;?> = new JCNewsListHeaderContacts(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		</script>
	</div>
<?}?>