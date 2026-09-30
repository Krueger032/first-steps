<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest() && $request->get("action") == "workingHoursToday") {			
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