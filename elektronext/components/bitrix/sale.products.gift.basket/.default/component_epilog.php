<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//CURRENCIES//
if(!empty($templateData["TEMPLATE_LIBRARY"])) {
	$loadCurrency = false;
	if(!empty($templateData["CURRENCIES"])) {
		$loadCurrency = \Bitrix\Main\Loader::includeModule("currency");
	}

	CJSCore::Init($templateData["TEMPLATE_LIBRARY"]);

	if($loadCurrency) {?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
		</script>
	<?}
}

//LAZY_LOAD//
$request = Bitrix\Main\Context::getCurrent()->getRequest();
if($request->isAjaxRequest() && $request->get("action") == "deferredLoad") {
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
	
	$component::sendJsonAnswer(array(
		"items" => $itemsContainer,
		"imgLazyLoad" => $GLOBALS["IMG_LAZYLOAD"],
		"imgWebp" => $GLOBALS["IMG_WEBP"]
	));
}