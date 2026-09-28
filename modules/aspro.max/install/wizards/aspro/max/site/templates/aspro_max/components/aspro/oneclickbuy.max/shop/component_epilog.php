<?php

use Bitrix\Main\Page\Frame;

if (!include_once($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new \Exception('Error include solution constants');
}

global $USER;

$arJSExtensions = ['validate', 'phone_input'];
if (TSolution::GetFrontParametrValue('USE_INTL_PHONE') === 'Y') {
    $arJSExtensions[] = 'intl_phone_input';
} elseif (TSolution::GetFrontParametrValue('PHONE_MASK')) {
    $arJSExtensions[] = 'phone_mask';
}

Frame::getInstance()->startDynamicWithID('form-block-one-click-buy');
?>
<script>
    (() => {
        <?if ($USER->IsAuthorized()):?>
            <?php
            $dbRes = CUser::GetList($by = 'id', $order = 'asc', ['ID' => $USER->GetID()], ['FIELDS' => ['ID', 'PERSONAL_PHONE']]);
            $arUser = $dbRes->Fetch();

            $fio = $USER->GetFullName();
            $phone = $arUser['PERSONAL_PHONE'];
            $email = $USER->GetEmail();
            ?>
            $('#one_click_buy_id_FIO').val(BX.Text.decode('<?=TSolution::formatJsNameEx($fio);?>'));
            $('#one_click_buy_id_PHONE').val(BX.Text.decode('<?=TSolution::formatJsNameEx($phone);?>'));
            $('#one_click_buy_id_EMAIL').val(BX.Text.decode('<?=TSolution::formatJsNameEx($email);?>'));
        <?endif;?>

        BX.Aspro.Loader.addExt('<?=implode("', '", $arJSExtensions);?>').then(() => {
            if (typeof appAspro === 'object' && appAspro && appAspro.phone && !appAspro.phone.config) {
                appAspro.phone.init($('#one_click_buy_id_PHONE'), {
                    coutriesData: '<?=TSolution::$arParametrsList['FORMS']['OPTIONS']['USE_INTL_PHONE']['DEPENDENT_PARAMS']['PHONE_CITIES']['TYPE_SELECT']['SRC'];?>',
                });
            }

            $('#one_click_buy_id_PHONE').trigger('change');
        });
    })()
</script>
<?Frame::getInstance()->finishDynamicWithID('form-block-one-click-buy', '');?>
