<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}
$this->setFrameMode(true);

$arParams['POSITION'] = $arParams['POSITION'] ?? '';
$arParams['SLIDER_MODE'] = $arParams['SLIDER_MODE'] ?? 'N';

$needWrap = $arParams['POSITION'] == 'CONTENT_TOP' || $arParams['POSITION'] == 'CONTENT_BOTTOM';
$templateData['ITEMS'] = false; ?>
<?if($arResult['ITEMS']):?>
    <?php
    $count = count($arResult['ITEMS']);
    $arParams['MENU_BANNER'] = $arParams['MENU_BANNER'] ?? false;
    $sliderMode = ($arParams['SLIDER_MODE'] === 'Y' || $arParams['MENU_BANNER']);
    $sliderAutoPlay = isset($arParams['SLIDER_AUTOPLAY']) && $arParams['SLIDER_AUTOPLAY'] === 'Y';
    $bannersInGoods = isset($arParams['BANNER_IN_GOODS']) && $arParams['BANNER_IN_GOODS'] === 'Y';
    $slidesSpeed = isset($arParams['SLIDES_SPEED']) ? (int) $arParams['SLIDES_SPEED'] : 5000;
    $animationSpeed = isset($arParams['ANIMATION_SPEED']) ? (int) $arParams['ANIMATION_SPEED'] : 1000;
    $sliderClass = !$bannersInGoods && !$arParams['MENU_BANNER'] ? 'swiper' : (!$arParams['MENU_BANNER'] && $bannersInGoods && $arParams['SLIDER_WAIT'] === 'Y' ? 'owl-carousel-wait loader_circle ' : '');

    ?>
    <?if($sliderMode && $arParams['SHOW_ALL_ELEMENTS'] != 'N'):?>
        <?$templateData['ITEMS'] = true; ?>
        <?php
        $arOptions = [
            // Disable preloading of all images
            'preloadImages' => false,
            // Enable lazy loading
            'lazy' => false,
            'keyboard' => true,
            'init' => false,
            'rewind' => true,

            'freeMode' => [
                'enabled' => true,
                'momentum' => true,
                'sticky' => true,
            ],
            'watchSlidesProgress' => true, // fix slide on click on slide link in mobile template
            'slidesPerView' => 'auto',
            'spaceBetween' => 10,
            // 'pagination' => false,
            'type' => 'banners_in_header',
        ];
        if ($sliderAutoPlay) {
            $arOptions['autoplay'] = [
                'delay' => $slidesSpeed,
                'pauseOnMouseEnter' => true,
            ];
            $arOptions['speed'] = $animationSpeed;
        }
        ?>
        <div class="relative navigation_on_hover slider-solution--show-nav-hover <?= $bannersInGoods ? 'sm-nav nav-in-slider slide-solution--overflow-hidden' : 'swiper-nav-offset'; ?>">
            <?if($needWrap):?><div class="banners_slider_wrap <?=$arParams['POSITION']; ?><?=$arParams['POSITION'] === 'CONTENT_BOTTOM' ? ' mt mt--32' : ''; ?>"><?endif; ?>
            <div class="<?=$arParams['MENU_BANNER'] ? ' owl-carousel-hover slide-solution--overflow-hidden' : ' dots-in-slider banners-slider'; ?> <?=$sliderClass; ?>
             swipeignore <?=$arParams['POSITION']; ?> slider-solution" data-plugin-options='<?=Bitrix\Main\Web\Json::encode($arOptions); ?>'>
                <div class="swiper-wrapper">
    <?endif; ?>
        <?foreach($arResult['ITEMS'] as $i => $arItem):?>
            <?php
                // edit/add/delete buttons for edit mode
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), ['CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]);

            // show preview picture?
            $bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
            $imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : false);
            ?>
            <div class="swiper-slide banner  item <?=$arItem['PROPERTIES']['SIZING']['VALUE_XML_ID']; ?> <?=$arParams['POSITION']; ?> <?= $arItem['PROPERTIES']['HIDDEN_SM']['VALUE_XML_ID'] == 'Y' ? 'hidden-sm' : ''; ?> <?= $arItem['PROPERTIES']['HIDDEN_XS']['VALUE_XML_ID'] == 'Y' ? 'hidden-xs' : ''; ?>" <?= $arItem['PROPERTIES']['BGCOLOR']['VALUE'] ? ' style=" background:'.$arItem['PROPERTIES']['BGCOLOR']['VALUE'].';"' : ''; ?> id="<?=$this->GetEditAreaId($arItem['ID']); ?>">
                <?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
                    <a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']; ?>" <?= $arItem['PROPERTIES']['TARGET']['VALUE_XML_ID'] ? "target='".$arItem['PROPERTIES']['TARGET']['VALUE_XML_ID']."'" : ''; ?>>
                <?endif; ?>
                    <?if($arParams['SLIDER_MODE'] === 'Y'):?>
                        <span class="lazy set-position center" data-src="<?=$imageSrc; ?>" title="<?=$arItem['NAME']; ?>" style="background-image:url('<?=Aspro\Functions\CAsproMax::showBlankImg($imageSrc); ?>')"></span>
                    <?else:?>
                        <img src="<?=$imageSrc; ?>" alt="<?=$arItem['NAME']; ?>" title="<?=$arItem['NAME']; ?>" class="<?=$arItem['PROPERTIES']['SIZING']['VALUE_XML_ID'] == 'CROP' ? '' : 'img-responsive'; ?>" />
                    <?endif; ?>
                <?if($arItem['PROPERTIES']['LINK']['VALUE']):?>
                    </a>
                <?endif; ?>
            </div>
        <?endforeach; ?>
    <?if($sliderMode && $arParams['SHOW_ALL_ELEMENTS'] != 'N'):?>
                </div>
            </div>
            <?if($needWrap):?></div><?endif; ?>

            <?if (count($arResult['ITEMS']) > 1):?>
                <?php
                TSolution\Functions::showBlockHtml([
                    'FILE' => 'ui/slider-pagination.php',
                    'PARAMS' => [
                        'CLASSES' => 'swiper-pagination--small',
                        'STYLE' => $arParams['MENU_BANNER'] ? '--swiper-pagination-bottom: -23px;' : '',
                    ],
                ]);
                ?>
                <?php
                TSolution\Functions::showBlockHtml([
                    'FILE' => 'ui/slider-navigation.php',
                ]);
                ?>
            <?endif; ?>
        </div>
    <?endif; ?>
<?endif; ?>
