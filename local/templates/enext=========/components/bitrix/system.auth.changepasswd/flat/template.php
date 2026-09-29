<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if($arResult["PHONE_REGISTRATION"])
	CJSCore::Init("phone_auth");?>

<div class="bx-authform">
	<div class="bx-authform-title-container">
		<div class="bx-authform-title">
			<div class="bx-authform-title-icon"><i class="icon-unlock"></i></div>
			<div class="bx-authform-title-val"><?=Loc::getMessage("AUTH_CHANGE_PASSWORD")?></div>
		</div>
	</div>
	<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
		<div class="bx-authform-content-container">
			<div class="col-xs-12 col-md-4 bx-authform-content">
				<?if(!empty($arParams["~AUTH_RESULT"]))
					if(is_array($arParams["~AUTH_RESULT"]))
						ShowNote($arParams["~AUTH_RESULT"]["MESSAGE"], ($arParams["~AUTH_RESULT"]["TYPE"] == "OK" ? "success" : "error"));
					else
						ShowMessage($arParams["~AUTH_RESULT"]);
				
				if($arResult["BACKURL"] <> "") {?>
					<input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>">
				<?}?>
				<input type="hidden" name="AUTH_FORM" value="Y">
				<input type="hidden" name="TYPE" value="CHANGE_PWD">
				<?if($arResult["PHONE_REGISTRATION"]) {?>
					<div id="bx_chpass_error" style="display: none;"></div>
					<div id="bx_chpass_resend"></div>
					<div class="form-group">
						<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_PHONE_NUMBER")?></div>
						<input type="text" value="<?=htmlspecialcharsbx($arResult['USER_PHONE_NUMBER'])?>" disabled="disabled" class="form-control" />
						<input type="hidden" name="USER_PHONE_NUMBER" value="<?=htmlspecialcharsbx($arResult['USER_PHONE_NUMBER'])?>" />
					</div>
					<div class="form-group">
						<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_CODE")?></div>
						<input type="text" name="USER_CHECKWORD" maxlength="255" value="<?=$arResult['USER_CHECKWORD']?>" autocomplete="off" class="form-control" />
					</div>
				<?} else {?>
					<div class="form-group">
						<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_LOGIN")?></div>
						<input type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult['LAST_LOGIN']?>" class="form-control" />
					</div>
					<div class="form-group">
						<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_CHECKWORD")?></div>
						<input type="text" name="USER_CHECKWORD" maxlength="255" value="<?=$arResult['USER_CHECKWORD']?>" class="form-control" />
					</div>
				<?}?>
				<div class="form-group bx-authform-psw-container">
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_NEW_PASSWORD_REQ")?></div>
					<?if($arResult["SECURE_AUTH"]) {?>
						<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display:none">
							<div class="bx-authform-psw-protected-desc"><?=Loc::getMessage("AUTH_SECURE_NOTE")?></div>
						</div>
						<script type="text/javascript">
							document.getElementById('bx_auth_secure').style.display = '';
						</script>
					<?}?>
					<input type="password" name="USER_PASSWORD" maxlength="255" value="<?=$arResult['USER_PASSWORD']?>" autocomplete="off" class="form-control" />
				</div>
				<div class="form-group bx-authform-psw-container">
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_NEW_PASSWORD_CONFIRM")?></div>
					<?if($arResult["SECURE_AUTH"]) {?>
						<div class="bx-authform-psw-protected" id="bx_auth_secure_conf" style="display:none">
							<div class="bx-authform-psw-protected-desc"><?=Loc::getMessage("AUTH_SECURE_NOTE")?></div>
						</div>
						<script type="text/javascript">
							document.getElementById('bx_auth_secure_conf').style.display = '';
						</script>
					<?}?>
					<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?=$arResult['USER_CONFIRM_PASSWORD']?>" autocomplete="off" class="form-control" />
				</div>
				<?if($arResult["USE_CAPTCHA"]) {?>
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_CAPTCHA")?></div>
					<div class="form-group captcha">
						<div class="pic">
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="100" height="36" alt="CAPTCHA" />
						</div>
						<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" class="form-control" />
						<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>" />
					</div>
				<?}?>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-group bx-authform-buttons-container">
			<button type="submit" class="btn btn-buy" name="change_pwd" value="<?=Loc::getMessage('AUTH_CHANGE')?>"><span><?=Loc::getMessage("AUTH_CHANGE")?></span></button>
			<a href="<?=$arResult["AUTH_AUTH_URL"]?>" class="btn btn-default"><?=Loc::getMessage("AUTH_AUTH")?></a>
		</div>
	</form>
</div>

<script type="text/javascript">
	<?if($arResult["PHONE_REGISTRATION"]) {?>
		new BX.PhoneAuth({
			containerId: 'bx_chpass_resend',
			errorContainerId: 'bx_chpass_error',
			interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
			data:
				<?=CUtil::PhpToJSObject([
					'signedData' => $arResult["SIGNED_DATA"]
				])?>,
			onError:
				function(response) {
					var errorNode = BX('bx_chpass_error');
					BX.cleanNode(errorNode);
					for(var i = 0; i < response.errors.length; i++) {
						BX.append(BX.create('SPAN', {
							props: {
								className: 'alert alert-error'
							},
							html: BX.util.htmlspecialchars(response.errors[i].message)
						}), errorNode);
					}
					BX.style(errorNode, 'display', '');
				}
		});
	<?}?>
	document.bform.USER_LOGIN.focus();
</script>
