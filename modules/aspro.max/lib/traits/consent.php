<?php

namespace Aspro\Max\Traits;

use Aspro\Functions\CAsproMax as SolutionFunctions;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\Json;
use CMax as Solution;

trait Consent
{
    public static function showUserConsentOrder()
    {
        $licenceHtml = $licenceRegisterHtml = $offerHtml = '';
        $bShowLicence = Solution::GetFrontParametrValue('SHOW_LICENCE') == 'Y';
        $bShowOffer = Solution::GetFrontParametrValue('SHOW_OFFER') == 'Y';

        if ($bShowLicence) {
            ob_start();
            SolutionFunctions::showBlockHtml([
                'FILE' => 'consent/userconsent.php',
                'PARAMS' => [
                    'OPTION_CODE' => 'AGREEMENT_ORDER',
                    'SUBMIT_TEXT' => GetMessage('ORDER_DEFAULT_BUTTON_CONSENT'),
                    'REPLACE_FIELDS' => [],
                    'INPUT_NAME' => 'licenses_popup',
                    'INPUT_ID' => 'licenses_order',
                    'HIDDEN_ERROR' => 'Y',
                    'SUBMIT_EVENT_NAME' => 'bx-soa-order-save-aspro',
                ],
            ]);
            $licenceHtml = trim(ob_get_clean());

            ob_start();
            SolutionFunctions::showBlockHtml([
                'FILE' => 'consent/userconsent.php',
                'PARAMS' => [
                    'OPTION_CODE' => 'AGREEMENT_REGISTRATION',
                    'SUBMIT_TEXT' => GetMessage('ORDER_DEFAULT_REGISTER_CONSENT'),
                    'REPLACE_FIELDS' => [],
                    'INPUT_NAME' => 'licenses_popup',
                    'INPUT_ID' => 'licenses_register',
                ],
            ]);
            $licenceRegisterHtml = trim(ob_get_clean());
        }

        if ($bShowOffer) {
            ob_start();
            SolutionFunctions::showBlockHtml([
                'FILE' => 'consent/public_offer.php',
                'PARAMS' => [
                    'INPUT_NAME' => 'offer_order',
                    'INPUT_ID' => 'offer_order',
                    'HIDDEN_ERROR' => 'Y',
                ],
            ]);
            $offerHtml = trim(ob_get_clean());
        }
        ?>
        <script>
            BX.message({
                'ORDER_DEFAULT_BUTTON_CONSENT': '<?=GetMessage('ORDER_DEFAULT_BUTTON_CONSENT');?>'
            });

            window.appAspro = window.appAspro || {};
            if (typeof appAspro === 'object' && appAspro && !appAspro.userConsent) {
                appAspro.userConsent = {
                    licenseRegisterBlock: <?=Json::encode($licenceRegisterHtml);?>,
                    licenceBlock: <?=Json::encode($licenceHtml);?>,
                    offerBlock: <?=Json::encode($offerHtml);?>,
                    useBitrix: <?=Solution::GetFrontParametrValue('LICENCE_TYPE') === 'BITRIX' ? 'true' : 'false';?>,
                };
            }

        </script>
        <?php
    }

    public static function getAgreementIdByOption(string $optionCode): string
    {
        $defaultOptionValue = 'DEFAULT';
        $useUniqueAgreement = Option::get(Solution::moduleID, 'AGREEMENT_USE_UNIQUE', 'N') === 'Y';

        $agreementId = $defaultOptionValue;
        if ($useUniqueAgreement) {
            $agreementId = Option::get(Solution::moduleID, $optionCode, $defaultOptionValue);
        }

        if ($agreementId === $defaultOptionValue) {
            $agreementId = Option::get(Solution::moduleID, 'AGREEMENT', '0');
        }

        return (string) $agreementId;
    }

    public static function getAgreementOptionCodeByFormSID(string $code, string $siteId = SITE_ID): string
    {
        $formCode = strtoupper(str_replace('_'.$siteId, '', $code));

        return "AGREEMENT_{$formCode}_FORM";
    }
}
