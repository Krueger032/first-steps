<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

global $USER;
if(!$USER->IsAuthorized())
	return;

if(strlen($arResult["ERROR_MESSAGE"]) > 0)
	ShowError($arResult["ERROR_MESSAGE"]);

if(count($arResult["PROFILES"])) {?>
	<div class="sale-personal-profile-list">
		<div class="sppl-title-container">
			<div class="row sppl-title">
				<div class="hidden-xs hidden-sm col-xs-1 sppl-title__val sppl-s-center-t"><?=Loc::getMessage("P_NUMBER")?></div><!--
				--><div class="col-xs-4 col-md-3 sppl-title__val"><?=Loc::getMessage("P_NAME")?></div><!--
				--><div class="col-xs-4 col-md-3 sppl-title__val"><?=Loc::getMessage("P_PERSON_TYPE_ID")?></div><!--
				--><div class="hidden-xs hidden-sm col-xs-3 sppl-title__val sppl-s-right-t"><?=Loc::getMessage("P_DATE_UPDATE")?></div><!--
				--><div class="col-xs-4 col-md-2 sppl-title__val sppl-s-center-t"></div>
			</div>
		</div>		
		<div class="sppl-block-container">
			<div class="sppl-inner">
				<?foreach($arResult["PROFILES"] as $key => $val) {?>
					<div class="row sppl-inner-items nof sppl-s-separator-blocks">
						<div class="hidden-xs hidden-sm col-xs-1 sppl-inner-item sppl-s-center-t"><?=$val["ID"]?></div><!--
						--><div class="col-xs-4 col-md-3 sppl-inner-item">
							<a href="<?=$val['URL_TO_DETAIL']?>" title="<?=$val['NAME']?>"><?=$val["NAME"]?></a>
						</div><!--
						--><div class="col-xs-4 col-md-3 sppl-inner-item"><?=$val["PERSON_TYPE"]["NAME"]?></div><!--
						--><div class="hidden-xs hidden-sm col-xs-3 sppl-inner-item sppl-s-right-t"><?=$val["DATE_UPDATE"]?></div><!--
						--><div class="col-xs-4 col-md-2 sppl-inner-item sppl-s-center-t">
							<a class="btn btn-default" title="<?=Loc::getMessage('SALE_DELETE_DESCR')?>" href="javascript:if(confirm('<?=Loc::getMessage("STPPL_DELETE_CONFIRM")?>')) window.location='<?=$val["URL_TO_DETELE"]?>'"><i class="icon-trash"></i><span class="hidden-xs hidden-sm"><?=Loc::getMessage("SALE_DELETE")?></span></a>
						</div>
					</div>
				<?}?>
			</div>
		</div>
	</div>
	<?if(strlen($arResult["NAV_STRING"]) > 0)
		echo $arResult["NAV_STRING"];
} else {
	ShowNote(Loc::getMessage("STPPL_EMPTY_PROFILE_LIST"), "warning");
}?>