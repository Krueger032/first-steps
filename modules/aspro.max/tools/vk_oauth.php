<?php

use Aspro\Max\Social\OAuth\VKID;
use Bitrix\Main\Localization\Loc;
use CMax as Solution;

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_popup_admin.php';

global $APPLICATION;
IncludeModuleLangFile(__FILE__);

$application = Bitrix\Main\Application::getInstance();
$context = $application->getContext();
$request = $context->getRequest();
$session = $application->getSession();

$APPLICATION->AddHeadString('<base href="/bitrix/admin/">', true);
$solutionCode = strtoupper(Solution::solutionName);

// check right
$RIGHT = $APPLICATION->GetGroupRight($moduleID);
if ($RIGHT < 'R') {
    echo CAdminMessage::ShowMessage(Loc::getMessage("ASPRO_{$solutionCode}_ACCESS_DENIED") ?: "ASPRO_{$solutionCode}_ACCESS_DENIED");
    exit;
}

try {
    $siteId = trim(strip_tags(htmlspecialcharsbx($request['site_id'])));

    // check site id
    if (!$siteId) {
        throw new Exception(Loc::getMessage("ASPRO_{$solutionCode}_NO_SITE_ID") ?: "ASPRO_{$solutionCode}_NO_SITE_ID");
    }

    $vkID = new VKID($siteId);

    $code = trim(strip_tags(htmlspecialcharsbx($request['code'])));
    if (!$code) {
        $vkID->authorize();
    }

    if (!$vkID->checkState()) {
        throw new Exception('Invalid state parameter');
    }

    $vkID->setAccessTokenWithAuthorizationCode($code, $siteId);
} catch (Throwable $th) {
    echo \CAdminMessage::ShowMessage([
        'MESSAGE' => $th->getMessage(),
        'TYPE' => 'ERROR',
        'DETAILS' => $th->getTraceAsString(),
        'HTML' => true,
    ]);
}
