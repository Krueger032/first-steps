<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Text\HtmlFilter;

CUtil::InitJSCore(array("popup"));

$arAuthServices = $arPost = array();
if(is_array($arParams["~AUTH_SERVICES"]))
	$arAuthServices = $arParams["~AUTH_SERVICES"];
if(is_array($arParams["~POST"]))
	$arPost = $arParams["~POST"];

$hiddens = "";
foreach($arPost as $key => $value) {
	if(!preg_match("|OPENID_IDENTITY|", $key))
		$hiddens .= "<input type='hidden' name='".$key."' value='".$value."' />"."\n";
}?>

<script type="text/javascript">
	function BxSocServPopup(id) {
		var content = BX('bx_socserv_form_' + id);
		if(content) {
			var popup = BX.PopupWindowManager.create('socServPopup' + id, BX('bx_socserv_icon_' + id), {
				autoHide: true,
				closeByEsc: true,
				angle: {offset: 41},
				content: content,
				offsetTop: 3,
				zIndex: 1100
			});

			popup.show();

			var input = BX.findChild(content, {'tag': 'input', 'attribute': {'type': 'text'}}, true);
			if(input) {
				input.focus();
				input.className = 'form-control';
			}

			var span = BX.findChildren(content, {'tag': 'span'}, true);
			if(span) {
				for(var i = 0; i < span.length; i++) {
					var spanClassIn = span[i].getAttribute('class');
					if(!!!(spanClassIn)) {
						span[i].className += 'bx-socserv_form-text';
					}
				}
			}

			var button = BX.findChild(content, {'tag': 'input', 'attribute': {'type': 'submit'}}, true);
			if(button)
				BX.remove(button);
		}
	}
</script>

<div class="bx-authform-social">
	<ul>
		<?foreach($arAuthServices as $service) {
			$onclick = ($service["ONCLICK"] <> "" ? $service["ONCLICK"] : "BxSocServPopup('".$service["ID"]."')");?>
			<li>
				<a id="bx_socserv_icon_<?=$service['ID']?>" class="<?=HtmlFilter::encode($service['ICON'])?> bx-authform-social-icon" href="javascript:void(0)" onclick="<?=HtmlFilter::encode($onclick)?>" title="<?=HtmlFilter::encode($service["NAME"])?>"></a>
				<?if($service["ONCLICK"] == "" && $service["FORM_HTML"] <> "") {?>
					<div id="bx_socserv_form_<?=$service["ID"]?>" class="bx-authform-social-popup">
						<form action="<?=$arParams["AUTH_URL"]?>" method="post">
							<?=$service["FORM_HTML"]?>
							<button type="submit" class="btn btn-buy" title="<?=GetMessage('socserv_submit_button')?>"><i class="icon-arrow-right"></i></button>
							<?=$hiddens?>
							<input type="hidden" name="auth_service_id" value="<?=$service["ID"]?>" />
						</form>
					</div>
				<?}?>
			</li>
		<?}?>
	</ul>
</div>
