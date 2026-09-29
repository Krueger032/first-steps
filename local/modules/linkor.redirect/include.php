<?
/*******************************************************************************
 * linkor.redirect - SEO redirects module
 * Copyright 2020 Moroz Vadim
 * MIT License
 ******************************************************************************/

namespace Linkor\Redirect;

use Bitrix\Main\Localization\Loc;

const ID = "linkor.redirect";
const APP = __DIR__ . "/";
const LIB = APP  . "lib/";

$docRoot = '';
$rsSites = \CSite::GetList($by="sort", $order="desc");
while ($arSite = $rsSites->Fetch())
{
    $docRoot = (SITE_ID === $arSite['LID']) ? $arSite['DOC_ROOT'] : $_SERVER['DOCUMENT_ROOT'];
    if(!$arSite['DOC_ROOT']){
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
    }
}

define(__NAMESPACE__ . "\CONFIG", $docRoot . "/local/config/" . ID . "/");


const FILE_OPTIONS = CONFIG . "options.php";
const FILE_REDIRECTS = CONFIG . "urls.csv";
const FILE_REDIRECTS_UPLOAD = CONFIG . "upload_urls.csv";
const FILE_REDIRECTS_CACHE = CONFIG . "urls.php";

require LIB . "StorageClass.php";
require LIB . "RedirectClass.php";
require LIB . "HelperClass.php";

// Функция используется для вывода редиректов в списке (во 2 вкладке), дабы не было таймаута
function AppendValues($data, $n, $v) {
    yield from $data;
    for ($i = 0; $i < $n; $i++) {
        yield  $v;
    }
}

// Читаем из файла настройки options.php
function Options($filePath = FILE_OPTIONS) {

    return is_readable($filePath)?
        include $filePath : [
            'redirect_www' => 'Y',
            'redirect_slash' => 'Y',
            'redirect_index_php' => 'Y',
            'redirect_index_html' => 'Y',
            'redirect_multislash' => 'Y',
            'use_redirect_urls' => 'N',
            'redirect_from_uppercase' => 'Y',
            'redirect_from_404' => 'N'
        ];
}

function CheckDirectoryExist($configPath = CONFIG){
    $rsSites = \CSite::GetList($by="sort", $order="desc");
    while ($arSite = $rsSites->Fetch())
    {
        $docRoot = (!$arSite['DOC_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : $arSite['DOC_ROOT'];

        @mkdir($docRoot . "/local/", BX_DIR_PERMISSIONS);
        @mkdir($docRoot . "/local/config/", BX_DIR_PERMISSIONS);
        @mkdir($docRoot . "/local/config/" . ID, BX_DIR_PERMISSIONS);
    }
}

function GetDocRootBySiteName($site_name){
    $rsSites = \CSite::GetByID($site_name);
    $arSite = $rsSites->Fetch();
    return (!empty($arSite['DOC_ROOT'])) ? $arSite['DOC_ROOT'] : $_SERVER['DOCUMENT_ROOT'];
}
function GetDomainBySiteName($site_name){
    $rsSites = \CSite::GetByID($site_name);
    $arSite = $rsSites->Fetch();
    return (!empty($arSite['SERVER_NAME'])) ? $arSite['SERVER_NAME'] : '';
}

// Обновляем настройки options.php и создаем конфиги, если папки нету
function OptionsUpdate($data, $siteId = 's1') {
    $docRoot = GetDocRootBySiteName($siteId);
    CheckDirectoryExist($docRoot."/local/config/".ID."/");

    Storage::PHPWrite($docRoot . "/local/config/" . ID . "/options.php", [
        "redirect_www" => $data[$siteId . "_redirect_www"],
        "redirect_slash" => $data[$siteId . "_redirect_slash"],
        "redirect_index_php" => $data[$siteId . "_redirect_index_php"],
        "redirect_index_html" => $data[$siteId . "_redirect_index_html"],
        "redirect_multislash" => $data[$siteId . "_redirect_multislash"],
        "use_redirect_urls" => $data[$siteId . "_use_redirect_urls"],
        "redirect_from_uppercase" => $data[$siteId . "_redirect_from_uppercase"],
        "redirect_from_404" => $data[$siteId . "_redirect_from_404"]
    ]);

}

// Загрузка прикрепленного файла с редиректами
function CSVFileUpload($file, $siteId = 's1') {
    CheckDirectoryExist();

    $is_correct = true;

    $docRoot = GetDocRootBySiteName($siteId);
    $filePathUpload = $docRoot."/local/config/".ID."/upload_urls.csv";

    if(substr($file[$siteId.'_use_redirect_file']['name'], -3, 3) === 'csv'){
        if (!move_uploaded_file($file[$siteId.'_use_redirect_file']['tmp_name'], $filePathUpload)) {
            $message = new \CAdminMessage(['MESSAGE' => Loc::getMessage("LINKOR_REDIRECT_FILE_LOAD_ERROR"), 'TYPE' => 'ERROR']);
            echo $message->Show();
            $is_correct = false;
        }
    }else{
        $message = new \CAdminMessage(['MESSAGE' => Loc::getMessage("LINKOR_REDIRECT_FILE_FORMAT_ERROR"), 'TYPE' => 'ERROR']);
        echo $message->Show();
        $is_correct = false;
    }
    return $is_correct;
}

// Удаление прикрепленного файла с редиректами
function CSVFileDelete($siteId = 's1') {
    CheckDirectoryExist();
    $is_correct = true;
    $docRoot = GetDocRootBySiteName($siteId);
    $filePathUpload = $docRoot."/local/config/".ID."/upload_urls.csv";

    if(file_exists($filePathUpload)){
        if (!unlink($filePathUpload)) {
            $message = new \CAdminMessage(['MESSAGE' => Loc::getMessage("LINKOR_REDIRECT_FILE_DELETE_ERROR"), 'TYPE' => 'ERROR']);
            echo $message->Show();
            $is_correct = false;
        }
    }else{
        $message = new \CAdminMessage(['MESSAGE' => Loc::getMessage("LINKOR_REDIRECT_FILE_EXIST_ERROR"), 'TYPE' => 'ERROR']);
        echo $message->Show();
        $is_correct = false;
    }

    return $is_correct;
}


// Выводим из CSV списка редиректов
function Select($fromCsv = false,
                $filePathUpload = FILE_REDIRECTS_UPLOAD,
                $filePath = FILE_REDIRECTS,
                $fileCache = FILE_REDIRECTS_CACHE) {
    if ($fromCsv) {
        return Storage::CSVRead(file_exists($filePathUpload) ? $filePathUpload : $filePath);
    }
    return is_readable($fileCache) ? include $fileCache : [];
}

// Добавляем в CSV список редиректов
function Update($data, $siteId = 's1') {
    $docRoot = GetDocRootBySiteName($siteId);
    $configPath = $docRoot."/local/config/".ID."/";
    $filePathUpload = $docRoot."/local/config/".ID."/upload_urls.csv";
    $filePath = $docRoot."/local/config/".ID."/urls.csv";
    $fileCache = $docRoot."/local/config/".ID."/urls.php";

    $urls = [];
    $urlsMap = [];
    foreach ($data[$siteId."_redirect_urls"] as $url) {
        $from = Helper::encodeCyrillicOnly(trim($url[0]));
        $to = Helper::encodeCyrillicOnly(trim($url[1]));
        if ($from != "" && $to != "") {
            $urls[] = $url;
            $urlsMap[$from] = [$to, trim($url[2]), trim($url[3])];
        }
    }
    if (!is_dir($configPath)) {
        @mkdir($configPath, 0777, true);
    }
    Storage::CSVWrite(file_exists($filePathUpload) ? $filePathUpload : $filePath, $urls);
    //print_r($urlsMap);
    Storage::PHPWrite($fileCache, $urlsMap);
}

// Основная функция
function HandlerRedirectUrl() {
    if ($_SERVER["REQUEST_METHOD"] != "GET" && $_SERVER["REQUEST_METHOD"] != "HEAD") {
        return;
    }
    // ignore scripts from /bitrix/, cli scripts and cron scripts
    if (php_sapi_name() == "cli" || defined("BX_CRONTAB") || \CSite::InDir("/bitrix/")) {
        return;
    }

    $currentOptions = Options();
    $redirects = Select();

    (new Redirect($currentOptions, $redirects));
}

function OnEpilog() {

    // запускаем сначала основные редиректы, а после уже 404

    if (!defined("ERROR_404") || ERROR_404 != "Y") {
        return;
    }
    global $APPLICATION;
    // get parent level url
    $uri = parse_url($APPLICATION->GetCurPage(false), PHP_URL_PATH);
    $segments = explode("/", trim($uri, "/"));
    array_pop($segments);
    if (count($segments) > 0) {
        $uri = "/" . implode("/", $segments) . "/";
    } else {
        $uri = "/";
    }
    // redirect
    LocalRedirect($uri, false, "301 Moved Permanently");
    exit;
}

// Добавление обработчика
function init() {
    Loc::loadMessages(__FILE__);

    AddEventHandler(
        "main",
        "OnBeforeProlog",
        __NAMESPACE__ . "\\HandlerRedirectUrl"
    );
    $currentOptions = Options();
    if ($currentOptions["redirect_from_404"] == "Y") {
        AddEventHandler(
            "main",
            "OnEpilog",
            __NAMESPACE__ . "\\OnEpilog"
        );
    }
}

// Точка входа
init();
