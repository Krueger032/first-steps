<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if(empty($arParams["ENEXT_IBLOCK"]) || empty($arParams["ENEXT_CONTAINER"]))
	return;

$containerName = $arParams["ENEXT_CONTAINER"];
$arResult = array("IBLOCK" => $arParams["ENEXT_IBLOCK"]);
$arParams = $arParams["ENEXT_PARAMS"];
$component = false;
?>
<div class="slide-panel__form" id="<?=$containerName?>">
	<div class="slide-panel__form-title"><?=$arResult["IBLOCK"]["NAME"]?></div>
	<?if(!empty($arResult["IBLOCK"]["DESCRIPTION"])) {?>
		<div class="slide-panel__form-caption"><?=$arResult["IBLOCK"]["DESCRIPTION"]?></div>
	<?}?>
	<form action="javascript:void(0)">		
		<?//PRO_WS//?>
		<input type="hidden" name="IBLOCK_STRING" value="<?=htmlspecialcharsbx($arResult['IBLOCK']['SIGNED_STRING'])?>" />
		<?if(false){?>
			<input type="hidden" name="IBLOCK_STRING" value="<?=$arResult['IBLOCK']['STRING']?>" />
		<?}?>
		<?//PRO_WS//?>
		<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
			if($arProp["CODE"] == "OBJECT_ID" || $arProp["CODE"] == "PRODUCT_ID" || $arProp["CODE"] == "OFFER_ID") {?>
				<input type="hidden" name="<?=$arProp['CODE']?>" value="" />
				<?if($arProp["CODE"] == "PRODUCT_ID") {?>
					<input type="hidden" name="PRODUCT_LINK" value="" />
				<?}
			} else {?>
				<div class="form-group<?=(!empty($arProp['HINT']) ? ' has-feedback' : '');?>">
					<?if($arProp["USER_TYPE"] != "HTML") {?>
						<input type="text" name="<?=$arProp['CODE']?>" class="form-control" placeholder="<?=(!empty($arProp['DEFAULT_VALUE']) ? $arProp['DEFAULT_VALUE'] : $arProp['NAME']);?>" inputmode="<?=($arProp['CODE'] == 'PHONE' ? 'numeric' : 'text')?>" />
					<?} else {?>									
						<textarea name="<?=$arProp['CODE']?>" class="form-control" rows="3" placeholder="<?=(!empty($arProp['DEFAULT_VALUE']['TEXT']) ? $arProp['DEFAULT_VALUE']['TEXT'] : $arProp['NAME']);?>" style="height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; min-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; max-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px;" inputmode="text"></textarea>
					<?}
					if(!empty($arProp["HINT"])) {?>
						<i class="form-control-feedback fv-icon-no-has fa <?=$arProp['HINT']?>"></i>
					<?}?>
				</div>
			<?}
		}
		unset($arProp);
		if($arParams["USER_CONSENT"]) {?>
			<input type="hidden" name="USER_CONSENT_ID" value="<?=$arParams['USER_CONSENT_ID']?>" />
			<input type="hidden" name="USER_CONSENT_URL" value="" />
			<div class="form-group form-group-checkbox">
				<div class="checkbox">
					<?$fields = array();
					foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
						if($arProp["CODE"] != "OBJECT_ID" && $arProp["CODE"] != "PRODUCT_ID" && $arProp["CODE"] != "OFFER_ID" && $arProp["USER_TYPE"] != "HTML")
							$fields[] = $arProp["NAME"];
					}
					unset($arProp);?>
					<?$APPLICATION->IncludeComponent("bitrix:main.userconsent.request", "",
						array(
							"ID" => $arParams["USER_CONSENT_ID"],
							"INPUT_NAME" => "USER_CONSENT",
							"IS_CHECKED" => $arParams["USER_CONSENT_IS_CHECKED"],
							"AUTO_SAVE" => "N",
							"IS_LOADED" => $arParams["USER_CONSENT_IS_LOADED"],
							"REPLACE" => array(
								"button_caption" => Loc::getMessage("FORMS_SLIDE_PANEL_SUBMIT"),
								"fields" => $fields
							)
						),
						$component
					);?>
					<?unset($fields);?>
				</div>
			</div>
		<?}
		if($arParams["USE_CAPTCHA"]) {?>
			<div class="form-group captcha">
				<div class="pic" style="display:none;">								
					<img src="" width="100" height="36" alt="CAPTCHA" />
				</div>							
				<input type="text" maxlength="5" name="CAPTCHA_WORD" class="form-control" placeholder="<?=Loc::getMessage('FORMS_SLIDE_PANEL_CAPTCHA_WORD')?>" />
				<input type="hidden" name="CAPTCHA_SID" value="" />
			</div>
		<?}?>		
		<div class="form-group">
			<button type="submit" class="btn btn-buy"><span><?=Loc::getMessage("FORMS_SLIDE_PANEL_SUBMIT")?></span></button>
		</div>		
	</form>
	<div class="alert" style="display: none;"></div>
</div>

