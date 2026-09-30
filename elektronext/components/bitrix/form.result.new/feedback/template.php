<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if(empty($arParams["ENEXT_IBLOCK"]) || empty($arParams["ENEXT_CONTAINER"]))
	return;

$containerName = $arParams["ENEXT_CONTAINER"];
$arResult = array("IBLOCK" => $arParams["ENEXT_IBLOCK"]);
$arParams = $arParams["ENEXT_PARAMS"];
$component = false;
?>
<div class="hidden-print feedback-wrapper">
	<div class="container">
		<div class="row feedback" id="<?=$containerName?>">
			<div class="col-xs-12">
				<div class="h1"><?=$arResult["IBLOCK"]["NAME"]?></div>
			</div>
			<form action="javascript:void(0)">
				<?//PRO_WS//?>
				<input type="hidden" name="IBLOCK_STRING" value="<?=htmlspecialcharsbx($arResult['IBLOCK']['SIGNED_STRING'])?>" />
				<?if(false){?>
					<input type="hidden" name="IBLOCK_STRING" value="<?=$arResult['IBLOCK']['STRING']?>" />
				<?}?>
				<?//PRO_WS//?>
				<div class="col-xs-12 col-md-3">
					<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
						if($arProp["USER_TYPE"] != "HTML") {
							if($arProp["CODE"] != "SOURCE_URL") {?>
								<div class="form-group<?=(!empty($arProp['HINT']) ? ' has-feedback' : '');?>">
									<input type="text" name="<?=$arProp['CODE']?>" class="form-control" placeholder="<?=$arProp['NAME']?>" inputmode="<?=($arProp['CODE'] == 'PHONE' ? 'numeric' : 'text')?>" />
									<?if(!empty($arProp["HINT"])) {?>
										<i class="form-control-feedback fv-icon-no-has fa <?=$arProp['HINT']?>"></i>
									<?}?>
								</div>
							<?} else {?>
								<input type="hidden" name="<?=$arProp['CODE']?>" value="" />
							<?}
						}
					}
					unset($arProp);?>
				</div>
				<div class="col-xs-12 col-md-6">
					<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
						if($arProp["USER_TYPE"] == "HTML") {?>
							<div class="form-group<?=(!empty($arProp['HINT']) ? ' has-feedback' : '');?>">
								<textarea name="<?=$arProp['CODE']?>" class="form-control" rows="3" placeholder="<?=$arProp['NAME']?>" style="height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; min-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; max-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px;" inputmode="text"></textarea>
								<?if(!empty($arProp["HINT"])) {?>
									<i class="form-control-feedback fv-icon-no-has fa <?=$arProp['HINT']?>"></i>
								<?}?>
							</div>
						<?}
					}
					unset($arProp);?>
				</div>
				<div class="col-xs-12 col-md-3">
					<?if($arParams["USE_CAPTCHA"]) {?>
						<div class="form-group captcha">
							<div class="pic" style="display:none;">								
								<img src="" width="100" height="36" alt="CAPTCHA" />
							</div>							
							<input type="text" maxlength="5" name="CAPTCHA_WORD" class="form-control" placeholder="<?=Loc::getMessage('FORMS_FEEDBACK_CAPTCHA_WORD')?>" />
							<input type="hidden" name="CAPTCHA_SID" value="" />
						</div>
					<?}?>
					<div class="form-group<?=(!$arParams['USE_CAPTCHA'] ? ' no-captcha' : '');?>">
						<button type="submit" class="btn btn-primary"><?=Loc::getMessage("FORMS_FEEDBACK_SUBMIT")?></button>
					</div>
				</div>
				<?if($arParams["USER_CONSENT"]) {?>
					<input type="hidden" name="USER_CONSENT_ID" value="<?=$arParams['USER_CONSENT_ID']?>" />
					<input type="hidden" name="USER_CONSENT_URL" value="" />
					<div class="col-xs-12">
						<div class="form-group form-group-checkbox">
							<div class="checkbox">
								<?$fields = array();
								foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {
									if($arProp["USER_TYPE"] != "HTML" && $arProp["CODE"] != "SOURCE_URL")
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
											"button_caption" => Loc::getMessage("FORMS_FEEDBACK_SUBMIT"),
											"fields" => $fields
										)
									),
									$component
								);?>
								<?unset($fields);?>
							</div>
						</div>
					</div>
				<?}?>
			</form>
			<div class="col-xs-12">
				<div class="alert" style="display: none;"></div>
			</div>
		</div>
	</div>
</div>

