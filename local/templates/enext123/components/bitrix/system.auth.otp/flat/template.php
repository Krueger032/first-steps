<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

if($arResult["REQUIRED_BY_MANDATORY"] === true) {
	$APPLICATION->IncludeComponent("bitrix:security.auth.otp.mandatory", "",
		array(
			"AUTH_LOGIN_URL" => $arResult["~AUTH_LOGIN_URL"],
			"NOT_SHOW_LINKS" => $arParams["NOT_SHOW_LINKS"]
		)
	);
} else {?>
	<div class="bx-authform">
		<div class="bx-authform-title-container">
			<div class="bx-authform-title">
				<div class="bx-authform-title-icon"><i class="icon-unlock"></i></div>
				<div class="bx-authform-title-val"><?=Loc::getMessage("AUTH_OTP_FORM_TITLE")?></div>
			</div>
		</div>
		<form name="form_auth" method="post" target="_top" action="<?=$arResult['AUTH_URL']?>">
			<div class="bx-authform-content-container">
				<div class="col-xs-12 col-md-4 bx-authform-content">
					<?if(!empty($arParams["~AUTH_RESULT"]))
						if(is_array($arParams["~AUTH_RESULT"]))
							ShowNote($arParams["~AUTH_RESULT"]["MESSAGE"], ($arParams["~AUTH_RESULT"]["TYPE"] == "OK" ? "success" : "error"));
						else
							ShowMessage($arParams["~AUTH_RESULT"]);?>						
					<input type="hidden" name="AUTH_FORM" value="Y" />
					<input type="hidden" name="TYPE" value="OTP" />
					<div class="form-group">
						<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_OTP_OTP")?></div>
						<input type="text" name="USER_OTP" maxlength="50" value="" autocomplete="off" class="form-control" />
					</div>
					<?if($arResult["CAPTCHA_CODE"]) {?>
						<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_OTP_CAPTCHA")?></div>
						<div class="form-group captcha">
							<div class="pic">
								<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="100" height="36" alt="CAPTCHA" />
							</div>
							<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" class="form-control" />
							<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>" />
						</div>
					<?}
					if($arResult["REMEMBER_OTP"]) {?>
						<div class="form-group">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="OTP_REMEMBER" value="Y" />
									<span class="check-cont"><span class="check"><i class="icon-ok-b"></i></span></span>
									<span class="check-form"><?=Loc::getMessage("AUTH_OTP_REMEMBER_ME")?></span>
								</label>
							</div>
						</div>
					<?}?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-group bx-authform-buttons-container">
				<button type="submit" class="btn btn-buy" name="Otp" value="<?=Loc::getMessage('AUTH_OTP_AUTHORIZE')?>"><span><?=Loc::getMessage("AUTH_OTP_AUTHORIZE")?></span></button>
				<?if($arParams["NOT_SHOW_LINKS"] != "Y") {?>
					<a href="<?=$arResult['AUTH_LOGIN_URL']?>" class="btn btn-default" rel="nofollow"><?=Loc::getMessage("AUTH_OTP_AUTH_BACK")?></a>
				<?}?>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		try{
			document.form_auth.USER_OTP.focus();
		} catch(e) {}
	</script>
<?}?>