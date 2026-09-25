<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest() && $request->get("action") == "checkAlreadySubscribed") {
	$productId = $request->get("productId");

	$alreadySubscribed = false;
	if(!empty($_SESSION["SUBSCRIBE_PRODUCT"]["LIST_PRODUCT_ID"])) {
		if(array_key_exists($productId, $_SESSION["SUBSCRIBE_PRODUCT"]["LIST_PRODUCT_ID"]))
			$alreadySubscribed = true;
	}

	echo Bitrix\Main\Web\Json::encode(array("alreadySubscribed" => $alreadySubscribed));
}