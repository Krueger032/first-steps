<?
use Bitrix\Main\Localization\Loc,
	CMax as Solution;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

$this->setFrameMode(true);

$arResult['TEMPLATE'] = $this->{'__name'};
$arIconSize = match ($arParams['ICON_SIZE']) {
	'sm' => ['WIDTH' => 16, 'HEIGHT' => 16],
	default => ['WIDTH' => 18, 'HEIGHT' => 18],
};
$iconClassList = 'icon-block__icon icon-block__icon--'.$arParams['ICON_SIZE'].' banner-light-icon-fill big inline';
?>
<button type="button"
	id="theme-selector--<?=$arResult['RAND']?>"
	class="theme-selector btn--no-btn-appearance"
	title="<?=Loc::getMessage('TS_T_'.$arResult['COLOR'])?>"
>
	<span class="theme-selector__inner">
		<span class="theme-selector__items">
			<span class="theme-selector__item theme-selector__item--light<?=($arResult['COLOR'] === 'light' ? ' current' : '')?> icon-block">
				<span class="line-block line-block--gap line-block--gap-20">
					<span class="theme-selector__item-icon">
						<?=Solution::showSpriteIconSvg($this->{'__folder'}.'/images/svg/icons.svg#light-'.$arParams['ICON_SIZE'], $iconClassList, $arIconSize);?>
					</span>
					<?if ($arParams['USE_TEXT'] === 'Y'):?>
						<span class="theme-selector__item-text"><?=Loc::getMessage('TS_T_light_simple');?></span>
					<?endif;?>
				</span>
			</span>
			<span class="theme-selector__item theme-selector__item--dark<?=($arResult['COLOR'] === 'dark' ? ' current' : '')?> icon-block">
				<span class="line-block line-block--gap line-block--gap-20">
					<span class="theme-selector__item-icon">
						<?=Solution::showSpriteIconSvg($this->{'__folder'}.'/images/svg/icons.svg#dark-'.$arParams['ICON_SIZE'], $iconClassList, $arIconSize);?>
					</span>
					<?if ($arParams['USE_TEXT'] === 'Y'):?>
						<span class="theme-selector__item-text"><?=Loc::getMessage('TS_T_dark_simple');?></span>
					<?endif;?>
				</span>
			</span>
		</span>
	</span>
	<script>
	BX.message({
		TS_T_light: '<?=GetMessageJS('TS_T_light')?>',
		TS_T_dark: '<?=GetMessageJS('TS_T_dark')?>',
	});

	new JThemeSelector(
		'<?=$arResult['RAND']?>',
		<?=CUtil::PhpToJSObject($arParams, false, true)?>, <?=CUtil::PhpToJSObject($arResult, false, true)?>
	);
	</script>
</button>
