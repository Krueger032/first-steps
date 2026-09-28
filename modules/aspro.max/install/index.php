<?php

global $MESS;
$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen('/install/index.php'));
include GetLangFileName($strPath2Lang.'/lang/', '/install/index.php');

class aspro_max extends CModule
{
    public const solutionName = 'max';
    public const partnerName = 'aspro';
    public const moduleClass = 'CMax';
    public const moduleClassEvents = 'CMaxEvents';
    public const moduleClassCache = 'CMaxCache';
    public const moduleClassPreset = 'Aspro\Max\Preset';

    public $MODULE_ID = 'aspro.max';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;
    public $MODULE_GROUP_RIGHTS = 'Y';

    public function __construct()
    {
        $arModuleVersion = [];

        $path = str_replace('\\', '/', __FILE__);
        $path = substr($path, 0, strlen($path) - strlen('/index.php'));
        include $path.'/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('ASPRO_MAX_SCOM_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('ASPRO_MAX_SCOM_INSTALL_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage('ASPRO_MAX_SPER_PARTNER');
        $this->PARTNER_URI = GetMessage('ASPRO_MAX_PARTNER_URI');
    }

    public function checkValid()
    {
        return true;
    }

    public function InstallDB($install_wizard = true)
    {
        global $DB, $DBType, $APPLICATION;

        if (preg_match('/.bitrixlabs.ru/', $_SERVER['HTTP_HOST'])) {
            RegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, self::moduleClassEvents, 'correctInstall');
        }

        RegisterModule($this->MODULE_ID);

        // autoload classes
        require_once realpath(__DIR__.'/../include.php');

        if (!Aspro\Max\ShareBasketTable::getEntity()->getConnection()->isTableExists(Aspro\Max\ShareBasketTable::getTableName())) {
            Aspro\Max\ShareBasketTable::getEntity()->createDbTable();
        }

        if (!Aspro\Max\ShareBasketItemTable::getEntity()->getConnection()->isTableExists(Aspro\Max\ShareBasketItemTable::getTableName())) {
            Aspro\Max\ShareBasketItemTable::getEntity()->createDbTable();
        }

        return true;
    }

    public function UnInstallDB($arParams = [])
    {
        global $DB, $DBType, $APPLICATION;

        // autoload classes
        require_once realpath(__DIR__.'/../include.php');

        Aspro\Max\ShareBasketTable::getEntity()->getConnection()->queryExecute('drop table if exists '.Aspro\Max\ShareBasketTable::getTableName());
        Aspro\Max\ShareBasketItemTable::getEntity()->getConnection()->queryExecute('drop table if exists '.Aspro\Max\ShareBasketItemTable::getTableName());

        UnRegisterModule($this->MODULE_ID);

        return true;
    }

    public function getEvents()
    {
        $arEvents = [
            'catalog' => [
                ['\Bitrix\Catalog\Subscribe::onBeforeAdd', self::moduleClassEvents, 'onBeforeProductSubscribeHandler'],
                ['\Bitrix\Catalog\Model\Product::OnAfterAdd', self::moduleClassEvents, 'setStockProduct'],
                ['\Bitrix\Catalog\Model\Product::OnAfterUpdate', self::moduleClassEvents, 'setStockProduct'],
            ],
            'main' => [
                ['\Bitrix\Main\Controller\LoadExt::onBeforeAction', self::moduleClassEvents, 'OnBeforeAction'],
            ],
            'sale' => [
                ['OnSaleOrderSaved', self::moduleClassEvents, 'BeforeSendEvent', 10],
            ],
        ];

        return $arEvents;
    }

    public function getCompatibleEvents()
    {
        $arCompatibleEvents = [
            'blog' => [
                ['OnBeforeCommentAdd', self::moduleClassEvents, 'OnBeforeCommentAddHandler'],
                ['OnCommentAdd', self::moduleClassEvents, 'OnCommentAddHandler'],
                ['OnBeforeCommentUpdate', self::moduleClassEvents, 'OnBeforeCommentUpdateHandler'],
                ['OnCommentUpdate', self::moduleClassEvents, 'OnCommentUpdateHandler'],
                ['OnCommentDelete', self::moduleClassEvents, 'OnCommentDeleteHandler'],
            ],
            'catalog' => [
                ['OnPriceAdd', self::moduleClassEvents, 'DoIBlockAfterSave'],
                ['OnPriceUpdate', self::moduleClassEvents, 'DoIBlockAfterSave'],
                ['OnStoreProductAdd', self::moduleClassEvents, 'setStoreProductHandler'],
                ['OnStoreProductUpdate', self::moduleClassEvents, 'setStoreProductHandler'],
                ['OnGetOptimalPrice', self::moduleClassEvents, 'OnGetOptimalPriceHandler'],
                ['OnCatalogStoreAdd', 'Aspro\Max\Stores\EventHandler', 'OnCatalogStoreAdd'],
                ['OnCatalogStoreUpdate', 'Aspro\Max\Stores\EventHandler', 'OnCatalogStoreUpdate'],
                ['OnCatalogStoreDelete', 'Aspro\Max\Stores\EventHandler', 'OnCatalogStoreDelete'],
                ['OnStoreProductAdd', 'Aspro\Max\Stores\EventHandler', 'OnStoreProductAdd'],
                ['OnBeforeStoreProductUpdate', 'Aspro\Max\Stores\EventHandler', 'OnBeforeStoreProductUpdate'],
                ['OnStoreProductUpdate', 'Aspro\Max\Stores\EventHandler', 'OnStoreProductUpdate'],
                ['OnProductSetAdd', 'Aspro\Max\Stores\EventHandler', 'OnProductSetAdd'],
                ['OnProductSetUpdate', 'Aspro\Max\Stores\EventHandler', 'OnProductSetUpdate'],
                ['OnBeforeStoreProductDelete', 'Aspro\Max\Stores\EventHandler', 'OnBeforeStoreProductDelete'],
                ['OnStoreProductDelete', 'Aspro\Max\Stores\EventHandler', 'OnStoreProductDelete'],
            ],
            'form' => [
                ['onAfterResultAdd', self::moduleClassEvents, 'onAfterResultAddHandler'],
                ['onBeforeResultAdd', self::moduleClassEvents, 'onBeforeResultAddHandler'],
            ],
            'iblock' => [
                ['OnAfterIBlockAdd', self::moduleClassCache, 'ClearTagIBlock'],
                ['OnAfterIBlockUpdate', self::moduleClassCache, 'ClearTagIBlock'],
                ['OnBeforeIBlockDelete', self::moduleClassCache, 'ClearTagIBlockBeforeDelete'],
                ['OnAfterIBlockElementAdd', self::moduleClassCache, 'ClearTagIBlockElement'],
                ['OnAfterIBlockElementUpdate', self::moduleClassCache, 'ClearTagIBlockElement'],
                ['OnAfterIBlockElementUpdate', self::moduleClassEvents, 'OnRegionUpdateHandler'],
                ['OnAfterIBlockSectionAdd', self::moduleClassCache, 'ClearTagIBlockSection'],
                ['OnAfterIBlockSectionUpdate', self::moduleClassCache, 'ClearTagIBlockSection'],
                ['OnAfterIBlockPropertyUpdate', self::moduleClassCache, 'ClearTagByProperty'],
                ['OnAfterIBlockPropertyAdd', self::moduleClassCache, 'ClearTagByProperty'],
                ['OnAfterIBlockPropertyDelete', self::moduleClassCache, 'ClearTagByProperty'],
                ['OnBeforeIBlockSectionDelete', self::moduleClassCache, 'ClearTagIBlockSectionBeforeDelete'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\ListStores', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\ListPrices', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\RegionLocation', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\CustomFilter', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\Service', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\YaDirectQuery', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\IBInherited', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\ListUsersGroups', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\ListWebForms', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\RegionPhone', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\ModalConditions', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\ConditionType', 'OnIBlockPropertyBuildList'],
                ['OnIBlockPropertyBuildList', 'Aspro\Max\Property\TextWithLink', 'OnIBlockPropertyBuildList'],
                ['OnAfterIBlockElementUpdate', self::moduleClassEvents, 'DoIBlockAfterSave'],
                ['OnAfterIBlockElementAdd', self::moduleClassEvents, 'DoIBlockAfterSave'],
                ['OnBeforeIBlockUpdate', 'Aspro\Max\PropertyGroups', 'iblockUpdateEventHandler'],
                ['OnAfterIBlockElementUpdate', 'Aspro\Max\Stores\EventHandler', 'OnAfterIBlockElementUpdate'],
            ],
            'main' => [
                ['OnAfterUserUpdate', self::moduleClassCache, 'ClearTagByUser'],
                ['OnAfterAjaxResponse', self::moduleClassEvents, 'onAfterAjaxResponseHandler'],
                ['OnPageStart', self::moduleClassEvents, 'OnPageStartHandler'],
                ['OnBeforeUserRegister', self::moduleClassEvents, 'OnBeforeUserUpdateHandler'],
                ['OnBeforeUserRegister', self::moduleClassEvents, 'onBeforeUserRegisterHandler'],
                ['OnBeforeUserAdd', self::moduleClassEvents, 'OnBeforeUserUpdateHandler'],
                ['OnBeforeUserUpdate', self::moduleClassEvents, 'OnBeforeUserUpdateHandler'],
                ['OnEndBufferContent', self::moduleClassEvents, 'OnEndBufferContentHandler'],
                ['OnBeforeEventAdd', self::moduleClassEvents, 'OnBeforeEventAddHandler'],
                ['OnEpilog', self::moduleClassEvents, 'OnEpilogHandler'],
                ['OnBeforeChangeFile', self::moduleClassEvents, 'OnBeforeChangeFileHandler'],
                ['OnChangeFile', self::moduleClassEvents, 'OnChangeFileHandler', 999],
                ['OnAdminContextMenuShow', self::moduleClassEvents, 'OnAdminContextMenuShowHandler'],
                ['OnBeforeUserLogin', self::moduleClassEvents, 'OnBeforeUserLoginHandler'],
                ['OnAfterUserLogin', self::moduleClassEvents, 'OnAfterUserLoginHandler'],
                ['OnAdminTabControlBegin', 'Aspro\Max\PropertyGroups', 'eventHandler'],
            ],
            'sale' => [
                ['OnSaleComponentOrderOneStepComplete', self::moduleClassEvents, 'clearBasketCacheHandler'],
                ['OnBasketAdd', self::moduleClassEvents, 'clearBasketCacheHandler'],
                ['OnBeforeBasketUpdate', self::moduleClassEvents, 'OnBeforeBasketUpdateHandler'],
                ['OnSaleComponentOrderProperties', self::moduleClassEvents, 'OnSaleComponentOrderPropertiesHandler'],
                ['OnSaleComponentOrderProperties', self::moduleClassEvents, 'OnSaleComponentOrderProperties'],
                ['OnSaleComponentOrderOneStepComplete', self::moduleClassEvents, 'OnSaleComponentOrderOneStepComplete'],
                ['OnSaleComponentOrderOneStepProcess', self::moduleClassEvents, 'OnSaleComponentOrderOneStepProcess'],
                ['OnSaleComponentOrderJsData', self::moduleClassEvents, 'OnSaleComponentOrderJsDataHandler'],
            ],
            'search' => [
                ['OnSearchGetURL', self::moduleClassEvents, 'OnSearchGetURL'],
            ],
            'sender' => [
                ['onPresetTemplateList', "\Aspro\Solution\CAsproMarketingMax", 'senderTemplateList'],
            ],
            'seo' => [
                ['\Bitrix\Seo\Sitemap::OnAfterUpdate', self::moduleClassEvents, 'OnAfterUpdateSitemapHandler'],
            ],
            'socialservices' => [
                ['OnAfterSocServUserAdd', self::moduleClassEvents, 'OnAfterSocServUserAddHandler'],
                ['OnFindSocialservicesUser', self::moduleClassEvents, 'OnFindSocialservicesUserHandler'],
            ],
            'subscribe' => [
                ['OnBeforeSubscriptionAdd', self::moduleClassEvents, 'OnBeforeSubscriptionAddHandler'],
                ['OnBeforeSubscriptionUpdate', self::moduleClassEvents, 'OnBeforeSubscriptionAddHandler'],
            ],
            $this->MODULE_ID => [
                ['OnCatalogDeliveryComponentInitUserResult', self::moduleClassEvents, 'OnCatalogDeliveryComponentInitUserResult'],
                ['OnAsproParameters', self::moduleClassEvents, 'onAsproParametersHandler'],
            ],
            'aspro.sku' => [
                ['OnAsproSkuSetCanBuy', 'Aspro\Max\Vendor', 'onSkuCanBuyHandler'],
            ],
        ];

        return $arCompatibleEvents;
    }

    public function InstallEvents()
    {
        $eventManager = Bitrix\Main\EventManager::getInstance();

        $arCompatibleEvents = $this->getCompatibleEvents();
        foreach ($arCompatibleEvents as $fromModuleId => $arModuleEvents) {
            foreach ($arModuleEvents as $arModuleEvent) {
                [$eventType, $toClass, $toMethod] = $arModuleEvent;
                $sort = $arModuleEvent[3] ?? null;
                $toPath = $arModuleEvent[4] ?? null;
                $toMethodArg = $arModuleEvent[5] ?? null;
                $eventManager->registerEventHandlerCompatible($fromModuleId, $eventType, $this->MODULE_ID, $toClass, $toMethod, $sort, $toPath, $toMethodArg);
            }
        }

        $arEvents = $this->getEvents();
        foreach ($arEvents as $fromModuleId => $arModuleEvents) {
            foreach ($arModuleEvents as $arModuleEvent) {
                [$eventType, $toClass, $toMethod] = $arModuleEvent;
                $sort = $arModuleEvent[3] ?? null;
                $toPath = $arModuleEvent[4] ?? null;
                $toMethodArg = $arModuleEvent[5] ?? null;
                $eventManager->registerEventHandler($fromModuleId, $eventType, $this->MODULE_ID, $toClass, $toMethod, $sort, $toPath, $toMethodArg);
            }
        }

        return true;
    }

    public function UnInstallEvents()
    {
        $eventManager = Bitrix\Main\EventManager::getInstance();

        $arEvents = $this->getCompatibleEvents() + $this->getEvents();
        foreach ($arEvents as $fromModuleId => $arModuleEvents) {
            foreach ($arModuleEvents as $arModuleEvent) {
                [$eventType, $toClass, $toMethod] = $arModuleEvent;
                $toPath = $arModuleEvent[4] ?? null;
                $toMethodArg = $arModuleEvent[5] ?? null;
                $eventManager->unRegisterEventHandler($fromModuleId, $eventType, $this->MODULE_ID, $toClass, $toMethod, $toPath, $toMethodArg);
            }
        }

        // compatibility
        UnRegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, self::moduleClassEvents, 'ShowPanel');

        return true;
    }

    public function removeDirectory($dir)
    {
        if ($objs = glob($dir.'/*')) {
            foreach ($objs as $obj) {
                if (is_dir($obj)) {
                    CMax::removeDirectory($obj);
                } else {
                    if (!unlink($obj)) {
                        if (chmod($obj, 0777)) {
                            unlink($obj);
                        }
                    }
                }
            }
        }
        if (!rmdir($dir)) {
            if (chmod($dir, 0777)) {
                rmdir($dir);
            }
        }
    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true);
        CopyDirFiles(__DIR__.'/css/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/css/'.self::partnerName.'.'.self::solutionName, true, true);
        CopyDirFiles(__DIR__.'/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::partnerName.'.'.self::solutionName, true, true);
        CopyDirFiles(__DIR__.'/tools/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.self::partnerName.'.'.self::solutionName, true, true);
        CopyDirFiles(__DIR__.'/images/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/'.self::partnerName.'.'.self::solutionName, true, true);
        CopyDirFiles(__DIR__.'/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
        CopyDirFiles(__DIR__.'/wizards/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards', true, true);

        $this->InstallGadget();

        /*if(preg_match('/.bitrixlabs.ru/', $_SERVER["HTTP_HOST"])){
            @set_time_limit(0);
            require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
            CFileMan::DeleteEx(array('s1', '/bitrix/modules/'.$this->MODULE_ID.'/install/wizards'));
            CFileMan::DeleteEx(array('s1', '/bitrix/modules/'.$this->MODULE_ID.'/install/gadgets'));
        }*/

        return true;
    }

    public function InstallPublic()
    {
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
        DeleteDirFilesEx('/bitrix/css/'.self::partnerName.'.'.self::solutionName.'/');
        DeleteDirFilesEx('/bitrix/js/'.self::partnerName.'.'.self::solutionName.'/');
        DeleteDirFilesEx('/bitrix/tools/'.self::partnerName.'.'.self::solutionName.'/');
        DeleteDirFilesEx('/bitrix/images/'.self::partnerName.'.'.self::solutionName.'/');
        DeleteDirFilesEx('/bitrix/wizards/'.self::partnerName.'/'.self::solutionName.'/');

        $this->UnInstallGadget();

        return true;
    }

    public function InstallGadget()
    {
        CopyDirFiles(__DIR__.'/gadgets/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/gadgets/', true, true);

        $gadget_id = strtoupper(self::solutionName);
        $gdid = $gadget_id.'@'.rand();
        if (class_exists('CUserOptions')) {
            $arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
            if (is_array($arUserOptions) && isset($arUserOptions[0])) {
                foreach ($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget) {
                    $p = strpos($tempid, '@');
                    $gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

                    if ($gadget_id_tmp == $gadget_id) {
                        return false;
                    }
                    if ($tempgadget['COLUMN'] == 0) {
                        ++$arUserOptions[0]['GADGETS'][$tempid]['ROW'];
                    }
                }
                $arUserOptions[0]['GADGETS'][$gdid] = ['COLUMN' => 0, 'ROW' => 0];
                CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
            }
        }

        return true;
    }

    public function UnInstallGadget()
    {
        $gadget_id = strtoupper(self::solutionName);
        if (class_exists('CUserOptions')) {
            $arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
            if (is_array($arUserOptions) && isset($arUserOptions[0])) {
                foreach ($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget) {
                    $p = strpos($tempid, '@');
                    $gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

                    if ($gadget_id_tmp == $gadget_id) {
                        unset($arUserOptions[0]['GADGETS'][$tempid]);
                    }
                }
                CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
            }
        }

        DeleteDirFilesEx('/bitrix/gadgets/'.self::partnerName.'/'.self::solutionName.'/');

        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION, $step;

        // autoload classes
        require_once realpath(__DIR__.'/../include.php');
        $this->InstallFiles();
        $this->InstallDB(false);
        $this->InstallEvents();
        $this->InstallPublic();

        $APPLICATION->IncludeAdminFile(GetMessage('ASPRO_MAX_SCOM_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/aspro.max/install/step.php');
    }

    public function DoUninstall()
    {
        global $APPLICATION, $step;

        // autoload classes
        require_once realpath(__DIR__.'/../include.php');

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();

        $APPLICATION->IncludeAdminFile(GetMessage('ASPRO_MAX_SCOM_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/aspro.max/install/unstep.php');
    }
}
