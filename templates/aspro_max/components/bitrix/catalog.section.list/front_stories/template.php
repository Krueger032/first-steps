<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true );?>
<?if($arResult['SECTIONS']):?>
    <?
    $viewType = $arParams['VIEW_TYPE'];
    $sectionIndex = 0;
    $arParams['SORT'] = $arParams['SORT'] ? $arParams['SORT'] : 'SORT';
    $arParams['SORT_ORDER'] = $arParams['SORT_ORDER'] ? $arParams['SORT_ORDER'] : 'ASC';
    $arParams['SORT_2'] = $arParams['SORT_2'] ? $arParams['SORT_2'] : 'ID';
    $arParams['SORT_ORDER_2'] = $arParams['SORT_ORDER_2'] ? $arParams['SORT_ORDER_2'] : 'ASC';
    $bShowTitle = ($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"]) && $arParams["TITLE_BLOCK_SHOW"] != 'N';
    ?>
    <div class="content_wrapper_block front_stories <?=$viewType?> <?=$bShowTitle ? '' : 'no-title'?>" data-sort=<?=$arParams['SORT']?> data-sort-order=<?=$arParams['SORT_ORDER']?> data-sort2=<?=$arParams['SORT_2']?> data-sort2-order=<?=$arParams['SORT_ORDER_2']?> >

        <?if($arParams['FRONT_PAGE'] == 'Y'):?>
            <div class="maxwidth-theme only-on-front">

            <?if($bShowTitle):?>
                <div class="top_block">
                <?=Aspro\Functions\CAsproMax::showTitleH($arParams["TITLE_BLOCK"], 'title_block');?>
                    <a href="<?=SITE_DIR.$arParams["ALL_URL"];?>" class="pull-right font_upper muted"><?=$arParams["TITLE_BLOCK_ALL"] ;?></a>
                </div>
            <?endif;?>
        <?endif;?>

            <div class="tab_slider_wrapp stories swiper-nav-offset">
                <?
                $arOptions = [
                    'preloadImages' => false,
                    'keyboard' => true,
                    'init' => false,
                    // 'rewind'=> true,
                    'freeMode' => ['enabled' => true, 'momentum' => true],
                    'slidesPerView' => 'auto',
                    'spaceBetween' => 32,
                    'pagination' => false,
                    'touchEventsTarget' => 'container',
                    'type' => 'main_sections',
                    'breakpoints' => [
                        '601' => [
                            'slidesPerView' => 4,
                            'freeMode' => false,
                        ],
                        '768' => [
                            'slidesPerView' => 5,
                            'freeMode' => false,
                        ],
                        '992' => [
                            'slidesPerView' => 6,
                            'freeMode' => false,
                        ],
                        '1200' => [
                            'slidesPerView' => ($viewType == 'ROUND' ? '8' : '7'),
                            'freeMode' => false,
                        ],
                    ],
                ];
                ?>

                <div class="swiper slider-solution swipeignore appear-block loading_state"  data-plugin-options='<?= Bitrix\Main\Web\Json::encode($arOptions); ?>'>
                    <div class="swiper-wrapper no-shrinked">
                        <?foreach($arResult['SECTIONS'] as $arSection):
                            if($arParams["COUNT_ELEMENTS"] && !$arSection['ELEMENT_CNT']) {
                                continue;
                            }
                            $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
                            $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_SECTION_DELETE_CONFIRM')));?>
                            <div class="swiper-slide item <?=($viewType == 'ROUND' ? 'color-theme-hover' : '')?>" id="<?=$this->GetEditAreaId($arSection['ID']);?>" data-iblock-id=<?=$arParams['IBLOCK_ID']?> data-section-id=<?=$arSection['ID']?> data-index=<?=$sectionIndex?> >
                                <div class="img">
                                    <?if($arSection["PICTURE"]["SRC"]):?>
                                        <span class="lazy" data-src="<?=$arSection["PICTURE"]["SRC"]?>" style="background-image:url(<?=\Aspro\Functions\CAsproMax::showBlankImg($arSection["PICTURE"]["SRC"]);?>)"></span>
                                    <?endif;?>
                                </div>
                                <div class="name font_xs">
                                    <?=$arSection['NAME'];?>
                                </div>
                            </div>
                            <?$sectionIndex++;?>
                        <?endforeach;?>
                    </div>
                </div>
                <?php
                TSolution\Functions::showBlockHtml([
                    'FILE' => 'ui/slider-navigation.php',
                ]);
                ?>
            </div>

        <?if($arParams['FRONT_PAGE'] == 'Y'):?>
            </div>
        <?endif;?>
    </div>
<?endif;?>
