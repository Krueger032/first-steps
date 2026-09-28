<?php
use Bitrix\Main\SystemException;

if (!include_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new SystemException('Error include solution constants');
}
$GLOBALS['APPLICATION']->ShowAjaxHead();

global $arTheme;
$licenceChecked = (CMax::GetFrontParametrValue('LICENCE_CHECKED') == 'Y' ? 'checked' : '');
$subscribePage = CMax::GetFrontParametrValue('SUBSCRIBE_PAGE_URL');
$showLicence = CMax::GetFrontParametrValue('SHOW_LICENCE');
?>
<?if (!$GLOBALS['bMobileForm']):?>
    <a href="#" class="close jqmClose"><?=CMax::showIconSvg('', SITE_TEMPLATE_PATH.'/images/svg/Close.svg');?></a>
<?endif;?>

<div class="form subscribe <?=$GLOBALS['bMobileForm'] ? 'mobile' : '';?>">
    <div class="form_head">
        <h2><?=GetMessage('SUBSCRIBE_TITLE');?></h2>
        <div class="error_message error_message--mt-20 hidden"></div>
    </div>
    <div class="js_form">
        <form name="short_subscribe" action="<?=$APPLICATION->GetCurPage();?>" method="post" enctype="multipart/form-data" novalidate="novalidate">
            <?=bitrix_sessid_post();?>
            <input type="hidden" name="type" value="subscribe">
            <input type="hidden" name="note" value="Y">

            <div class="form_body">
                <div class="row" data-sid="SUBSCRIBE">
                    <div class="col-md-12">
                        <div class="form-control form-group animated-labels">
                            <label for="POPUP_EMAIL"><span><?=GetMessage('EMAIL');?>&nbsp;<span class="required-star">*</span></span></label>
                            <div class="input">
                                <input type="email" id="POPUP_EMAIL" class="form-control inputtext" data-sid="EMAIL" required name="EMAIL" value="" aria-required="true">
                            </div>
                        </div>
                    </div>
                    <?if (TSolution::GetFrontParametrValue('CAPTCHA_ON_SUBSCRIBE') === 'Y'):?>
                        <div class="col-md-12">
                            <?$arResult['CAPTCHACode'] = $GLOBALS['APPLICATION']->CaptchaGetCode();?>
                            <div class="form-control captcha-row clearfix fill-animate">
                                <label class="font_13 color_999"><span><?=GetMessage('CAPTCHA_FORM_TITLE');?>&nbsp;<span class="required-star">*</span></span></label>
                                <div class="captcha_image">
                                    <img data-src="" src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult['CAPTCHACode']);?>" class="captcha_img">
                                    <input type="hidden" name="captcha_sid" class="captcha_sid" value="<?=htmlspecialcharsbx($arResult['CAPTCHACode']);?>">
                                    <div class="captcha_reload"></div>
                                    <span class="refresh"><a href="javascript:;" rel="nofollow"><?=GetMessage('BITRIX_CAPTCHA_REFRESH_TITLE');?></a></span>
                                </div>
                                <div class="captcha_input">
                                    <input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required>
                                </div>
                            </div>
                        </div>
                    <?endif;?>
                </div>
            </div>

            <div class="form_footer">
                <?if($showLicence == 'Y'):?>
                    <?
                    TSolution\Functions::showBlockHtml([
                        'FILE' => 'consent/userconsent.php',
                        'PARAMS' => [
                            'OPTION_CODE' => 'AGREEMENT_SUBSCRIBE',
                            'SUBMIT_TEXT' => GetMessage("SUBSCRIBE_PAGE"),
                            'REPLACE_FIELDS' => [],
                            'INPUT_NAME' => "licenses_subscribe",
                            'INPUT_ID' => 'licenses_subscribe',
                        ]
                    ]);
                    ?>
                <?endif;?>

                <div class="line-block line-block--column line-block--align-flex-start line-block--24-vertical">
                    <div class="line-block__item">
                        <?$APPLICATION->IncludeFile(SITE_DIR.'include/required_message.php', [], ['MODE' => 'html']);?>
                    </div>

                    <div class="line-block__item width100">
                        <div class="buttons clearfix">
                            <button class="btn btn-default btn-lg pull-left" type="submit" value="<?=GetMessage('SUBSCRIBE_PAGE');?>" name="web_form_submit">
                                <?=GetMessage('SUBSCRIBE_PAGE');?>
                            </button>
                            <a class="settings font_upper pull-right dark-color" href="<?=$subscribePage;?>"><?=CMax::showIconSvg('', SITE_TEMPLATE_PATH.'/images/svg/gear.svg');?><?=GetMessage('SETTINGS');?></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
BX.Aspro.Utils.readyDOM(() => {
  BX.Aspro.Loader.addExt("validate").then(() => {
    $('form[name="short_subscribe"]').validate({
      submitHandler: (form) => {
        if ($('form[name="short_subscribe"]').valid()) {
          const validateInputName = arAsproOptions.VALIDATION?.FORM_INPUT_NAME;
          BX.Aspro?.Validation?.appendSolutionField?.(form);

          if($(form).find("input.error").length || $(form).find("textarea.error").length){
            return false;
          } else {
            new Promise((resolve, reject) => {
              if (BX.Aspro?.Captcha) {
                if (arAsproOptions.THEME.CAPTCHA_ON_SUBSCRIBE === "Y") {
                  BX.Aspro.Captcha.onSubmit({ form })
                    .then((result) => {
                      resolve(result);
                    })
                    .catch((e) => {
                      reject(e);
                    });

                  return;
                }
              }
              resolve(true);
            }).then((result) => {
                const $buttonSubmit = $(form).find('button[type="submit"]');
                $buttonSubmit.prop("disabled", true);

              $.ajax({
                url: arMaxOptions["SITE_DIR"] + "ajax/subscribe_user.php",
                data: new FormData(form),
                processData: false,
                contentType: false,
                type: "POST",
                success: (html) => {
                     const hasError = $(html).hasClass('errortext');
                    if (hasError) {
                        const $errorNode = $('.form.subscribe').find('.error_message')
                        $errorNode.removeClass('hidden').html('').html(html);
                        $buttonSubmit.prop("disabled", false);
                    }else{
                        $(".form .js_form").html(html);
                    }
                    $('.form.subscribe').removeClass('sending');
                },
              });
            });
          }
        }
      },
    });
  });
});
</script>
