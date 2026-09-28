<?
namespace Aspro\Max\Components;

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\SystemException,
    CMax as Solution;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$lang = isset($_REQUEST['lang']) ? trim($_REQUEST['lang']) : LANGUAGE_ID;
Loc::setCurrentLang($lang);
Loc::loadMessages(__FILE__);

class ThemeSelectorController extends \Bitrix\Main\Engine\Controller {
	public function configureActions(){
        return array(
            'show' => array(
                'prefilters' => array(),
            ),
            'setColor' => array(
                'prefilters' => array(),
            ),
        );
    }

    public function showAction() {
        $tmp = $this->action();

        return $tmp['content'];
    }

    public function setColorAction() {
        $tmp = $this->action();

        return $tmp['result'];
    }

    protected function prepare() {
        $this->includeModules();

        $componentName = Solution::partnerName.':theme.selector.'.Solution::solutionName;

        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

        $siteId = $request->get('SITE_ID');
        $lang = $request->get('lang');
        $sessid = $request->get('sessid');
        $template = $request->get('TEMPLATE');
        $signedParameters = $request->get('SIGNED_PARAMS');

        $this->checkSession($sessid);
        $this->checkSite($siteId);

        $signer = new \Bitrix\Main\Component\ParameterSigner;
        $arParams = $signer->unsignParameters(str_replace(':', '.', $componentName), $signedParameters);

        $template = $arParams['COMPONENT_TEMPLATE'] ?: $template;
        $arParams['CUSTOM_LANGUAGE_ID'] = $lang;
        $arParams['CUSTOM_SITE_ID'] = $siteId;

        return [
            'componentName' => $componentName,
            'template' => $template,
            'arParams' => $arParams,
        ];
    }

    protected function includeModules() {
        if (!Loader::includeModule(Solution::moduleID)) {
            throw new SystemException(Loc::getMessage('TS_C_ERROR_MODULE_NOT_INSTALLED'));
        }
    }

    protected function checkSession($sessid) {
        if ($sessid !== bitrix_sessid()) {
            throw new SystemException(Loc::getMessage('TS_C_ERROR_BAD_SESSID'));
        }
    }

    protected function checkSite($siteId) {
        if (!$siteId) {
            throw new SystemException(Loc::getMessage('TS_C_ERROR_BAD_SITE'));
        } else {
            $arSite = \CSite::GetByID($siteId)->Fetch();
            if (!$arSite) {
                throw new SystemException(Loc::getMessage('TS_C_ERROR_BAD_SITE'));
            }
        }
    }

    protected function action() {
        $tmp = $this->prepare();

        $GLOBALS['APPLICATION']->RestartBuffer();

        ob_start();
        $result = $GLOBALS['APPLICATION']->IncludeComponent(
			$tmp['componentName'],
			$tmp['template'],
			$tmp['arParams']
		);
        $content = ob_get_clean();

        return [
            'result' => $result,
            'content' => $content,
        ];
    }
}
