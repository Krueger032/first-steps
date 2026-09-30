<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId))
	define("SITE_ID", $siteId);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Bitrix\Main\Security\Sign\Signer;

$request = Application::getInstance()->getContext()->getRequest();
if(!$request->isAjaxRequest() || $request->get("action") != "changeEmptySection")
	die();

$sectionId = intval($request->get("sectionId"));
$parameters = array();
$parentId = 0;
$sections = array();

try {
	$signer = new Signer;
	$signed = $signer->unsign($request->get("signed"), "catalog.empty.section");
	$payload = unserialize(base64_decode($signed), array("allowed_classes" => false));
	if(is_array($payload)) {
		if(isset($payload["params"]) && is_array($payload["params"]))
			$parameters = $payload["params"];
		$parentId = intval($payload["parentId"]);
		if(isset($payload["sections"]) && is_array($payload["sections"]))
			$sections = $payload["sections"];
	}
	unset($signed, $payload, $signer);
} catch(Exception $e) {
	$parameters = array();
}

$allowed = false;
foreach($sections as $allowedSectionId) {
	if(intval($allowedSectionId) === $sectionId) {
		$allowed = true;
		break;
	}
}
unset($allowedSectionId);

if(empty($parameters) || !$allowed || ($sectionId === 0 && $parentId <= 0)) {
	if(Loader::includeModule("iblock"))
		Bitrix\Iblock\Component\Base::sendJsonAnswer(array("content" => ""));
	die();
}

if($sectionId > 0)
	$parameters["SECTION_ID"] = $sectionId;
else
	$parameters["SECTION_ID"] = $parentId;
$parameters["SECTION_CODE"] = "";
$parameters["SET_TITLE"] = "N";
$parameters["SET_BROWSER_TITLE"] = "N";
$parameters["SET_META_KEYWORDS"] = "N";
$parameters["SET_META_DESCRIPTION"] = "N";
$parameters["BROWSER_TITLE"] = "-";
$parameters["META_KEYWORDS"] = "-";
$parameters["META_DESCRIPTION"] = "-";
$parameters["SET_STATUS_404"] = "N";
$parameters["SHOW_404"] = "N";
$parameters["ADD_SECTIONS_CHAIN"] = "N";
$parameters["SEF_RULE"] = "";
$parameters["SMART_FILTER_PATH"] = "";
$parameters["DISPLAY_TOP_PAGER"] = "N";
$parameters["DISPLAY_BOTTOM_PAGER"] = "N";
$parameters["PAGER_SHOW_ALWAYS"] = "N";
$parameters["LAZY_LOAD"] = "Y";
$parameters["POPUP_MODE"] = "Y";
$parameters["FILTER_NAME"] = "arCatalogEmptyParentFilter";
$GLOBALS["arCatalogEmptyParentFilter"] = array();

ob_start();
$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", $parameters, false);
$content = ob_get_contents();
ob_end_clean();

$arSettings = array();
if(Loader::includeModule("altop.elektronext"))
	$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

$webpSupport = strpos($_SERVER["HTTP_ACCEPT"], "image/webp") !== false || strpos($_SERVER["HTTP_USER_AGENT"], " Chrome/") !== false ? true : false;
$GLOBALS["IMG_LAZYLOAD"] = (!empty($arSettings["IMG_LAZYLOAD"]) && $arSettings["IMG_LAZYLOAD"] == "Y");
$GLOBALS["IMG_WEBP"] = (!empty($arSettings["IMG_WEBP"]) && $arSettings["IMG_WEBP"] == "Y" && function_exists("imagewebp") && $webpSupport);

if($GLOBALS["IMG_LAZYLOAD"] || $GLOBALS["IMG_WEBP"]) {
	$content = preg_replace_callback("/<img[^>]+src=\"([^\"]+)\"/is", function($matches) {
		if($GLOBALS["IMG_LAZYLOAD"])
			$matches[0] = str_replace(" src=", " data-lazyload-src=", $matches[0]);

		if($GLOBALS["IMG_WEBP"]) {
			if(substr($matches[1], 0, 4) != "http" && substr($matches[1], 0, 2) != "//" && substr($matches[1], 0, 11) != "data:image/") {
				$pathinfo = pathinfo($matches[1]);
				if(!empty($pathinfo["extension"]) && in_array(strtolower($pathinfo["extension"]), array("jpg", "jpeg", "png"))) {
					$newFile = $_SERVER["DOCUMENT_ROOT"].$pathinfo["dirname"]."/".$pathinfo["filename"].".webp";
					if(file_exists($newFile)) {
						$newSrc = $pathinfo["dirname"]."/".$pathinfo["filename"].".webp?".filemtime($newFile);
						$matches[0] = str_replace($matches[1], $newSrc, $matches[0]);
					}
					unset($newSrc, $newFile);
				}
				unset($pathinfo);
			}
		}

		return $matches[0];
	}, $content);
}

$js = "";
$asset = Bitrix\Main\Page\Asset::getInstance();
if(is_object($asset) && method_exists($asset, "getJs"))
	$js = $asset->getJs();

if(Loader::includeModule("iblock")) {
	Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
		"content" => $content,
		"JS" => $js,
		"imgLazyLoad" => $GLOBALS["IMG_LAZYLOAD"],
		"imgWebp" => $GLOBALS["IMG_WEBP"]
	));
}

die();
