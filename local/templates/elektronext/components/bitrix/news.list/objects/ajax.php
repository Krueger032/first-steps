<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest()) {
	if($request->get("action") == "showMoreObjects") {
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
			if(preg_match("%^PAGEN_(\d+)$%", $name, $m)) {
				global $NavNum;
				$NavNum = (int)$m[1] - 1;
			}
		}
		unset($name, $value);
		
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
		list(, $paginationContainer) = explode("<!-- pagination-container -->", $content);		
		
		if(Bitrix\Main\Loader::includeModule("iblock")) {
			Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
				"items" => $itemsContainer,			
				"pagination" => $paginationContainer,
				"imgLazyLoad" => $GLOBALS["IMG_LAZYLOAD"],
				"imgWebp" => $GLOBALS["IMG_WEBP"]
			));
		}
	} elseif($request->get("action") == "workingHoursToday") {
		$timezone = $request->get("timezone");
		if(!empty($timezone))
			$currentDateTime = strtotime(gmdate("Y-m-d H:i", strtotime($timezone." hours")));
		else
			$currentDateTime = time() + CTimeZone::GetOffset();	
		
		$workingHours = $request->get("workingHours");
		$siteCharset = $request->get("siteCharset") ?: SITE_CHARSET;
		if(!empty($workingHours) && $siteCharset != "utf-8")
			$workingHours = Bitrix\Main\Text\Encoding::convertEncoding($workingHours, "utf-8", $siteCharset);
		
		if(!empty($currentDateTime) && !empty($workingHours)) {
			$currentDay = strtoupper(date("D", $currentDateTime));
			$arCurDay = $workingHours[$currentDay];
			if(!empty($arCurDay)) {			
				$arWorkingHoursToday[$currentDay] = array(
					"WORK_START" => strtotime($arCurDay["WORK_START"]) ? $arCurDay["WORK_START"] : "",
					"WORK_END" => strtotime($arCurDay["WORK_END"]) ? $arCurDay["WORK_END"] : "",
					"BREAK_START" => strtotime($arCurDay["BREAK_START"]) ? $arCurDay["BREAK_START"] : "",
					"BREAK_END" => strtotime($arCurDay["BREAK_END"]) ? $arCurDay["BREAK_END"] : ""
				);
				
				$currentDate = date("Y-m-d", $currentDateTime);
					
				$workStart = strtotime($arCurDay["WORK_START"]);
				$workStartDateTime = strtotime($currentDate." ".$arCurDay["WORK_START"]);
				$workEnd = strtotime($arCurDay["WORK_END"]);
					
				$breakStart = strtotime($arCurDay["BREAK_START"]);
				$breakStartDateTime = strtotime($currentDate." ".$arCurDay["BREAK_START"]);
				$breakEnd = strtotime($arCurDay["BREAK_END"]);

				if($workStart && $workEnd) {
					if($workStart < $workEnd) {				
						$workEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]);
						$prevDayWorkEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]." -1 days");

						$breakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]);
						$prevDayBreakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]." -1 days");
					} elseif($workStart > $workEnd) {				
						$workEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]." +1 days");
						$prevDayWorkEndDateTime = strtotime($currentDate." ".$arCurDay["WORK_END"]);

						$breakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]." +1 days");
						$prevDayBreakEndDateTime = strtotime($currentDate." ".$arCurDay["BREAK_END"]);
					} else {
						$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";
					}
				} else {
					$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
				}

				if(!$arWorkingHoursToday[$currentDay]["STATUS"]) {
					if($workStartDateTime && $workEndDateTime) {
						if($currentDateTime >= $workStartDateTime && $currentDateTime < $workEndDateTime) {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";					
							if($breakStartDateTime && $breakEndDateTime)
								if($currentDateTime >= $breakStartDateTime && $currentDateTime < $breakEndDateTime)
									$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";					
						} elseif($currentDateTime < $workStartDateTime && $currentDateTime < $prevDayWorkEndDateTime) {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "OPEN";
							if($breakStartDateTime && $breakEndDateTime)
								if($currentDateTime < $breakStartDateTime && $currentDateTime < $prevDayBreakEndDateTime)
									$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
						} else {
							$arWorkingHoursToday[$currentDay]["STATUS"] = "CLOSED";
						}
					}
				}
			}
			unset($arCurDay, $currentDay);
		}
		unset($currentDateTime);

		echo Bitrix\Main\Web\Json::encode(array(
			"today" => !empty($arWorkingHoursToday) ? $arWorkingHoursToday : false
		));
	}
}