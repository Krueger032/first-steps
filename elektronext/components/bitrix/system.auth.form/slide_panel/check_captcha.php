<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$resp = CEnext::CheckCaptchaCode($_POST["CAPTCHA_WORD"], $_POST["CAPTCHA_SID"]);

echo json_encode(
	array(
		"valid" => $resp
	)
);