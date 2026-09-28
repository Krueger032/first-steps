<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?
global $arTheme;
$countSldes = (is_array($arResult['ITEMS'])? count($arResult['ITEMS']) : '0');
$slideshowSpeed = abs(intval($arTheme['PARTNERSBANNER_SLIDESSHOWSPEED']['VALUE']));
$animationSpeed = abs(intval($arTheme['PARTNERSBANNER_ANIMATIONSPEED']['VALUE']));
$bAnimation = (bool)$slideshowSpeed && $countSldes>6;

?>
<?if($arResult['ITEMS']):?>
    <?$bShowTopBlock = ($arParams['TITLE_BLOCK'] || $arParams['TITLE_BLOCK_ALL']);
    $bBordered = ($arParams['BORDERED'] == 'Y');
    ?>
    <div class="content_wrapper_block front_brands_slider <?=$templateName;?>">
    <div class="maxwidth-theme only-on-front <?=($bShowTopBlock ? '' : 'no-title')?>">
        <?if($bShowTopBlock):?>
            <div class="top_block">
            <?=Aspro\Functions\CAsproMax::showTitleH($arParams['TITLE_BLOCK']);?>
                <?if($arParams['TITLE_BLOCK_ALL']):?>
                    <a href="<?=SITE_DIR.$arParams['ALL_URL'];?>" class="pull-right font_upper muted"><?=$arParams['TITLE_BLOCK_ALL'] ;?></a>
                <?endif;?>
            </div>
        <?endif;?>
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
            'spaceBetween' => ($bBordered ? 0 : 32),
            // 'pagination' => false,
            'type' => 'banners_in_header',
            'breakpoints' => [
                '601' => [
                    'slidesPerView' => 3,
                    'freeMode' => false,
                ],
                '768' => [
                    'slidesPerView' => 4,
                    'freeMode' => false,
                ],
                '992' => [
                    'slidesPerView' => 5,
                    'freeMode' => false,
                ],
                '1200' => [
                    'slidesPerView' => ($bBordered ? '5' : '6'),
                    'freeMode' => false,
                ],
            ],
        ];
        $arOptions['autoplay'] = [
            'delay' => $slideshowSpeed,
            'pauseOnMouseEnter' => true,
        ];
        $arOptions['speed'] = $animationSpeed;
        ?>
        <div class="swiper-nav-offset relative">
            <div class="item-views brands appear-block loading_state swiper slider-solution swipeignore <?=($bBordered ? 'with_border':'')?> brands_slider slides"  data-plugin-options='<?= Bitrix\Main\Web\Json::encode($arOptions); ?>'>
                <div class="swiper-wrapper no-shrinked">
                    <?foreach($arResult["ITEMS"] as $arItem){?>
                        <?
                            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                        ?>
                        <?if( is_array($arItem["PREVIEW_PICTURE"]) ){?>
                            <div class="swiper-slide visible item pull-left text-center <?=($bBordered ? 'bordered' : '')?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                                    <img class="noborder lazy" data-src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" src="<?=\Aspro\Functions\CAsproMax::showBlankImg($arItem["PREVIEW_PICTURE"]["SRC"]);?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
                                </a>
                            </div>
                        <?}?>
                    <?}?>
                </div>
            </div>
            <?php
            TSolution\Functions::showBlockHtml([
                'FILE' => 'ui/slider-navigation.php',
            ]);
            ?>
        </div>
    </div></div>
<?endif;?>
