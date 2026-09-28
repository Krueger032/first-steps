<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
$this->setFrameMode(false);

if (!include_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new Exception('Error include solution constants');
}

$arError = $arResult['ERROR_MESSAGE'];
TSolution\PhoneAuth::modifyResult($arResult, $arParams);
$authType = 'login';

$bUsePhoneInput = $arResult['PHONE_AUTH_PARAMS']['USE'];

$context = Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

if ($bUsePhoneInput) {
    $authType = 'phone';
    echo CJSCore::Init('phone_auth', true);
}
if ($request->get('AUTH_TYPE')) {
    $authType = htmlspecialcharsbx($request->get('AUTH_TYPE'));

    if ($authType == 'phone') {
        $arError = $arResult['ERROR_MESSAGE'];
    }
}

$bActivePhoneTab = $authType == 'phone';
?>
<?if ($arResult['FORM_TYPE'] === 'login'):?>
    <?if (
        $arResult['ERROR']
        && $arResult['ERROR_MESSAGE']['TYPE'] === 'ERROR'
        && $arResult['ERROR_MESSAGE']['ERROR_TYPE'] === 'CHANGE_PASSWORD'
        && $arParams['CHANGE_PASSWORD_URL']
    ):?>
        <?php
        $_SESSION['arAuthResult'] = $APPLICATION->arAuthResult;
        $_SESSION['lastLoginSave'] = $arResult['USER_LOGIN'];
        ?>
        <script>
            location.href = '<?=$arParams['CHANGE_PASSWORD_URL'].(strlen($arResult['BACKURL']) ? (strpos($arParams['CHANGE_PASSWORD_URL'], '?') ? '&' : '?').'backurl='.$arResult['BACKURL'] : '');?>';
        </script>
    <?else:?>
        <div id="ajax_auth" class="auth-page pk-page">
            <div class="auth_wrapp">
                <div class="wrap_md1">
                    <div class="form">
                        <form id="auth-page-form" name="system_auth_form<?=$arResult['RND'];?>" method="post" target="_top" action="<?=$arParams['AUTH_URL'];?>?login=yes">
                            <?if ($arResult['BACKURL'] != ''):?>
                                <input type="hidden" name="backurl" value="<?=$arResult['BACKURL'];?>" />
                            <?endif;?>

                            <?foreach ($arResult['POST'] as $key => $value):?>
                                <?if (!in_array($key, ['captcha_word', 'Login', 'Login1', 'SITE_ID', 'SOLUTION_ID'])):?>
                                    <input type="hidden" name="<?=$key;?>" value="<?=$value;?>" />
                                <?endif;?>
                            <?endforeach;?>

                            <input type="hidden" name="AUTH_FORM" value="Y" />
                            <input type="hidden" name="TYPE" value="AUTH" />
                            <input type="hidden" name="POPUP_AUTH" value="<?=$arParams['POPUP_AUTH'];?>" />
                            <input type="hidden" name="AUTH_TYPE" value="<?=$authType;?>" />

                            <input type="hidden" name="SITE_ID" value="<?=SITE_ID;?>" />
                            <input type="hidden" name="SOLUTION_ID" value="<?=ASPRO_MAX_MODULE_ID;?>" />

                            <div class="form_body">
                                <?if ($arResult['ERROR']):?>
                                    <div class="alert alert-danger compact"><?=$arError['MESSAGE'];?></div>
                                <?elseif ($arResult['SHOW_SMS_FIELD']):?>
                                    <div class="alert alert-success compact"><?=GetMessage('auth_code_sent');?></div>
                                <?endif;?>

                            <?if ($bUsePhoneInput && !$arResult['SHOW_SMS_FIELD']):?>
                            <div class="tabs tabs--compact">
                                <div class="form-control">
                                    <ul class="nav nav-tabs" style="display:none">
                                        <li class="<?= $bActivePhoneTab ? 'active' : '';?>"><a href="#auth_by_phone" data-toggle="tab" data-type="phone"><?=GetMessage('AUTH_BU_PHONE_TITLE');?></a></li>
                                        <li class="<?= $bActivePhoneTab ? '' : 'active';?>"><a href="#auth_by_login" data-toggle="tab" data-type="login"><?=GetMessage('AUTH_BU_LOGIN_EMAIL_TITLE');?></a></li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane <?= $bActivePhoneTab ? 'active' : '';?>" id="auth_by_phone">
                            <?endif;?>

                            <?if ($bUsePhoneInput):?>
                                    <?if ($arResult['SHOW_SMS_FIELD']):?>
                                        <input type="hidden" name="USER_PHONE_NUMBER" value="<?=htmlspecialcharsbx($arResult['USER_PHONE_NUMBER']);?>" />
                                        <input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult['SIGNED_DATA']);?>" />
                                        <div class="form-control">
                                            <label for="SMS_CODE_POPUP"><span><?=GetMessage('auth_sms_code');?>&nbsp;<span class="required-star">*</span></span></label>
                                            <input type="text" name="SMS_CODE" id="SMS_CODE_POPUP" class="form-control" maxlength="50" value="<?=htmlspecialcharsbx($arResult['SMS_CODE']);?>" autocomplete="off" tabindex="1" required />
                                        </div>
                                    <?else:?>
                                        <div class="form-control phone_or_login">
                                            <label for="AUTH_PHONE_OR_LOGIN"class=""><span><?=GetMessage('auth_phone_number');?>&nbsp;<span class="star">*</span></span></label>
                                            <input id="AUTH_PHONE_OR_LOGIN" class="required phone" type="tel" name="USER_PHONE_NUMBER" maxlength="50" autocomplete="off" value="" tabindex="1" />
                                            <?=TSolution::showIconSvg('cabinet', SITE_TEMPLATE_PATH.'/images/svg/inputlogin.svg', '', 'colored');?>
                                            <?=TSolution::showIconSvg('cabinet', SITE_TEMPLATE_PATH.'/images/svg/inputphone.svg', '', 'colored');?>
                                        </div>
                                        <div class="form-control">
                                            <label for="USER_PASSWORD_POPUP"><span><?=GetMessage('auth_password2');?>&nbsp;<span class="required-star">*</span></span></label>
                                            <input type="password" name="USER_PASSWORD" id="USER_PASSWORD_POPUP" class="required password" maxlength="50" value="" autocomplete="off" tabindex="2"/>
                                        </div>
                                    </div><?// .tab-pane?>
                                    <div class="tab-pane <?= $bActivePhoneTab ? '' : 'active';?>" id="auth_by_login">
                                <?endif;?>
                            <?endif;?>

                            <?if (!$bUsePhoneInput || ($bUsePhoneInput && !$arResult['SHOW_SMS_FIELD'])):?>
                                <div class="form-control">
                                    <label for="USER_LOGIN_POPUP"><span><?=GetMessage('auth_login');?>&nbsp;<span class="required-star">*</span></span></label>
                                    <input type="text" name="USER_LOGIN" id="USER_LOGIN_POPUP" class="required" maxlength="50" value="<?=$arResult['USER_LOGIN'];?>" autocomplete="on" tabindex="1"/>
                                </div>
                                <div class="form-control">
                                    <label for="USER_PASSWORD_POPUP"><span><?=GetMessage('auth_password');?>&nbsp;<span class="required-star">*</span></span></label>
                                    <input type="password" name="USER_PASSWORD" id="USER_PASSWORD_POPUP" class="required password" maxlength="50" value="" autocomplete="off" tabindex="2"/>
                                </div>
                            <?endif;?>

                            <?if ($bUsePhoneInput && !$arResult['SHOW_SMS_FIELD']):?>
                                    </div> <?// .tab-pane?>
                                </div> <?// .tab-content?>
                            </div> <?// .tabs?>
                            <?endif;?>

                                <?if ($arResult['CAPTCHA_CODE']):?>
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
                                <div class="filter block">
                                    <div class="prompt remember onoff">
                                        <input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" tabindex="5" <?= isset($_REQUEST['USER_REMEMBER']) && $_REQUEST['USER_REMEMBER'] === 'Y' ? 'checked' : '';?> />
                                        <label for="USER_REMEMBER_frm" title="<?=GetMessage('AUTH_REMEMBER_ME');?>" tabindex="5"><?=GetMessage('AUTH_REMEMBER_SHORT');?></label>
                                    </div>
                                    <?if (!$arResult['SHOW_SMS_FIELD']):?>
                                        <a class="forgot" href="<?=$arResult['AUTH_FORGOT_PASSWORD_URL'];?>" tabindex="4"><?=GetMessage('AUTH_FORGOT_PASSWORD_2');?></a>
                                    <?endif;?>
                                </div>

                                <div class="buttons clearfix">
                                    <div class="line-block line-block--column line-block--align-flex-start line-block--24-vertical">
                                        <div class="line-block__item">
                                            <?$APPLICATION->IncludeFile(SITE_DIR.'include/required_message.php', [], ['MODE' => 'html']);?>
                                        </div>

                                        <div class="line-block__item width100">
                                            <?if ($arResult['PHONE_AUTH_PARAMS']['USE']):?>
                                                <?if ($arResult['SHOW_SMS_FIELD']):?>
                                                    <button class="btn btn-default btn-lg" type="submit" name="Login1" value="Y" tabindex="2"><span><?=GetMessage('AUTH_LOGIN_BUTTON');?></span></button>
                                                <?else:?>
                                                    <button class="btn btn-default btn-lg<?= $bActivePhoneTab ? '' : ' hidden';?>" data-type="phone" type="submit" name="Login1" value="Y" tabindex="2"><span><?=GetMessage('auth_get_sms_code');?></span></button>
                                                    <button class="btn btn-default btn-lg<?= $bActivePhoneTab ? ' hidden' : '';?>" data-type="login" type="submit" name="Login1" value="Y" tabindex="2"><span><?=GetMessage('AUTH_LOGIN_BUTTON');?></span></button>
                                                    <!--noindex--><a href="<?=$arResult['AUTH_REGISTER_URL'];?>" rel="nofollow" class="btn btn-transparent-border-color btn-lg pull-right register" tabindex="6"><?=GetMessage('AUTH_REGISTER_NEW');?></a><!--/noindex-->
                                                <?endif;?>
                                            <?else:?>
                                                <button class="btn btn-default btn-lg" type="submit" name="Login1" value="Y" tabindex="3"><span><?=GetMessage('AUTH_LOGIN_BUTTON');?></span></button>
                                                <!--noindex--><a href="<?=$arResult['AUTH_REGISTER_URL'];?>" rel="nofollow" class="btn btn-transparent-border-color btn-lg pull-right register" tabindex="6"><?=GetMessage('AUTH_REGISTER_NEW');?></a><!--/noindex-->
                                            <?endif;?>
                                        </div>
                                    </div>

                                    <input type="hidden" name="Login" value="Y" />
                                    <div class="clearboth"></div>
                                </div>

                                <?if ($arResult['PHONE_AUTH_PARAMS']['USE'] && !$arResult['SHOW_SMS_FIELD']):?>
                                    <div class="licence_block hidden"><label><?$APPLICATION->IncludeFile(SITE_DIR.'include/auth_phone_licenses_text.php', [], ['MODE' => 'html', 'NAME' => 'LICENSES']);?></label></div>
                                <?endif;?>

                                <?if ($arResult['SHOW_SMS_FIELD']):?>
                                    <?$rand = rand(1, 99);?>
                                    <div id="bx_auth_error<?=$rand;?>" style="display:none;"><?ShowError('error');?></div>
                                    <div id="bx_auth_resend<?=$rand;?>"></div>
                                    <script>
                                        new BX.PhoneAuth({
                                            containerId: 'bx_auth_resend<?=$rand;?>',
                                            errorContainerId: 'bx_auth_error<?=$rand;?>',
                                            interval: <?=$arResult['PHONE_CODE_RESEND_INTERVAL'];?>,
                                            data: <?=CUtil::PhpToJSObject(['signedData' => $arResult['SIGNED_DATA']]);?>,
                                            onError: function(response) {
                                                const errorDiv = BX('bx_auth_error<?=$rand;?>');
                                                const errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                                                errorNode.innerHTML = '';
                                                for (let i = 0; i < response.errors.length; i++) {
                                                    errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                                                }
                                                errorDiv.style.display = '';
                                            }
                                        });
                                    </script>
                                <?endif;?>
                            </div>
                        </form>
                        <?if ($arResult['AUTH_SERVICES']):?>
                            <div class="reg-new social_block">
                                <div class="soc-avt">
                                    <div class="title"><?=GetMessage('SOCSERV_AS_USER_FORM');?></div>
                                    <?$APPLICATION->IncludeComponent(
                                        'bitrix:socserv.auth.form',
                                        'icons',
                                        [
                                            'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
                                            'AUTH_URL' => SITE_DIR.'auth/?login=yes',
                                            'POST' => $arResult['POST'],
                                            'SUFFIX' => 'form',
                                        ],
                                        $component,
                                        ['HIDE_ICONS' => 'Y']
                                    );
                                     ?>
                                </div>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(){
                    $('form[name=bx_auth_servicesform]').validate();
                    $('.auth_wrapp .form_body a').removeAttr('onclick');

                    $('#auth-page-form').validate({
                        rules: {
                            USER_LOGIN: {
                                required: true
                            }
                        },
                        submitHandler: function(form){
                            const $form = $(form);
                            if ($form.valid()){
                                (new Promise((resolve, reject) => {
                                    if (BX.Aspro?.Captcha) {
                                        BX.Aspro.Captcha.onSubmit({form}).then((result) => {
                                            resolve(result);
                                        }).catch((e) => {
                                            reject(e);
                                        });

                                        return;
                                    }

                                    resolve(true);
                                })).then((result) => {
                                    if (result) {
                                        const $button = $form.find("button[type=submit]:visible");
                                        if ($button.length){
                                            if (!$button.hasClass("loadings")){
                                                $button.addClass("loadings");
                                                $form.closest(".form").addClass("sending");

                                                $.ajax({
                                                    type: "POST",
                                                    url: $form.attr("action"),
                                                    data: $form.serializeArray()
                                                }).done(function(html){
                                                    if ($(html).find('.auth_wrapp').length) {
                                                        $("#ajax_auth").parent().html(html);

                                                        // show password eye
                                                        $("#ajax_auth").find(".form-control:not(.eye-password-ignore) [type=password]").each(function (item) {
                                                            $(this).closest(".form-control").addClass("eye-password");
                                                        });
                                                    } else {
                                                        const match = html.match(/location\.href\s*=\s*['"]([^'"]*)['"]/);
                                                        if (match){
                                                            location.href = match[1]
                                                        } else {
                                                            BX.reload(false);
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        errorPlacement: function(error, element) {
                            $(error).attr('alt', $(error).text());
                            $(error).attr('title', $(error).text());
                            error.insertBefore(element);
                        }
                    });

                    $('form[name="system_auth_form<?=$arResult['RND'];?>"] a').on("shown.bs.tab", function (e) {
                        $(this).closest('form').find('input[name="AUTH_TYPE"]').val($(this).data("type"));
                        $(this).closest('form').find('.buttons button').addClass('hidden');
                        $(this).closest('form').find('button[data-type="'+$(this).data("type")+'"]').removeClass('hidden');
                    });
                });

                setTimeout(function(){
                    $('#auth-page-form').find('input:visible').eq(0).focus();
                }, 50);

                <?// skip Bx.ajax.insertToNode when BX ajax mode is on?>
                BX.addCustomEvent('onAjaxInsertToNode', function(e) {
                    e.eventArgs.cancel = true;
                    location.href = BX.util.remove_url_param(e.url, ['bxajaxid']);
                });
            </script>
        </div>
    <?endif;?>
<?else:?>
    <script>BX.reload(true);</script>
<?endif;?>

<?// need pageobject.js for BX.reload()?>
<script>
    BX.loadScript(['<?=Bitrix\Main\Page\Asset::getInstance()->getFullAssetPath('/bitrix/js/main/pageobject/pageobject.js');?>']);
    if (typeof appAspro === 'object' && appAspro && appAspro.phone) {
        appAspro.phone.init($('form[name="system_auth_form<?=$arResult['RND'];?>"] input.phone'), {
            coutriesData: '<?=TSolution::$arParametrsList['FORMS']['OPTIONS']['USE_INTL_PHONE']['DEPENDENT_PARAMS']['PHONE_CITIES']['TYPE_SELECT']['SRC'];?>',
            mask: arAsproOptions['THEME']['PHONE_MASK'],
            onlyCountries: '<?=TSolution::GetFrontParametrValue('PHONE_CITIES');?>',
            preferredCountries: '<?=TSolution::GetFrontParametrValue('PHONE_CITIES_FAVORITE');?>'
        });
    }
</script>

<?php
$arExtensions = ['validate'];

if ($bUsePhoneInput) {
    $arExtensions[] = 'tabs';
    $arExtensions[] = 'phone_input';
    if (TSolution::GetFrontParametrValue('USE_INTL_PHONE') === 'Y') {
        $arExtensions[] = 'intl_phone_input';
    } elseif (TSolution::GetFrontParametrValue('PHONE_MASK')){
        $arExtensions[] = 'phone_mask';
    }
}

TSolution\Extensions::initInPopup($arExtensions);
