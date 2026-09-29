<?
/*******************************************************************************
 * linkor.redirect - SEO redirects module
 * Copyright 2020 Moroz Vadim
 * MIT License
 ******************************************************************************/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class linkor_redirect extends CModule {
    var $MODULE_ID = "linkor.redirect";
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;

    public $MODULE_GROUP_RIGHTS;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct(){
        if(file_exists(__DIR__."/version.php")){
            $arModuleVersion = array();
            include(__DIR__."/version.php");

            $this->MODULE_ID            = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME          = Loc::getMessage("LINKOR_REDIRECT_MODULE_NAME");
            $this->MODULE_DESCRIPTION   = Loc::getMessage("LINKOR_REDIRECT_MODULE_DESCRIPTION");
            $this->PARTNER_NAME         = Loc::getMessage("LINKOR_REQUIREMENTS_PARTNER_NAME");
            $this->PARTNER_URI          = Loc::getMessage("LINKOR_REQUIREMENTS_PARTNER_URI");
        }
        return false;
    }
    function DoInstall() {
        global $APPLICATION;

        if (version_compare(PHP_VERSION, "7", "<")) {
            $APPLICATION->ThrowException(Loc::getMessage("LINKOR_REQUIREMENTS_PHP_VERSION"));
            return false;
        }
        if (!defined("BX_UTF")) {
            $APPLICATION->ThrowException(Loc::getMessage("LINKOR_REQUIREMENTS_BITRIX_UTF8"));
            return false;
        }
        RegisterModule($this->MODULE_ID);
        RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID); // событие на срабатывание модуля
    }
    function DoUninstall() {
        UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID);
        UnRegisterModule($this->MODULE_ID);

        // config путь
        $rsSites = \CSite::GetList($by="sort", $order="desc");
        while ($arSite = $rsSites->Fetch()) {
            $config = $arSite['DOC_ROOT'] . "/local/config/".$this->MODULE_ID;
            $this->DeleteConfig($config);
        }

    }
    function DeleteConfig($dir) {
        if (file_exists($dir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $path) {
                if ($path->isDir()) {
                    rmdir((string)$path);
                } else {
                    unlink((string)$path);
                }
            }
            rmdir($dir);
        }
    }
}