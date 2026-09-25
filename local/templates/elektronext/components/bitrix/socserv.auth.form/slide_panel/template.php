<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CUtil::InitJSCore(array("popup"));

$arAuthServices = $arPost = array();
if(is_array($arParams["~AUTH_SERVICES"])) {
	$arAuthServices = $arParams["~AUTH_SERVICES"];
}
if(is_array($arParams["~POST"])) {
	$arPost = $arParams["~POST"];
}

$hiddens = "";
foreach($arPost as $key => $value) {
	if(!preg_match("|OPENID_IDENTITY|", $key)) {
		$hiddens .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
	}
}?>

<div class="bx-authform-social">
	<ul>
		<?foreach($arAuthServices as $service) {
			$onclick = ($service["ONCLICK"] <> "" ? $service["ONCLICK"] : "slidePanelBxSocServPopup('".$service["ID"]."')");?>
			<li>
				<a id="bx_slide_panel_socserv_icon_<?=$service['ID']?>" class="<?=Bitrix\Main\Text\HtmlFilter::encode($service['ICON'])?> bx-authform-social-icon" href="javascript:void(0)" onclick="<?=Bitrix\Main\Text\HtmlFilter::encode($onclick)?>" title="<?=Bitrix\Main\Text\HtmlFilter::encode($service['NAME'])?>"></a>
				<?if($service["ONCLICK"] == "" && $service["FORM_HTML"] <> "") {?>
					<div id="bx_slide_panel_socserv_form_<?=$service['ID']?>" class="bx-authform-social-popup">
						<form action="<?=$arParams['AUTH_URL']?>" method="post">
							<?=$service["FORM_HTML"]?>
							<button type="submit" class="btn btn-buy" title="<?=GetMessage('socserv_submit_button')?>"><i class="icon-arrow-right"></i></button>
							<?=$hiddens?>
							<input type="hidden" name="auth_service_id" value="<?=$service['ID']?>" />
						</form>
					</div>
				<?}?>
			</li>
		<?}?>
	</ul>
</div>