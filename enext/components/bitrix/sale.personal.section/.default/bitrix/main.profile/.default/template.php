<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if($arResult["SHOW_SMS_FIELD"] == true)
	CJSCore::Init("phone_auth");?>

<div class="main-profile">
	<?ShowError($arResult["strProfileError"]);
	
	if($arResult["DATA_SAVED"] == "Y")
		ShowNote(Loc::getMessage("PROFILE_DATA_SAVED"), "success");

	if($arResult["SHOW_SMS_FIELD"] == true) {?>
		<form method="post" action="<?=$arResult['FORM_TARGET']?>">
			<?=$arResult["BX_SESSION_CHECK"]?>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="hidden" name="ID" value=<?=$arResult['ID']?> />
			<input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult['SIGNED_DATA'])?>" />
			<div class="mb-personal-data">
				<div class="mb-title-container">
					<div class="mb-title">
						<div class="mb-title__icon"><i class="icon-user"></i></div>
						<div class="mb-title__val"><?=Loc::getMessage("MP_TITLE_PERSONAL_DATA")?></div>
					</div>
				</div>			
				<div class="mb-block-container">
					<div class="row">
						<div class="col-xs-12 col-md-4 mb-personal-data-inner">
							<div id="bx_profile_error" style="display: none;"></div>
							<div id="bx_profile_resend"></div>
							<div class="form-group">
								<div class="mb-label-container"><?=Loc::getMessage("main_profile_code")?> <span class="mb-starrequired">*</span></div>
								<input type="text" name="SMS_CODE" maxlength="50" value="<?=htmlspecialcharsbx($arResult['SMS_CODE'])?>" class="form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group mb-buttons-container">
				<button type="submit" name="code_submit_button" class="btn btn-buy" value="<?=Loc::getMessage('main_profile_send')?>"><span><?=Loc::getMessage("main_profile_send")?></span></button>
			</div>
		</form>
		<script type="text/javascript">
			new BX.PhoneAuth({
				containerId: 'bx_profile_resend',
				errorContainerId: 'bx_profile_error',
				interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
				data:
					<?=CUtil::PhpToJSObject([
						'signedData' => $arResult["SIGNED_DATA"],
					])?>,
				onError:
					function(response) {
						var errorNode = BX('bx_register_error');
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
		</script>
	<?} else {?>
		<form method="post" name="form1" action="<?=$arResult['FORM_TARGET']?>" enctype="multipart/form-data" role="form">
			<?=$arResult["BX_SESSION_CHECK"]?>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="hidden" name="ID" value="<?=$arResult['ID']?>" />
			<input type="hidden" name="LOGIN" value="<?=$arResult['arUser']['LOGIN']?>" />
			<div class="mb-personal-data">
				<div class="mb-title-container">
					<div class="mb-title">
						<div class="mb-title__icon"><i class="icon-user"></i></div>
						<div class="mb-title__val"><?=Loc::getMessage("MP_TITLE_PERSONAL_DATA")?></div>
					</div>
				</div>			
				<div class="mb-block-container">
					<div class="row">
						<div class="col-xs-12 col-md-4 mb-personal-data-inner">
							<div class="mb-personal-data-inner-date-info">
								<?if($arResult["ID"] > 0) {
									if(strlen($arResult["arUser"]["TIMESTAMP_X"]) > 0) {?>
										<div class="mb-personal-data-inner-date-info__last-update"><?=Loc::getMessage("LAST_UPDATE"). " ".$arResult["arUser"]["TIMESTAMP_X"]?></div>
									<?}
									if(strlen($arResult["arUser"]["LAST_LOGIN"]) > 0) {?>
										<div class="mb-personal-data-inner-date-info__last-login"><?=Loc::getMessage("LAST_LOGIN")." ".$arResult["arUser"]["LAST_LOGIN"]?></div>
									<?}
								}?>
							</div>
							<?if(!in_array(LANGUAGE_ID, array("ru"))) {?>
								<div class="form-group">
									<div class="mb-label-container"><?=Loc::getMessage("main_profile_title")?></div>
									<input type="text" name="TITLE" maxlength="50" value="<?=$arResult['arUser']['TITLE']?>" class="form-control" />
								</div>
							<?}?>
							<div class="form-group">
								<div class="mb-label-container"><?=Loc::getMessage("MP_NAME")?></div>
								<input type="text" name="NAME" maxlength="50" value="<?=$arResult['arUser']['NAME']?>" class="form-control" />
							</div>
							<div class="form-group">
								<div class="mb-label-container"><?=Loc::getMessage("MP_LAST_NAME")?></div>
								<input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult['arUser']['LAST_NAME']?>" class="form-control" />
							</div>
							<div class="form-group">
								<div class="mb-label-container"><?=Loc::getMessage("MP_SECOND_NAME")?></div>
								<input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult['arUser']['SECOND_NAME']?>" class="form-control" />
							</div>
							<div class="form-group">
								<div class="mb-label-container"><?=Loc::getMessage('MP_LOGIN')?> <span class="mb-starrequired">*</span></div>
								<input type="text" name="LOGIN" maxlength="50" value="<?=$arResult['arUser']['LOGIN']?>" class="form-control" />
							</div>
							<div class="form-group">
								<div class="mb-label-container"><?=Loc::getMessage("MP_EMAIL").($arResult["EMAIL_REQUIRED"] ? " <span class='mb-starrequired'>*</span>" : "");?></div>
								<input type="text" name="EMAIL" maxlength="50" value="<?=$arResult['arUser']['EMAIL']?>" class="form-control" />
							</div>
							<?if($arResult["PHONE_REGISTRATION"]) {?>
								<div class="form-group">
									<div class="mb-label-container"><?=Loc::getMessage("main_profile_phone_number").($arResult["PHONE_REQUIRED"] ? " <span class='mb-starrequired'>*</span>" : "");?></div>
									<input type="text" name="PHONE_NUMBER" maxlength="50" value="<?=$arResult['arUser']['PHONE_NUMBER']?>" class="form-control" />
								</div>
							<?}?>
							<div class="mb-label-container"><?=Loc::getMessage("MP_USER_PHOTO")?></div>
							<?if(!empty($arResult["arUser"]["PERSONAL_PHOTO_HTML"])) {?>
								<div class="mb-photo-container"><?=$arResult["arUser"]["PERSONAL_PHOTO_HTML"]?></div>
								<div class="form-group">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="PERSONAL_PHOTO_del" value="Y" id="PERSONAL_PHOTO_del">
											<span class="check-cont"><span class="check"><i class="icon-ok-b"></i></span></span>
											<a class="check-title check-form"><?=Loc::getMessage("MP_DELETE_PHOTO")?></a>
										</label>
									</div>
								</div>
							<?}?>
							<div class="form-group form-group-file">
								<label class="btn btn-buy" for="form-group-file-input"><span><?=Loc::getMessage("MP_SELECT_PHOTO")?></span></label>
								<input id="form-group-file-input" type="file" name="PERSONAL_PHOTO" size="20" />
								<div class="form-group-file-val"><?=Loc::getMessage("MP_NOT_PHOTO")?></div>
							</div>
							<script type="text/javascript">
								var wrapper = $(".form-group-file"),
									inp = wrapper.find("input"),
									btn = wrapper.find("label"),
									lbl = wrapper.find("div");
									
								btn.focus(function() {
									inp.focus();
								});
								
								var file_api = (window.File && window.FileReader && window.FileList && window.Blob) ? true : false;

								inp.change(function() {
									var file_name;
									if(file_api && inp[0].files[0])
										file_name = inp[0].files[0].name;
									else
										file_name = inp.val().replace("C:\\fakepath\\", "");

									if(!file_name.length)
										return;

									if(lbl.is(":visible") )
										lbl.text(file_name);
									
								}).change();
								
								$(window).resize(function(){
									$(".form-group-file input").triggerHandler("change");
								});
							</script>
						</div>
					</div>
				</div>
			</div>
			<?if($arResult["CAN_EDIT_PASSWORD"]) {?>
				<div class="mb-change-password">
					<div class="mb-title-container">
						<div class="mb-title">
							<div class="mb-title__icon"><i class="icon-unlock"></i></div>
							<div class="mb-title__val"><?=Loc::getMessage("MP_TITLE_CHANGE_PASSWORD")?></div>
						</div>
					</div>				
					<div class="mb-block-container">
						<div class="row">
							<div class="col-xs-12 col-md-4 mb-change-password-inner">
								<div class="form-group mb-psw-container">
									<div class="mb-label-container"><?=Loc::getMessage("MP_NEW_PASSWORD_REQ")." (".$arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"].")"?></div>
									<?if($arResult["SECURE_AUTH"]) {?>								
										<div class="mb-psw-protected" id="bx_auth_secure" style="display: none;">
											<div class="mb-psw-protected-desc"><?=Loc::getMessage("MP_AUTH_SECURE_NOTE")?></div>
										</div>
										<script type="text/javascript">
											document.getElementById("bx_auth_secure").style.display = "";
										</script>								
									<?}?>
									<input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="form-control"/>
								</div>
								<div class="form-group mb-psw-container">
									<div class="mb-label-container"><?=Loc::getMessage("MP_NEW_PASSWORD_CONFIRM")?></div>
									<?if($arResult["SECURE_AUTH"]) {?>								
										<div class="mb-psw-protected" id="bx_auth_secure_conf" style="display: none;">
											<div class="mb-psw-protected-desc"><?=Loc::getMessage("MP_AUTH_SECURE_NOTE")?></div>
										</div>
										<script type="text/javascript">
											document.getElementById("bx_auth_secure_conf").style.display = "";
										</script>								
									<?}?>
									<input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" class="form-control" />
								</div>
							</div>
						</div>
					</div>
				</div>
			<?}?>
			<div class="form-group mb-buttons-container">
				<button type="submit" name="save" class="btn btn-buy" value="<?=($arResult['ID'] > 0 ? Loc::getMessage('MP_MAIN_SAVE') : Loc::getMessage('MP_MAIN_ADD'))?>"><span><?=($arResult["ID"] > 0 ? Loc::getMessage("MP_MAIN_SAVE") : Loc::getMessage("MP_MAIN_ADD"))?></span></button>
				<input type="submit" class="btn btn-default"  name="reset" value="<?=Loc::getMessage('MP_MAIN_RESET')?>">
			</div>
		</form>
		<?if($arResult["SOCSERV_ENABLED"]) {?>
			<div class="mb-social-block">
				<div class="mb-title-container">
					<div class="mb-title">
						<div class="mb-title__icon"><i class="icon-share"></i></div>
						<div class="mb-title__val"><?=Loc::getMessage("MP_TITLE_SOCIAL_BLOCK")?></div>
					</div>
				</div>
				<div class="mb-block-container">
					<div class="mb-social-block-inner">
						<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "",
							array(
								"SHOW_PROFILES" => "Y",
								"ALLOW_DELETE" => "Y"
							),
							false
						);?>
					</div>
				</div>
			</div>
		<?}
	}?>
</div>