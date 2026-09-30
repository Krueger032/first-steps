<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

if(empty($arResult) || !$USER->IsAuthorized())
	return;
	
global $arSettings;?>

<div class="user-menu-popup" data-role="dropdownContent" style="display: none;">
	<ul>
		<?foreach($arResult as $arItem) {
			if(($arItem["PARAMS"]["CODE"] != "BASKET" || ($arItem["PARAMS"]["CODE"] == "BASKET" && $arSettings["DISABLE_BASKET"]["VALUE"] != "Y" && $arSettings["DISABLE_DELAY"]["VALUE"] != "Y")) && ($arItem["PARAMS"]["CODE"] != "DISCOUNT" || ($arItem["PARAMS"]["CODE"] == "DISCOUNT" && $arSettings["ACCUMULATIVE_DISCOUNT"]["VALUE"] == "Y"))) {?>
				<li>
					<a class="user-menu-item<?=($arItem['SELECTED'] ? ' selected' : '')?>" href="<?=$arItem['LINK']?>" title="<?=$arItem['TEXT']?>">						
						<?if(!empty($arItem['PARAMS']['ICON'])) {?>
							<span class="user-menu-item-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
						<?}?>
						<span class="user-menu-item-name"><?=htmlspecialcharsbx($arItem["TEXT"])?></span>
						<?if(isset($arItem["COUNT"])) {?>
							<span class="user-menu-item-count<?=($arItem['PARAMS']['CODE'] == 'BASKET' ? ' user-menu-item-scheme-count' : '').($arItem['COUNT'] > 0 ? '' : ' user-menu-item-count-empty')?>"><?=$arItem["COUNT"]?></span>
						<?}?>
					</a>
				</li>
			<?}
		}
		unset($arItem);?>
		<li>
			<a class="user-menu-item" href="?logout=yes&<?=bitrix_sessid_get()?>" title="<?=Loc::getMessage('USER_MENU_EXIT')?>">
				<span class="user-menu-item-icon"><i class="icon-logout"></i></span>
				<span class="user-menu-item-name"><?=Loc::getMessage("USER_MENU_EXIT")?></span>
			</a>
		</li>
	</ul>
</div>