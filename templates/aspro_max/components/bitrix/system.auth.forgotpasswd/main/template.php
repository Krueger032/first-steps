<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
$this->setFrameMode(false);

if (!include_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new Exception('Error include solution constants');
}

if (isset($APPLICATION->arAuthResult)) {
    $arResult['ERROR_MESSAGE'] = $APPLICATION->arAuthResult;
}

$bEmailAsLogin = TSolution::GetFrontParametrValue('LOGIN_EQUAL_EMAIL') === 'Y';
$fieldLang = $bEmailAsLogin ? GetMessage('forgot_pass_email') : GetMessage('forgot_pass_login');

$bUsePhoneInput = $arResult['PHONE_REGISTRATION'];
$bByPhoneRequest = $bUsePhoneInput && isset($_POST['USER_PHONE_NUMBER']) && $_POST['USER_PHONE_NUMBER'];
$bActivePhoneTab = $bByPhoneRequest || ($bUsePhoneInput && $_SERVER['REQUEST_METHOD'] === 'GET');
?>
<div class="forgotpasswd-page pk-page">
    <?if ($arResult['ERROR_MESSAGE']):?>
        <div class="alert <?= $arResult['ERROR_MESSAGE']['TYPE'] === 'OK' ? 'alert-success' : 'alert-danger';?> compact"><?=$arResult['ERROR_MESSAGE']['MESSAGE'];?></div>
    <?endif;?>

    <?if (!$arResult['ERROR_MESSAGE'] || $arResult['ERROR_MESSAGE']['TYPE'] != 'OK'):?>
        <div class="form">
            <form id="forgotpasswd-page-form" method="post" action="<?=POST_FORM_ACTION_URI;?>" name="bform">
                <?$description_file = $bEmailAsLogin ? '_email' : '';?>
                <div class="top-text">
                    <?if ($arResult['PHONE_REGISTRATION']):?>
                        <?$APPLICATION->IncludeFile(SITE_DIR.'include/forgotpasswd_phone'.$description_file.'_description.php', [], ['MODE' => 'html', 'NAME' => '']);?>
                    <?else:?>
                        <?$APPLICATION->IncludeFile(SITE_DIR.'include/forgotpasswd'.$description_file.'_description.php', [], ['MODE' => 'html', 'NAME' => '']);?>
                    <?endif;?>
                </div>

                <?if ($arResult['BACKURL'] != ''):?>
                    <input type="hidden" name="backurl" value="<?=$arResult['BACKURL'];?>" />
                <?endif;?>

                <input type="hidden" name="AUTH_FORM" value="Y">
                <input type="hidden" name="TYPE" value="SEND_PWD">
                <div class="form_body form-control">
                    <?if ($bUsePhoneInput):?>
                        <div class="tabs tabs--compact">
                            <ul class="nav nav-tabs">
                                <li class="<?= $bActivePhoneTab ? 'active' : '';?>"><a href="#forgot_by_phone" data-toggle="tab"><?=GetMessage('AUTH_FORGOT_BY_PHONE');?></a></li>
                                <li class="<?= $bActivePhoneTab ? '' : 'active';?>"><a href="#forgot_by_login" data-toggle="tab"><?=GetMessage('AUTH_FORGOT_BY_LOGIN_OR_EMAIL', ['#FIELD#' => $fieldLang]);?></a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane <?= $bActivePhoneTab ? 'active' : '';?>" id="forgot_by_phone">
                    <?endif;?>

                    <?if ($bUsePhoneInput):?>
                            <div class="form-control phone_or_login">
                                <label for="USER_PHONE_NUMBER"class=""><span><?=GetMessage('forgot_pass_phone_number');?>&nbsp;<span class="star">*</span></span></label>
                                <input id="USER_PHONE_NUMBER" class="required phone" type="tel" name="USER_PHONE_NUMBER" maxlength="255" autocomplete="off" />

                                <div class="text-block"><?=GetMessage('forgot_pass_phone_number_note');?></div>
                            </div>
                        </div><?// .tab-pane?>
                        <div class="tab-pane <?= $bActivePhoneTab ? '' : 'active';?>" id="forgot_by_login">
                    <?endif;?>

                            <div class="form-control">
                                <label for="FORGOTPASSWD_USER_LOGIN"><span><?=GetMessage('AUTH_FORGOT_BY_LOGIN_OR_EMAIL', ['#FIELD#' => $fieldLang]);?>&nbsp;<span class="star">*</span></span></label>
                                <input id="FORGOTPASSWD_USER_LOGIN" type="<?= $bEmailAsLogin ? 'email' : 'text';?>" name="USER_LOGIN" required maxlength="255" autocomplete="off" />
                                <input type="hidden" name="USER_EMAIL" maxlength="255" autocomplete="off" />
                                <div class="text-block"><?=GetMessage('forgot_pass_login_note');?></div>
                            </div>

                    <?if ($bUsePhoneInput):?>
                            </div> <?// .tab-pane?>
                        </div> <?// .tab-content?>
                    </div> <?// .tabs?>
                    <?endif;?>

                    <?if ($arResult['USE_CAPTCHA']):?>
                        <?php
                        /** @var TSolution\Captcha\Service $captcha */
                        $captcha = TSolution\Captcha::getInstance();
                        ?>
                        <div class="clearboth"></div>
                        <div class="form-control captcha-row clearfix">
                            <label for="FORGOTPASSWD_CAPTCHA"><span><?=$captcha->isService() && $captcha->isActive() ? GetMessage("FORM_GENERAL_RECAPTCHA") : GetMessage("CAPTCHA_PROMT");?>&nbsp;<span class="star">*</span></span></label>
                            <div class="captcha_image">
                                <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE'];?>" border="0" />
                                <input type="hidden" name="captcha_sid" value="<?=$arResult['CAPTCHA_CODE'];?>" />
                                <div class="captcha_reload"><?=GetMessage('RELOAD');?></div>
                            </div>
                            <div class="captcha_input">
                                <input id="FORGOTPASSWD_CAPTCHA" type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required />
                            </div>
                        </div>
                        <div class="clearboth"></div>
                    <?endif;?>
                </div>

                <div class="form_footer">
                    <div class="line-block form_footer__bottom">
                        <div class="line-block__item">
                            <button class="btn btn-default btn-lg" type="submit" name="send_account_info" value="Y"><span><?=GetMessage('RETRIEVE');?></span></button>
                        </div>
                        <div class="line-block__item">
                            <?$APPLICATION->IncludeFile(SITE_DIR.'include/required_message.php', [], ['MODE' => 'html']);?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?endif;?>
    <script>
        BX.Aspro.Utils.readyDOM(() => {
            $('#forgotpasswd-page-form').validate({
                highlight: function(element) {
                    $(element).parent().addClass('error');
                },
                unhighlight: function(element) {
                    $(element).parent().removeClass('error');
                },
                submitHandler: function(form) {
                    if ($(form).valid()) {
                        var $button = $(form).find('button[type=submit]');
                        if ($button.length) {

                            // sync USER_EMAIL value with USER_LOGIN
                            $(form).find('[name=USER_EMAIL]').val($(form).find('[name=USER_LOGIN]').val());

                            if (!$button.hasClass('loadings')) {
                                $button.addClass('loadings');

                                var eventdata = {type: 'form_submit', form: form, form_name: 'FORGOT'};
                                BX.onCustomEvent('onSubmitForm', [eventdata]);
                            }
                        }
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertBefore(element);
                },
            });

            setTimeout(function() {
                $('#forgotpasswd-page-form').find('input:visible').eq(0).focus();
            }, 50);

        });

        if (typeof appAspro === 'object' && appAspro && appAspro.phone) {
            appAspro.phone.init($('#forgotpasswd-page-form input.phone'), {
                coutriesData: '<?=TSolution::$arParametrsList['FORMS']['OPTIONS']['USE_INTL_PHONE']['DEPENDENT_PARAMS']['PHONE_CITIES']['TYPE_SELECT']['SRC'];?>',
                mask: arAsproOptions['THEME']['PHONE_MASK'],
                onlyCountries: '<?=TSolution::GetFrontParametrValue('PHONE_CITIES');?>',
                preferredCountries: '<?=TSolution::GetFrontParametrValue('PHONE_CITIES_FAVORITE');?>'
            });
        }
    </script>
</div>
<?php
$arExtensions = ['validate', 'phone_input'];
if (TSolution::GetFrontParametrValue('USE_INTL_PHONE') === 'Y') {
    $arExtensions[] = 'intl_phone_input';
} elseif (TSolution::GetFrontParametrValue('PHONE_MASK')) {
    $arExtensions[] = 'phone_mask';
}

if ($bUsePhoneInput) {
    $arExtensions[] = 'tabs';
}
TSolution\Extensions::init($arExtensions);
