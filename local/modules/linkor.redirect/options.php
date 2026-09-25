<?
/*******************************************************************************
 * linkor.redirect - SEO redirects module
 * Copyright 2020 Moroz Vadim
 * MIT License
 ******************************************************************************/

namespace Linkor\Redirect;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\{Application, Localization\Loc, HttpApplication, Loader, Config\Option};

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

// получаем идентификатор модуля
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
// подключаем наш модуль
Loader::includeModule($module_id);


// сохранение настроек
if (check_bitrix_sessid() && $request->isPost()) {
    $is_correct = true;
    if ($request->getPost("save") != "") {
        $data = $_POST;
        $rsSites = \CSite::GetList($by="sort", $order="desc");
        while ($arSite = $rsSites->Fetch())
        {
            if(!$arSite['DOC_ROOT'] && $arSite['LID'] != 's1' ||
                !file_exists($arSite['DOC_ROOT']) && $arSite['LID'] != 's1'){
                continue;
            }
            Update($data, $arSite['LID']);
            OptionsUpdate($data, $arSite['LID']);

            if($_FILES[$arSite['LID'].'_use_redirect_file']['name']) {
                if (CSVFileUpload($_FILES, $arSite['LID']) == false) {
                    $is_correct = false;
                    break;
                }
            }
        }
    }
    if ($request->getPost("delete_file") != "") {
        CSVFileDelete($_POST['site_id']);
    }
    if($is_correct){
        $message = new \CAdminMessage(['MESSAGE' => 'Настройки сохранены.', 'TYPE' => 'OK']);
        echo $message->Show();
    }

}

// проврека многосайтовости
$rsSites = \CSite::GetList($by="sort", $order="desc");
while ($arSite = $rsSites->Fetch()) {
    // проверяем настройки многосайтовости
    if(!$arSite['DOC_ROOT'] && $arSite['LID'] != 's1'){
        $message = new \CAdminMessage(['MESSAGE' => 'Укажите путь к корневой папке веб-сервера в настройках многосайтовости, для сайта '.$arSite['LID'], 'TYPE' => 'ERROR']);
        echo $message->Show();
    }
    elseif(!file_exists($arSite['DOC_ROOT']) && $arSite['LID'] != 's1'){
        $message = new \CAdminMessage(['MESSAGE' => 'Не верно указаны настройки многосайтовости, для сайта '.$arSite['LID'].'. Указан несуществующий путь к корневой папке!', 'TYPE' => 'ERROR']);
        echo $message->Show();
    }else{
        // создаем файл конфигов для многосайтовости
        //OptionsUpdate(array(), $arSite['LID']);
    }
}


$aTabs = [];
$rsSites = \CSite::GetList($by="sort", $order="desc");
while ($arSite = $rsSites->Fetch())
{
    $MainRoot = ($arSite['LID'] == 's1') ? $arSite['DOC_ROOT']: '';
    if(!$arSite['DOC_ROOT'] && $arSite['LID'] != 's1' ||
        !file_exists($arSite['DOC_ROOT']) && $arSite['LID'] != 's1'){
        continue;
    }
    if($arSite['LID'] == 's1'){
        $arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
    }

    $currentOptions = Options($arSite['DOC_ROOT']."/local/config/".ID.'/options.php');

    $arTab = [
        "DIV" => $arSite['LID'],
        "TAB" => Loc::getMessage("LINKOR_REDIRECT_BTN_OPTIONS").'('.$arSite['LID'].')',
        "TITLE" => Loc::getMessage("LINKOR_REDIRECT_OPTIONS_TITLE").'('.$arSite['LID'].')',
        'OPTIONS' => array(
            array(
                $arSite['LID'].'_redirect_www',
                Loc::getMessage('LINKOR_REDIRECT_OPTIONS_WWW_TITLE'),
                $currentOptions["redirect_www"] == "Y"? "Y" : "N",
                array('checkbox')
            ),
            array(
                $arSite['LID'].'_redirect_slash',
                Loc::getMessage('LINKOR_REDIRECT_OPTIONS_SLASH_TITLE'),
                $currentOptions["redirect_slash"] == "Y"? "Y" : "N",
                array('checkbox')
            ),
            array(
                $arSite['LID'].'_redirect_index_php',
                Loc::getMessage('LINKOR_REDIRECT_OPTIONS_INDEX_PHP_TITLE'),
                $currentOptions["redirect_index_php"] == "Y"? "Y" : "N",
                array('checkbox')
            ),
            array(
                $arSite['LID'].'_redirect_index_html',
                Loc::getMessage('LINKOR_REDIRECT_OPTIONS_INDEX_HTML_TITLE'),
                $currentOptions["redirect_index_html"] == "Y"? "Y" : "N",
                array('checkbox')
            ),
            array(
                $arSite['LID'].'_redirect_multislash',
                Loc::getMessage('LINKOR_REDIRECT_OPTIONS_MULTISLASH_TITLE'),
                $currentOptions["redirect_multislash"] == "Y"? "Y" : "N",
                array('checkbox')
            ),
            array(
                $arSite['LID'].'_redirect_from_uppercase',
                Loc::getMessage('LINKOR_REDIRECT_FROM_UPPERCASE'),
                $currentOptions["redirect_from_uppercase"] == "Y"? "Y" : "N",
                array('checkbox')
            ),
            array(
                $arSite['LID'].'_redirect_from_404',
                Loc::getMessage('LINKOR_REDIRECT_FROM_404'),
                $currentOptions["redirect_from_404"] == "Y"? "Y" : "N",
                array('checkbox')
            ),
            array(
                $arSite['LID'].'_use_redirect_urls',
                Loc::getMessage('LINKOR_REDIRECT_URLS_TITLE'),
                $currentOptions["use_redirect_urls"] == "Y"? "Y" : "N",
                array('checkbox')
            )
        )
    ];

    array_push($aTabs, $arTab);

}

$tabControl = new \CAdminTabControl(
    'tabControl',
    $aTabs
);



$tabControl->begin();
?>
<div class="alert-changes" style="margin-left: 15px">

</div>
<form method="post" enctype="multipart/form-data" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
    <?= bitrix_sessid_post(); ?>
        <? foreach ($aTabs as $aTab): ?>
        <? $docRoot = GetDocRootBySiteName($aTab['DIV']);
            $upFile = $docRoot."/local/config/".ID."/upload_urls.csv";
            $defFile = $docRoot."/local/config/".ID."/urls.csv";
            $cacheFile = $docRoot."/local/config/".ID."/urls.php";
        ?>
            <? $tabControl->beginNextTab(); ?>
            <? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>

            <td>
                <?= Loc::getMessage("LINKOR_REDIRECT_UPLOAD_FILE_LABEL") ?>
            </td>
            <td class="use_redirect_row">

                <? if(!file_exists($upFile)):?>
                    <input type="file" name="<?=$aTab['DIV']?>_use_redirect_file" id="<?=$aTab['DIV']?>use_redirect_file">
                <? else: ?>

                    <span class="delete_link"><?= Loc::getMessage("LINKOR_REDIRECT_FILE_NAME") ?></span>

                    <? $domain = GetDomainBySiteName($aTab['DIV']) ?>
                    <? if($domain): ?>
                    <a href="<?='http://'.$domain?>/local/config/<?=ID?>/upload_urls.csv" class="delete_link">
                        <?= Loc::getMessage("LINKOR_REDIRECT_DOWNLOAD_FILE") ?>
                    </a>
                    <? endif; ?>
                <input type="file" name="<?=$aTab['DIV']?>_use_redirect_file" id="<?=$aTab['DIV']?>use_redirect_file">
<!--                    <input type="hidden" name="site_id" value="--><?//=$aTab['DIV']?><!--">-->
<!--                    <input type="submit" name="delete_file"-->
<!--                           value="--><?//= Loc::getMessage("LINKOR_REDIRECT_DELETE_FILE") ?><!--">-->
                <? endif; ?>
            </td>

            <tr>
                <td colspan="2">
                    <table width="100%" class="js-table-autoappendrows">
                        <thead>
                        <tr>
                            <td>
                                <?= Loc::getMessage("LINKOR_REDIRECT_TABLE_LABEL_FROM") ?>
                            </td>
                            <td>
                                <?= Loc::getMessage("LINKOR_REDIRECT_TABLE_LABEL_TO") ?>
                            </td>
                            <td>
                                <?= Loc::getMessage("LINKOR_REDIRECT_TABLE_LABEL_TYPE") ?>
                            </td>
                            <td style="width: 10px">
                                <?= Loc::getMessage("LINKOR_REDIRECT_TABLE_LABEL_USE_MASK") ?>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <? $i = 0;
                        foreach (AppendValues(Select(true, $upFile, $defFile, $cacheFile), 5, ["", "", ""]) as $url) {
                            $i++; ?>
                            <tr data-idx="<?= $i ?>">
                                <td>
                                    <input type="text"
                                           name="<?=$aTab['DIV']?>_redirect_urls[<?= $i ?>][0]"
                                           value="<?= htmlspecialcharsex($url[0]) ?>"
                                           style="width:96%;"
                                           onchange="CheckUrl(this)"
                                           class="table-from">
                                </td>
                                <td>
                                    <input type="text"
                                           name="<?=$aTab['DIV']?>_redirect_urls[<?= $i ?>][1]"
                                           value="<?= htmlspecialcharsex($url[1]) ?>"
                                           style="width:96%;">
                                </td>
                                <td>
                                    <select name="<?=$aTab['DIV']?>_redirect_urls[<?= $i ?>][2]"
                                            title="<?= Loc::getMessage("LINKOR_REDIRECT_URLS_STATUS") ?>"
                                            style="width:96%;">
                                        <option value="301" <?= $url[2] == "301"? "selected" : "" ?>>301</option>
                                        <option value="302" <?= $url[2] == "302"? "selected" : "" ?>>302</option>
                                    </select>
                                </td>
                                <td>
                                    <input name="<?=$aTab['DIV']?>_redirect_urls[<?= $i ?>][3]" value="Y" type="checkbox"
                                           title="<?= Loc::getMessage("LINKOR_REDIRECT_URLS_IS_PART_URL") ?>"
                                        <?= $url[3] == "Y"? "checked" : "" ?>>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                    <span class="adm-info-message">
                    <?= Loc::getMessage("LINKOR_REDIRECT_TABLE_USE_MASK_NOTICE") ?>
                </span>
                </td>
            </tr>

            <td>
            <input type="hidden" name="site_id" value="<?=$aTab['DIV']?>">
            </td>
        <? endforeach; ?>

    <?  $tabControl->buttons(); ?>
    <input class="adm-btn-save" type="submit" name="save"
           value="<?= Loc::getMessage("LINKOR_REDIRECT_SAVE_SETTINGS") ?>">
</form>

<? $tabControl->end(); ?>

<style>
    .use_redirect_file_submit{
        margin-left: 10px;
    }
    .use_redirect_row{
        display: flex;
        align-items: center;
    }
    .delete_link{
        display: block;
        margin-right: 10px;
    }
</style>

<script>
    BX.ready(function () {
        "use strict";
        // autoappend rows
        function makeAutoAppend($table) {
            function bindEvents($row) {
                for (let $input of $row.querySelectorAll('input[type="text"]')) {
                    $input.addEventListener("change", function (event) {

                        let $tr = event.target.closest("tr");
                        let $trLast = $table.rows[$table.rows.length - 1];
                        if ($tr != $trLast) {
                            return;
                        }
                        $table.insertRow(-1);
                        $trLast = $table.rows[$table.rows.length - 1];
                        $trLast.innerHTML = $tr.innerHTML;
                        let idx = parseInt($tr.getAttribute("data-idx")) + 1;
                        $trLast.setAttribute("data-idx", idx);
                        for (let $input of $trLast.querySelectorAll("input,select")) {
                            let name = $input.getAttribute("name");
                            if (name) {
                                $input.setAttribute("name", name.replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
                            }
                        }
                        bindEvents($trLast);
                    });
                }
            }
            for (let $row of document.querySelectorAll(".js-table-autoappendrows tr")) {
                bindEvents($row);
            }
        }
        for (let $table of document.querySelectorAll(".js-table-autoappendrows")) {
            makeAutoAppend($table);
        }


        var items = document.getElementsByClassName("table-from");
        for (var i = 0; i < items.length; i++) {
            CheckUrl(items[i]);
        }

    });

    function validateURL(textval) {
        var urlregex = new RegExp(
            "^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
        return urlregex.test(textval);
    }
    function clearUrl(url){
        return url.replace(/^.*\/\/[^\/]+/, '');
    }
    function CheckUrl(context){
        if(validateURL(context.value)){
            let newUrl = clearUrl(context.value);
            context.value = newUrl;


            let alert = document.getElementsByClassName('alert-changes');
            alert[0].innerHTML = '<div class="adm-info-message">Внимание! Абсолютные URL адреса были преобразованые в относительные. Примените настройки, чтобы изменения вступили всилу.</div>';
        }
    }

</script>
