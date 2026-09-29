<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;?>

<div class="bx-authform">
	<div class="bx-authform-title-container">
		<div class="bx-authform-title">
			<div class="bx-authform-title-icon"><i class="icon-unlock"></i></div>
			<div class="bx-authform-title-val"><?=Loc::getMessage("AUTH_FORM_TITLE")?></div>
		</div>
	</div>	
	<div class="bx-authform-socserv-container">
		<div class="col-xs-12 col-md-4 bx-authform-socserv">
			<?if(!empty($arParams["~AUTH_RESULT"]))
				if(is_array($arParams["~AUTH_RESULT"]))
					ShowNote($arParams["~AUTH_RESULT"]["MESSAGE"], ($arParams["~AUTH_RESULT"]["TYPE"] == "OK" ? "success" : "error"));
				else
					ShowMessage($arParams["~AUTH_RESULT"]);			
			
			if(!empty($arResult["ERROR_MESSAGE"]))
				ShowError($arResult["ERROR_MESSAGE"]);?>

			<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_LOGIN_SOCSERV")?></div>
			<?if($arResult["AUTH_SERVICES"]) {?>
				<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "flat",
					array(
						"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
						"AUTH_URL" => $arResult["AUTH_URL"],
						"POST" => $arResult["POST"],
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);?>
			<?}?>
		</div>
		<div class="clearfix"></div>
	</div>
	<form name="form_auth" method="post" target="_top" action="<?=$arResult['AUTH_URL'];?>">
		<div class="bx-authform-content-container">
			<div class="col-xs-12 col-md-4 bx-authform-content">
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="AUTH" />				
				<?if(strlen($arResult["BACKURL"]) > 0) {?>
					<input type="hidden" name="backurl" value="<?=$arResult['BACKURL']?>" />
				<?}
				foreach($arResult["POST"] as $key => $value) {?>
					<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
				<?}?>
				<div class="form-group">
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_LOGIN")?></div>
					<input type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult['LAST_LOGIN']?>" class="form-control" />
				</div>
				<div class="form-group bx-authform-psw-container">
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_PASSWORD")?></div>
					<?if($arResult["SECURE_AUTH"]) {?>						
						<div class="bx-authform-psw-protected" id="bx_auth_secure" style="display: none;">
							<div class="bx-authform-psw-protected-desc"><?=Loc::getMessage("AUTH_SECURE_NOTE")?></div>
						</div>
						<script type="text/javascript">
							document.getElementById("bx_auth_secure").style.display = "";
						</script>						
					<?}?>						
					<input type="password" name="USER_PASSWORD" maxlength="255" autocomplete="off" class="form-control" />
				</div>				
				<?if($arResult["CAPTCHA_CODE"]) {?>				
					<div class="bx-authform-label-container"><?=Loc::getMessage("AUTH_CAPTCHA")?></div>
					<div class="form-group captcha">
						<div class="pic">
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>" width="100" height="36" alt="CAPTCHA">
						</div>
						<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" class="form-control" />
						<input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE']?>" />
					</div>
				<?}
				if($arResult["STORE_PASSWORD"] == "Y") {?>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" />
								<span class="check-cont"><span class="check"><i class="icon-ok-b"></i></span></span>
								<span class="check-title"><?=Loc::getMessage("AUTH_REMEMBER_ME")?></span>
							</label>
						</div>
					</div>
				<?}?>
			</div>
			<div class="clearfix"></div>
		</div>		
		<div class="form-group bx-authform-buttons-container">
			<button type="submit" class="btn btn-buy" name="Login" value="<?=Loc::getMessage('AUTH_AUTHORIZE')?>"><span><?=Loc::getMessage("AUTH_AUTHORIZE")?></span></button>
			<?if($arParams["NOT_SHOW_LINKS"] != "Y") {?>
				<a href="<?=$arResult['AUTH_FORGOT_PASSWORD_URL']?>" rel="nofollow" class="btn btn-default"><?=Loc::getMessage("AUTH_FORGOT_PASSWORD")?></a>
			<?}
			if($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y") {?>
				<a href="<?=$arResult['AUTH_REGISTER_URL']?>" rel="nofollow" class="btn btn-default"><?=Loc::getMessage("AUTH_REGISTER")?></a>
			<?}?>
		</div>		
	</form>
</div>

<script type="text/javascript">
	<?if(strlen($arResult["LAST_LOGIN"]) > 0) {?>
		try {
			document.form_auth.USER_PASSWORD.focus();
		} catch(e) {}
	<?} else {?>
		try {
			document.form_auth.USER_LOGIN.focus();
		} catch(e) {}
	<?}?>
</script>