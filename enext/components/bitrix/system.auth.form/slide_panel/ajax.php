<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;
global $USER;

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest() && $request->get("action") == "login" && !$USER->IsAuthorized()) {
	$userLogin = $request->get("USER_LOGIN");
	$userPassword = $request->get("USER_PASSWORD");
	$userRemember = $request->get("USER_REMEMBER") ?: "N";
	
	//CAPTCHA//
	$captchaSid = $request->get("CAPTCHA_SID");
	if(!empty($captchaSid)) {	
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");		
		CCaptcha::Delete($captchaSid);
	}

	$arUser = $USER->Login($userLogin, $userPassword, $userRemember);
	if(isset($arUser["TYPE"]) && $arUser["TYPE"] == "ERROR") {
		$result = array(
			"status" => false,
			"message" => !empty($arUser["MESSAGE"]) ? $arUser["MESSAGE"] : false,
			"captcha_code" => !empty($captchaSid) ? $APPLICATION->CaptchaGetCode() : false
		);
	} else {
		$result = array(
			"status" => true,
			"message" => false,
			"captcha_code" => false
		);
	}
		
	echo Bitrix\Main\Web\Json::encode($result);
}