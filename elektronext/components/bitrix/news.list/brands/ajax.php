<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest() && ($request->get("action") == "changeCountryLink" || $request->get("action") == "changeBrandFilter" || $request->get("action") == "showMoreBrands")) {
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$template = $signer->unsign($request->get("template"), "news.list");
	$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "news.list")));

	foreach($parameters as $key => $arParams) {
		if($key != "~".$key && !empty($parameters["~".$key]))
			$parameters[$key] = $parameters["~".$key];
	}
	unset($key, $arParams);

	if($parameters["CHECK_PERMISSIONS"] == true)
		$parameters["CHECK_PERMISSIONS"] = "Y";

	$requestUri = $request->get("requestUri");
	if(!empty($requestUri)) {
		$parameters["PAGER_BASE_LINK_ENABLE"] = "Y";
		$parameters["PAGER_BASE_LINK"] = $requestUri;
	}
	
	foreach($request->getPostList() as $name => $value) {
		if(preg_match('%^PAGEN_(\d+)$%', $name, $m)) {
			global $NavNum;
			$NavNum = (int)$m[1] - 1;
		}
	}
	unset($name, $value);
	
	//BRAND_FILTER//
	$charset = defined("SITE_CHARSET") && SITE_CHARSET != "" ? SITE_CHARSET : "UTF-8";
	$isUtf = strtoupper($charset) == "UTF-8";
	$normalizeBrandLetter = static function($name) use ($charset, $isUtf) {
		$name = trim((string)$name);
		if($name == "")
			return "";

		if(!$isUtf)
			$name = Bitrix\Main\Text\Encoding::convertEncoding($name, $charset, "UTF-8");

		$letter = mb_strtoupper(mb_substr($name, 0, 1, "UTF-8"), "UTF-8");
		if($letter == mb_chr(0x401, "UTF-8"))
			$letter = mb_chr(0x415, "UTF-8");

		if(preg_match("/^[0-9]$/u", $letter))
			return "0-9";

		if(preg_match("/^[A-Z]$/", $letter))
			return $letter;

		$code = mb_ord($letter, "UTF-8");
		if($code >= 0x410 && $code <= 0x42F)
			return $letter;

		return "";
	};

	$countryId = intval($request->get("countryId"));
	$letterRaw = trim((string)$request->get("letter"));
	if(!$isUtf && $letterRaw != "")
		$letterRaw = Bitrix\Main\Text\Encoding::convertEncoding($letterRaw, $charset, "UTF-8");
	if(mb_strlen($letterRaw, "UTF-8") > 3)
		$letterRaw = "";

	$letter = $letterRaw != "" ? $normalizeBrandLetter($letterRaw) : "";

	$brandFilter = array();
	if($countryId > 0)
		$brandFilter["PROPERTY_COUNTRY"] = $countryId;

	if($letter != "" && !empty($parameters["IBLOCK_ID"]) && Bitrix\Main\Loader::includeModule("iblock")) {
		$brandIds = array();
		$rsBrands = CIBlockElement::GetList(
			array(),
			array("ACTIVE" => "Y", "IBLOCK_ID" => $parameters["IBLOCK_ID"]),
			false,
			false,
			array("ID", "NAME")
		);
		while($arBrand = $rsBrands->GetNext()) {
			$brandLetter = $normalizeBrandLetter(isset($arBrand["~NAME"]) ? $arBrand["~NAME"] : $arBrand["NAME"]);
			if($brandLetter == $letter)
				$brandIds[] = (int)$arBrand["ID"];
		}
		unset($brandLetter, $arBrand, $rsBrands);

		$brandFilter["ID"] = !empty($brandIds) ? $brandIds : array(-1);
		unset($brandIds);
	}
	unset($letterRaw, $letter, $charset, $isUtf, $normalizeBrandLetter);

	if(!empty($brandFilter) && !empty($parameters["FILTER_NAME"]))
		$GLOBALS[$parameters["FILTER_NAME"]] = $brandFilter;
	unset($brandFilter, $countryId);

	if(isset($parameters["PARENT_NAME"])) {
		$parent = new CBitrixComponent();
		$parent->InitComponent($parameters["PARENT_NAME"], $parameters["PARENT_TEMPLATE_NAME"]);
		$parent->InitComponentTemplate($parameters["PARENT_TEMPLATE_PAGE"]);
	} else {
		$parent = false;
	}
	
	$APPLICATION->IncludeComponent("bitrix:news.list", $template,
		$parameters,
		$parent
	);
	
	$content = ob_get_contents();
	ob_end_clean();
	
	$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

	$webpSupport = strpos($_SERVER["HTTP_ACCEPT"], "image/webp") !== false || strpos($_SERVER["HTTP_USER_AGENT"], " Chrome/") !== false ? true : false;
	
	$GLOBALS["IMG_LAZYLOAD"] = $arSettings["IMG_LAZYLOAD"] == "Y";
	$GLOBALS["IMG_WEBP"] = $arSettings["IMG_WEBP"] == "Y" && function_exists("imagewebp") && $webpSupport;
	
	if($GLOBALS["IMG_LAZYLOAD"] || $GLOBALS["IMG_WEBP"]) {
		$content = preg_replace_callback("/<img[^>]+src=\"([^\"]+)\"/is", function($matches) {
			if($GLOBALS["IMG_LAZYLOAD"])
				$matches[0] = str_replace(" src=", " data-lazyload-src=", $matches[0]);

			if($GLOBALS["IMG_WEBP"]) {
				if(substr($matches[1], 0, 4) != "http" && substr($matches[1], 0, 2) != "//" && substr($matches[1], 0, 11) != "data:image/") {
					$pathinfo = pathinfo($matches[1]);
					if(in_array($pathinfo["extension"], array("jpg", "jpeg", "png"))) {
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

	list(, $itemsContainer) = explode("<!-- items-container -->", $content);
	list(, $showMoreContainer) = explode("<!-- show-more-container -->", $content);
	list(, $paginationContainer) = explode("<!-- pagination-container -->", $content);		
	
	if(Bitrix\Main\Loader::includeModule("iblock")) {
		Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
			"items" => $itemsContainer,
			"showMore" => $showMoreContainer,
			"pagination" => $paginationContainer,
			"imgLazyLoad" => $GLOBALS["IMG_LAZYLOAD"],
			"imgWebp" => $GLOBALS["IMG_WEBP"]
		));
	}
}