<?php

use Bitrix\Main\Page\Frame;

if (!include_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new \Exception('Error include solution constants');
}

global $USER;

Frame::getInstance()->startDynamicWithID('form-block'.$arParams['WEB_FORM_ID']);
?>
<script>
    BX.Aspro.Utils.readyDOM(() => {
        <?if ($USER->IsAuthorized()):?>
            <?php
            $dbRes = CUser::GetList($by = 'id', $order = 'asc', ['ID' => $USER->GetID()], ['FIELDS' => ['ID', 'PERSONAL_PHONE']]);
            $arUser = $dbRes->Fetch();

            $fio = $USER->GetFullName();
            $phone = $arUser['PERSONAL_PHONE'];
            $email = $USER->GetEmail();
            ?>
            try {
                $('.form.<?=$arResult['arForm']['SID'];?> input[data-sid=CLIENT_NAME], .form.<?=$arResult['arForm']['SID'];?> input[data-sid=FIO], .form.<?=$arResult['arForm']['SID'];?> input[data-sid=NAME]').val(BX.Text.decode('<?=TSolution::formatJsNameEx($fio);?>'));
                $('.form.<?=$arResult['arForm']['SID'];?> input[data-sid=PHONE]').val(BX.Text.decode('<?=TSolution::formatJsNameEx($phone);?>'));
                $('.form.<?=$arResult['arForm']['SID'];?> input[data-sid=EMAIL]').val(BX.Text.decode('<?=TSolution::formatJsNameEx($email);?>'));
                $('.form.<?=$arResult['arForm']['SID'];?> input[data-sid=PHONE]').trigger('change');
            } catch(e) {

            }
        <?endif;?>

        if (typeof appAspro === 'object' && appAspro && appAspro.phone && !appAspro.phone.config) {
            appAspro.phone.init($('form[name=<?=$arResult['arForm']['VARNAME'];?>] input.phone'), {
                coutriesData: '<?=TSolution::$arParametrsList['FORMS']['OPTIONS']['USE_INTL_PHONE']['DEPENDENT_PARAMS']['PHONE_CITIES']['TYPE_SELECT']['SRC'];?>',
            });
        }

        $('.form.<?=$arResult['arForm']['SID'];?> input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
    });
</script>
<?Frame::getInstance()->finishDynamicWithID('form-block'.$arParams['WEB_FORM_ID'], '');?>
<?php
$arExtensions = ['validate', 'jquery.uniform', 'phone_input', 'phone_mask'];
if (TSolution::GetFrontParametrValue('USE_INTL_PHONE') === 'Y') {
    $arExtensions[] = 'intl_phone_input';
}

TSolution\Extensions::init($arExtensions);
