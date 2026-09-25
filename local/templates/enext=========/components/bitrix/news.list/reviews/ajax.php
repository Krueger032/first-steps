<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$context = Bitrix\Main\Application::getInstance()->getContext();

$response = $context->getResponse();
$response->addHeader("Content-Type", "application/json");

$request = $context->getRequest();
if($request->isAjaxRequest()) {
	$action = $request->get("action");
	if($action == "changeRatingItem" || $action == "showMoreReviews" || $action == "addReviewRequest") {
		$signer = new \Bitrix\Main\Security\Sign\Signer;		
		$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "news.list")));

		foreach($parameters as $key => $arParams) {
			if($key != "~".$key && !empty($parameters["~".$key]))
				$parameters[$key] = $parameters["~".$key];
		}
		unset($key, $arParams);
		
		if($parameters["CHECK_PERMISSIONS"] == true)
			$parameters["CHECK_PERMISSIONS"] = "Y";
		
		if($action == "changeRatingItem" || $action == "showMoreReviews") {
			$requestUri = $request->get("requestUri");
			if(!empty($requestUri)) {
				$parameters["PAGER_BASE_LINK_ENABLE"] = "Y";
				$parameters["PAGER_BASE_LINK"] = $requestUri;
			}
			
			$template = $signer->unsign($request->get("template"), "news.list");

			foreach($request->getPostList() as $name => $value) {
				if(preg_match('%^PAGEN_(\d+)$%', $name, $m)) {
					global $NavNum;
					$NavNum = (int)$m[1] - 1;
				}
			}
			unset($name, $value);
		
			if(!empty($parameters["GLOBAL_FILTER"]))
				$GLOBALS[$parameters["FILTER_NAME"]] = $parameters["GLOBAL_FILTER"];

			$ratingId = intval($request->get("ratingId"));
			if($ratingId > 0)
				$GLOBALS[$parameters["FILTER_NAME"]]["PROPERTY_RATING"] = $ratingId;
			
			$APPLICATION->IncludeComponent("bitrix:news.list", $template,
				$parameters,
				false
			);
			
			$content = ob_get_contents();
			ob_end_clean();

			list(, $itemsContainer) = explode("<!-- items-container -->", $content);
			list(, $showMoreContainer) = explode("<!-- show-more-container -->", $content);
			
			if(Bitrix\Main\Loader::includeModule("iblock")) {
				$response->flush(Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
					"items" => $itemsContainer,
					"showMore" => $showMoreContainer
				)));
			}
		} elseif($action == "addReviewRequest") {
			$elementId = intval($parameters["GLOBAL_FILTER"]["PROPERTY_PRODUCT_ID"]);
			$objectId = intval($parameters["GLOBAL_FILTER"]["PROPERTY_OBJECT_ID"]);
			$ratingId = intval($request->get("ratingId"));

			$APPLICATION->IncludeComponent("altop:add.review.enext", "slide_panel",
				array(
					"IBLOCK_TYPE" => $parameters["IBLOCK_TYPE"],
					"IBLOCK_ID" => $parameters["IBLOCK_ID"],
					"ELEMENT_ID" => $elementId > 0 ? $elementId : ($objectId > 0 ? $objectId : ""),
					"RATING_ID" => $ratingId > 0 ? $ratingId : "",
					"CACHE_TYPE" => $parameters["CACHE_TYPE"],
					"CACHE_TIME" => $parameters["CACHE_TIME"]
				),
				false
			);
			
			$content = ob_get_contents();
			ob_end_clean();
			
			if(Bitrix\Main\Loader::includeModule("iblock")) {
				Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
					"content" => $content
				));
			}
		}
	} elseif($action == "checkLiked" || $action == "addLike" || $action == "deleteLike") {
		$iblockId = intval($request->get("iblockId"));
		$reviewId = intval($request->get("reviewId"));
		if($iblockId > 0 && $reviewId > 0 && Bitrix\Main\Loader::includeModule("iblock")) {
			$cookieReviews = unserialize(base64_decode(strtr($request->getCookie("ENEXT_REVIEWS"), "-_,", "+/=")));			
			
			$isLiked = false;
			if(!empty($cookieReviews) && array_key_exists($reviewId, $cookieReviews[$iblockId]))
				$isLiked = true;
			
			if($action == "checkLiked") {
				$response->flush(Bitrix\Main\Web\Json::encode(array(
					"liked" => $isLiked
				)));
			} elseif($action == "addLike" || $action == "deleteLike") {
				if(($action == "addLike" && !$isLiked) || ($action == "deleteLike" && $isLiked)) {
					$rsProp = CIBlockElement::GetProperty($iblockId, $reviewId, array(), array("CODE" => "LIKES"));			
					if($arProp = $rsProp->Fetch()) {
						CIBlockElement::SetPropertyValuesEx($reviewId, $iblockId, array(
							$arProp["CODE"] => array(
								"VALUE" => intval($arProp["VALUE"]) + ($action == "addLike" ? 1 : -1)
							)
						));
						CIBlock::clearIblockTagCache($iblockId);
					}

					if($action == "addLike") {
						$cookieVal[$iblockId][$reviewId] = true;
						if(!empty($cookieReviews)) {
							foreach($cookieReviews as $ibId => $elements) {
								foreach($elements as $elId => $value) {
									$cookieVal[$ibId][$elId] = $value;
								}
								unset($elId, $value);
							}
							unset($ibId, $elements);
						}
					} else {
						$cookieVal = $cookieReviews;
						unset($cookieVal[$iblockId][$reviewId]);
					}

					$siteServerName = $request->get("siteServerName") ?: SITE_SERVER_NAME;
					
					$cookie = new Bitrix\Main\Web\Cookie("ENEXT_REVIEWS", strtr(base64_encode(serialize($cookieVal)), "+/=", "-_,"), time() + 32832000);
					$cookie->setDomain($siteServerName);
					$cookie->setHttpOnly(false);
					$response->addCookie($cookie);
				}

				$response->flush(Bitrix\Main\Web\Json::encode(array(
					"liked" => $action == "addLike" ? true : false,
					"likes" => $action == "addLike" && !$isLiked ? "plus" : ($action == "deleteLike" && $isLiked ? "minus" : false)
				)));
			}
		}
	}
}