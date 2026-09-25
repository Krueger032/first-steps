<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;?>

<span class="alert alert-warning">
	<?=Loc::getMessage("EMPTY_BASKET_TITLE");?>
	<br />
	<?=Loc::getMessage('EMPTY_BASKET_HINT', array('#A1#' => '<a href="'.SITE_DIR.'catalog/">', '#A2#' => '</a>'));?>
</span>