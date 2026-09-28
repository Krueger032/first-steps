<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
//***********************************
//setting section
//***********************************
?>
<form action="<?= $arResult["FORM_ACTION"];?>" method="post" name="subscribe-settings-form" class="subscribe-settings-form">
	<?=bitrix_sessid_post();?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table top">
		<thead>
			<tr>
				<td colspan="2">
					<h4><?=GetMessage("subscr_title_settings");?></h4>
				</td>
			</tr>
		</thead>
		<tr valign="top">
			<td class="left_blocks">
				<div class="form-control">
					<label><?=GetMessage("subscr_email");?> <span class="star">*</span></label>
					<input type="text" name="EMAIL" value="<?= $arResult["SUBSCRIPTION"]["EMAIL"] != "" ? $arResult["SUBSCRIPTION"]["EMAIL"] : $arResult["REQUEST"]["EMAIL"];?>" size="30" maxlength="255" required />
				</div>
				<div class="adaptive more_text">
					<div class="more_text_small">
						<?=GetMessage("subscr_settings_note1");?><br />
						<?=GetMessage("subscr_settings_note2");?>
					</div>
				</div>
                <div class="form-control">
                    <h5><?=GetMessage("subscr_rub");?><span class="star">*</span></h5 />
                    <div class="filter label_block">
                        <?foreach ($arResult["RUBRICS"] as $itemID => $itemValue):?>
                            <input type="checkbox" name="RUB_ID[]" id="RUB_ID_<?= $itemValue["ID"];?>" value="<?= $itemValue["ID"];?>" <?if ($itemValue["CHECKED"]) echo " checked";?> />
                            <label for="RUB_ID_<?= $itemValue["ID"];?>"><?= $itemValue["NAME"];?></label>
                        <?endforeach;?>
                    </div>
                </div>
                <div class="form-control">
                    <h5><?=GetMessage("subscr_fmt");?></h5>
                    <div class="filter label_block radio">
                        <input type="radio" name="FORMAT" id="txt" value="text" <?if ($arResult["SUBSCRIPTION"]["FORMAT"] == "text") echo " checked";?> /><label for="txt"><?=GetMessage("subscr_text");?></label>&nbsp;/&nbsp;<input type="radio" name="FORMAT" id="html" value="html" <?if ($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo " checked";?> /><label for="html">HTML</label>
                    </div>
                </div>
			</td>
			<td class="right_blocks">
				<div class="more_text_small">
					<?=GetMessage("subscr_settings_note1");?><br />
					<?=GetMessage("subscr_settings_note2");?>
				</div>
			</td>
		</tr>
		<tfoot>
			<tr>
				<td colspan="2">
					<?global $arTheme;?>
					<?if ($arTheme["SHOW_LICENCE"]["VALUE"] == "Y" && !$arResult["ID"]):?>
						<div class="subscribe_licenses">
                            <?
                            TSolution\Functions::showBlockHtml([
                                'FILE' => 'consent/userconsent.php',
                                'PARAMS' => [
                                    'OPTION_CODE' => 'AGREEMENT_SUBSCRIBE',
                                    'SUBMIT_TEXT' => GetMessage("subscr_add"),
                                    'REPLACE_FIELDS' => [],
                                    'INPUT_NAME' => "licenses_subscribe",
                                    'INPUT_ID' => 'licenses_subscribe',
                                ]
                            ]);
                            ?>
						</div>
					<?endif;?>
					<div class="line-block form_footer__bottom">
						<div class="line-block__item">
							<div class="line-block">
								<div class="line-block__item">
									<input type="submit" name="Save" class="btn btn-default" value="<?=($arResult["ID"] > 0 ? GetMessage("subscr_upd") : GetMessage("subscr_add"));?>" />
								</div>
								<div class="line-block__item">
									<input type="reset" class="btn btn-default white subscribe-reset-btn" value="<?=GetMessage("subscr_reset");?>" name="reset" />
								</div>
							</div>
						</div>
						<div class="line-block__item">
							<?$APPLICATION->IncludeFile(SITE_DIR."include/required_message.php", Array(), Array("MODE" => "html"));?>
						</div>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="PostAction" value="<?=($arResult["ID"] > 0 ? "Update" : "Add");?>" />
	<input type="hidden" name="ID" value="<?=$arResult["SUBSCRIPTION"]["ID"];?>" />
	<?if ($_REQUEST["register"] == "YES"):?>
		<input type="hidden" name="register" value="YES" />
	<?endif;?>
	<?if ($_REQUEST["authorize"] == "YES"):?>
		<input type="hidden" name="authorize" value="YES" />
	<?endif;?>
	<input type="hidden" name="check_condition" value="YES" />
</form>
<br />
<script>
    BX.Aspro.Loader.addExt('validate').then(() => {
        $('form[name="subscribe-settings-form"]').validate({
            ignore: ".ignore",
            highlight: function (element) {
                $(element).parent().addClass('error');
            },
            unhighlight: function (element) {
                $(element).parent().removeClass('error');
            },
            submitHandler: function (form) {
                if( $('form[name="subscribe-settings-form"]').valid() ){
                    setTimeout(function() {
                        $(form).find('button[type="submit"]').attr("disabled", "disabled");
                    }, 300);
                    var eventdata = {type: 'form_submit', form: form, form_name: 'subscribe-settings-form'};
                    BX.onCustomEvent('onSubmitForm', [eventdata]);
                }
            },
            errorPlacement: function (error, element) {
                error.insertBefore(element);
            },
            messages: {
                licenses_subscribe: {
                    required: BX.message('JS_REQUIRED_LICENSES')
                }
            }
        });
    });
</script>
