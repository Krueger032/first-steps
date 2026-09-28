<?
namespace Aspro\Max\Components;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Config\Option,
    Bitrix\Main\Web\Json,
    Bitrix\Main\SystemException,
    CMax as Solution;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class ThemeSelectorMax extends \CBitrixComponent {
    protected $action;
    protected $isAction;
    protected $isJson;

    protected static function getRand() {
        static $get, $post;

        if (!isset($get)) {
            $get = 0;
        }

        if (!isset($post)) {
            $post = 0;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return 'p'.$post++;
        }

        return 'g'.$get++;
    }

    public function onPrepareComponentParams($arParams){
        if (isset($arParams['CUSTOM_SITE_ID'])) {
			$this->setSiteId($arParams['CUSTOM_SITE_ID']);
		}

		if (isset($arParams['CUSTOM_LANGUAGE_ID'])) {
			$this->setLanguageId($arParams['CUSTOM_LANGUAGE_ID']);
		}

        if (!isset($arParams['ICON_SIZE'])) {
            $arParams['ICON_SIZE'] = 'md';
        }

        return $arParams;
    }

    public function executeComponent() {
        $this->setFrameMode(true);

        if ($this->request->isPost()) {
            $this->action = $this->request['action'];
        }

    	$this->isAction = (bool) strlen($this->action);

    	if ($this->isAction) {
			$GLOBALS['APPLICATION']->RestartBuffer();
		}

        $this->arResult = [
            'RAND' => \Bitrix\Main\Security\Random::getString(5),
        ];

        try {
            $this->includeModules();

            if (
                $this->action &&
                method_exists($this, $actionMethod = $this->getActionMethod())
            ) {
                $this->$actionMethod();
            } else {
                $this->showAction();
            }
        }
        catch (SystemException $e) {
            $this->arResult['ERROR'] = $e->getMessage();
        }

        if ($this->action) {
            $GLOBALS['APPLICATION']->RestartBuffer();
        }

        if (!$this->isJson) {
            $isAjaxBlockToggle = $this->request->isPost() && $this->request['BLOCK'] === 'HEADER_TOGGLE_THEME_SELECTOR' && $this->request['IS_AJAX'] === 'Y';
            if ($isAjaxBlockToggle) {
                $GLOBALS['APPLICATION']->ShowAjaxHead();
            }

            $this->includeComponentTemplate();
        }

        return $this->arResult;
    }

    protected function getActionMethod() {
        return $this->action ? $this->action.'Action' : '';
    }

    public function showAction() {
        $signer = new \Bitrix\Main\Security\Sign\Signer;
        $signedParams = $signer->sign(base64_encode(serialize($this->arParams)), str_replace(':', '.', $this->getName()));

        $siteId = $this->getSiteId();
        $themeViewColor = strtolower(Solution::GetFrontParametrValue('THEME_VIEW_COLOR', $siteId, false));

        $this->arResult = [
            'RAND' => self::getRand(),
            'SIGNED_PARAMS' => $signedParams,
            'LANGUAGE_ID' => $this->getLanguageId(),
            'SITE_ID' => $this->getSiteId(),
            'COLOR' => $themeViewColor,
        ];
    }

    protected function setColorAction() {
        $this->isJson = true;

        $siteId = $this->getSiteId();
        $color = trim($this->request['color']);
        if (
            strlen($color) &&
            in_array($color, ['light', 'dark', 'default'])
        ) {
            $color = strtoupper($color);
            $_SESSION['THEME'][$siteId]['THEME_VIEW_COLOR'] = strtoupper($color);

            $this->arResult['COLOR'] = $color;
        }

        $themeViewColor = strtolower(Solution::GetFrontParametrValue('THEME_VIEW_COLOR', $siteId, false));
        $this->arResult['COLOR'] = $themeViewColor;
    }

    protected function includeModules() {
        if (!Loader::includeModule(Solution::moduleID)) {
            throw new SystemException(Loc::getMessage('WS_C_ERROR_MODULE_NOT_INSTALLED'));
        }
    }
}
