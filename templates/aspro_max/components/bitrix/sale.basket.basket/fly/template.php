<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

// update basket counters
Bitrix\Main\Loader::includeModule('aspro.max');

if (!include_once ($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/vendor/php/solution.php')) {
    throw new SystemException('Error include solution constants');
}

$catalogIblockID = TSolution::getFrontParametrValue('CATALOG_IBLOCK_ID');
$normalCount = count($arResult['ITEMS']['AnDelCanBuy']);
$delayCount = count($arResult['ITEMS']['DelDelCanBuy']);
$subscribeCount = count($arResult['ITEMS']['ProdSubscribe']);
$naCount = count($arResult['ITEMS']['nAnCanBuy']);

if (
    isset($_SESSION['CATALOG_COMPARE_LIST'][$catalogIblockID]['ITEMS'])
    && is_array($_SESSION['CATALOG_COMPARE_LIST'][$catalogIblockID]['ITEMS'])
) {
    $compareCount = count($_SESSION['CATALOG_COMPARE_LIST'][$catalogIblockID]['ITEMS']);
} else {
    $compareCount = 0;
}

$arParamsExport = $arParams;
unset($arParamsExport['INNER']);
$paramsString = urlencode(serialize($arParamsExport));

$title_basket = ($normalCount ? GetMessage('BASKET_COUNT', ['#PRICE#' => $arResult['allSum_FORMATED']]) : GetMessage('EMPTY_BLOCK_BASKET'));

$arCounters = TSolution::updateBasketCounters(['READY' => ['COUNT' => $normalCount, 'TITLE' => $title_basket, 'HREF' => $arParams['PATH_TO_BASKET']]]);

$arParams['INNER'] = $arParams['INNER'] ?? false;
?>
<?if ($arParams['INNER'] !== true && $_SERVER['REQUEST_METHOD'] !== 'POST'):?>
    <div class="basket_fly loaded<?if (strlen($arResult['ERROR_MESSAGE']) > 0):?> basket_empty<?endif;?>">
<?endif;?>
    <div class="wrap_cont">
    <?$frame = $this->createFrame()->begin('');?>
        <input type="hidden" name="total_price" value="<?=$arResult['allSum_FORMATED'];?>" />
        <input type="hidden" name="total_discount_price" value="<?=$arResult['allSum_FORMATED'];?>" />
        <input type="hidden" name="total_count" value="<?=$normalCount;?>" />
        <input type="hidden" name="delay_count" value="<?=$delayCount;?>" />

        <div class="opener">
            <div title="<?=$arCounters['READY']['TITLE'];?>" data-type="AnDelCanBuy" class="colored_theme_hover_text basket_count small clicked<?=!$arCounters['READY']['COUNT'] ? ' empty' : '';?>">
                <a href="<?=$arCounters['READY']['HREF'];?>"></a>
                <div class="wraps_icon_block basket">
                    <?=TSolution::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/header_icons_srite.svg#basket', 'down ', ['WIDTH' => 20, 'HEIGHT' => 16]);?>
                    <div class="count<?=!$arCounters['READY']['COUNT'] ? ' empty_items' : '';?>">
                        <span class="colored_theme_bg">
                            <span class="items">
                                <span class="colored_theme_bg"><?=$arCounters['READY']['COUNT'];?></span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <?if (TSolution::GetFrontParametrValue('CATALOG_DELAY') != 'N'):?>
                <div title="<?=$arCounters['FAVORITE']['TITLE'];?>" class="colored_theme_hover_text wish_count small<?=!$arCounters['FAVORITE']['COUNT'] ? ' empty' : '';?>">
                    <a href="<?=$arCounters['FAVORITE']['HREF'];?>" class="delay"></a>
                    <div class="wraps_icon_block delay">
                        <?=TSolution::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/header_icons_srite.svg#chosen', 'down ', ['WIDTH' => 20, 'HEIGHT' => 16]);?>
                        <div class="count basket-link delay<?=!$arCounters['FAVORITE']['COUNT'] ? ' empty_items' : '';?>">
                            <span class="colored_theme_bg">
                                <span class="items">
                                    <span class="js-count"><?=$arCounters['FAVORITE']['COUNT'];?></span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            <?endif;?>
            <?if (TSolution::GetFrontParametrValue('CATALOG_COMPARE') != 'N'):?>
                <div title="<?=$arCounters['COMPARE']['TITLE'];?>" class="colored_theme_hover_text compare_count small">
                    <a href="<?=$arCounters['COMPARE']['HREF'];?>"></a>
                    <div id="compare_fly" class="wraps_icon_block compare <?=!$arCounters['COMPARE']['COUNT'] ? ' empty_block' : '';?>">
                        <?=TSolution::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/header_icons_srite.svg#compare', 'down ', ['WIDTH' => 18, 'HEIGHT' => 17]);?>
                        <div class="count<?=!$arCounters['COMPARE']['COUNT'] ? ' empty_items' : '';?>">
                            <span class="colored_theme_bg">
                                <span class="items">
                                    <span class="js-count"><?=$arCounters['COMPARE']['COUNT'];?></span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            <?endif;?>
            <?=TSolution\Functions::showSideFormLinkIcons();?>
        </div>
        <script src="<?=$APPLICATION->oAsset->getFullAssetPath($templateFolder.'/script.js');?>" type="text/javascript"></script>

        <?php
        include $_SERVER['DOCUMENT_ROOT'].$templateFolder.'/functions.php';
        $arUrls = ['delete' => SITE_DIR.'ajax/show_basket_fly.php?action=delete&id=#ID#',
            'delay' => SITE_DIR.'ajax/show_basket_fly.php?action=delay&id=#ID#',
            'add' => SITE_DIR.'ajax/show_basket_fly.php?action=add&id=#ID#'];

        ?>

        <?php
        $arMenu = [['ID' => 'AnDelCanBuy', 'TITLE' => GetMessage('SALE_BASKET_ITEMS'), 'COUNT' => $normalCount, 'FILE' => '/basket_items.php']];
        if ($delayCount) {
            $arMenu[] = ['ID' => 'DelDelCanBuy', 'TITLE' => GetMessage('SALE_BASKET_ITEMS_DELAYED'), 'COUNT' => $delayCount, 'FILE' => '/basket_items_delayed.php'];
        }
        // if ($subscribeCount) { $arMenu[] = array("ID"=>"ProdSubscribe", "TITLE"=>GetMessage("SALE_BASKET_ITEMS_SUBSCRIBED"), "COUNT"=>$subscribeCount, "FILE"=>"/basket_items_subscribed.php"); }
        if ($naCount) {
            $arMenu[] = ['ID' => 'nAnCanBuy', 'TITLE' => GetMessage('SALE_BASKET_ITEMS_NOT_AVAILABLE'), 'COUNT' => $naCount, 'FILE' => '/basket_items_not_available.php'];
        }

        ?>
        <div class="basket_sort">
            <div class="basket_title"><a href="<?=$arParams['PATH_TO_BASKET'];?>" class="dark-color basket-link option-font-bold"><?=GetMessage('BASKET_TITLE');?></a></div>
            <?if (count($arMenu) > 1):?>
                <ul class="tabs">
                    <?if (strlen($arResult['ERROR_MESSAGE']) <= 0) {?>
                        <?foreach($arMenu as $key => $arElement) {?>
                            <li<?=$arElement['SELECTED'] ? ' class="cur"' : '';?> item-section="<?=$arElement['ID'];?>" data-type="<?=$arElement['ID'];?>">
                                <div class="wrap_li">
                                    <span><?=$arElement['TITLE'];?></span>
                                    <span class="quantity">&nbsp;(<span class="count"><?=$arElement['COUNT'];?></span>)</span>
                                </div>
                            </li>
                        <?}?>
                    <?}?>
                </ul>
            <?endif;?>

            <?=TSolution::showIconSvg('close colored_theme_hover_text', SITE_TEMPLATE_PATH.'/images/svg/Close.svg', '', '', true, false);?>
        </div>
        <?$arError = TSolution::checkAllowDelivery($arResult['allSum'], CSaleLang::GetLangCurrency(SITE_ID));?>
        <?if (is_array($arResult['WARNING_MESSAGE']) && !empty($arResult['WARNING_MESSAGE'])):?>
            <div class="errors-basket-block">
                <?php foreach ($arResult['WARNING_MESSAGE'] as $v) {
                    echo ShowError($v);
                }?>
            </div>
        <?endif;?>
        <?if ($normalCount):?>
            <script src="<?=$APPLICATION->oAsset->getFullAssetPath(SITE_TEMPLATE_PATH.'/js/buy_services.js');?>" type="text/javascript"></script>
            <link rel="stylesheet" href="<?=$APPLICATION->oAsset->getFullAssetPath(SITE_TEMPLATE_PATH.'/css/bonus-system.css');?>"/>
            <link rel="stylesheet" href="<?=$APPLICATION->oAsset->getFullAssetPath(SITE_TEMPLATE_PATH.'/css/buy_services.css');?>"/>
        <?endif;?>

        <form method="post" action="<?=POST_FORM_ACTION_URI;?>" name="basket_form" id="basket_form" class="basket_wrapp">
            <?if (strlen($arResult['ERROR_MESSAGE']) <= 0):?>
                <ul class="tabs_content basket">
                    <?foreach($arMenu as $key => $arElement):?>
                        <li class="<?=$arElement['SELECTED'] ? ' cur' : '';?><?=$arError['ERROR'] ? ' min-price' : '';?>" item-section="<?=$arElement['ID'];?>">
                            <?include $_SERVER['DOCUMENT_ROOT'].$templateFolder.$arElement['FILE'];?>
                        </li>
                    <?endforeach;?>
                </ul>
            <?else:?>
                <ul class="tabs_content basket">
                    <li class="cur" item-section="AnDelCanBuy">
                        <?include $_SERVER['DOCUMENT_ROOT'].$templateFolder.'/basket_items.php';?>
                    </li>
                </ul>
            <?endif;?>
            <input id="fly_basket_params" type="hidden" name="PARAMS" value='<?=$paramsString;?>' />
        </form>

        <script>
            if (typeof updateBottomIconsPanel === 'function') {
                updateBottomIconsPanel(<?=CUtil::PhpToJSObject($arCounters);?>);
            }

            $(document).ready(() => {
                $("#basket_line .basket_fly").on('submit', (e) => {
                    e.preventDefault();
                });

                $('#basket_line .basket_fly a.apply-button').click(() => {
                    $('#basket_line .basket_fly form[name^=basket_form]').prepend('<input type="hidden" name="BasketRefresh" value="Y" />');

                    $.post(
                        `${arMaxOptions.SITE_DIR}basket/`,
                        $("#basket_line .basket_fly form[name^=basket_form]").serialize(),
                        $.proxy(function(data) {
                            $('#basket_line .basket_fly form[name^=basket_form] input[name=BasketRefresh]').remove();
                        })
                    );
                });

                $("#basket_line .basket_fly .tabs > li").on("click", function() {
                    $("#basket_line .basket_fly .tabs > li").removeClass("cur");
                    $("#basket_line .basket_fly .tabs_content > li").removeClass("cur");
                    $("#basket_line .basket_fly .tabs > li:eq("+$(this).index()+")").addClass("cur");
                    $("#basket_line .basket_fly .tabs_content > li:eq("+$(this).index()+")").addClass("cur");

                    $("#basket_line .basket_fly .opener > div").removeClass("cur");
                    $("#basket_line .basket_fly .opener > div:eq("+$(this).index()+")").addClass("cur");
                });

                $("#basket_line .basket_fly .back_button, #basket_line .basket_fly .svg-inline-close").on("click", () => {
                    $("#basket_line .basket_fly .opener > div.cur").trigger('click');
                    $("#basket_line .basket_fly .opener > div").removeClass("cur");
                    $('#basket_line .basket_fly').removeClass('swiped');
                });
            });


            <?if ($arParams['AJAX_MODE_CUSTOM'] == 'Y'):?>
                var animateRow = function(row) {
                    $(row).find("td.thumb-cell img").css({"maxHeight": "inherit", "maxWidth": "inherit"}).fadeTo(50, 0);
                    var columns = $(row).find("td");
                    $(columns).wrapInner('<div class="slide"></div>');
                    $(row).find(".summ-cell").wrapInner('<div class="slide"></div>');
                    setTimeout(function() {$(columns).animate({"paddingTop": 0, "paddingBottom": 0}, 50)}, 0);
                    $(columns).find(".slide").slideUp(333);
                }

                $("#basket_form").ready(() => {
                    $('form[name^=basket_form] .counter_block input[type=text]').change((e) => {
                        e.preventDefault();
                    });

                    $('.basket_action .remove_all_basket').click(function(e) {
                        if (!$(this).hasClass('disabled')) {
                            $(this).addClass('disabled');
                            delete_all_items($(this).data("type"), $(this).closest("li").attr("item-section"), 333);
                        }

                        $(this).removeClass('disabled');
                    })

                    $('form[name^=basket_form] .remove').unbind('click').click(function(e) {
                        e.preventDefault();

                        const row = $(this).parents(".item").first();
                        row.fadeTo(100 , 0.05, function() {});

                        deleteProduct(
                            $(this).parents(".item[data-id]").attr('data-id'),
                            $(this).parents("li").attr("item-section"),
                            $(this).parents(".item[data-id]").attr('product-id'),
                            $(this).parents(".item[data-id]")
                        );

                        markProductRemoveBasket($(this).parents(".item[data-id]").attr('product-id'));

                        return false;
                    });

                    $('form[name^=basket_form] .delay .action_item').unbind('click').click(function(e) {
                        e.preventDefault();

                        const row = $(this).parents(".item").first();

                        delayProduct(
                            $(this).parents(".item[data-id]").attr('data-id'),
                            $(this).parents("li").attr("item-section"),
                            $(this).parents(".item[data-id]")
                        );

                        row.fadeTo(100 , 0.05, function() {});

                        markProductDelay($(this).parents(".item[data-id]").attr('product-id'));
                    });

                    $('form[name^=basket_form] .add .action_item').unbind('click').click(function(e) {
                        e.preventDefault();

                        const basketId = $(this).parents(".item[data-id]").attr('data-id');
                        const row = $(this).parents(".item").first();

                        row.fadeTo(100 , 0.05, function() {});
                        addProduct(basketId, $(this).parents("li").attr("item-section"), $(this).parents(".item[data-id]"));
                        markProductAddBasket($(this).parents(".item[data-id]").attr('product-id'));
                    });
                });
            <?endif;?>
        </script>
        <?php
        if (Bitrix\Main\Loader::includeModule('currency')) {
            CJSCore::Init(['currency']);
            $currencyFormat = CCurrencyLang::GetFormatDescription(CSaleLang::GetLangCurrency(SITE_ID));
        }
        ?>
        <script type="text/javascript">
            <?if (is_array($currencyFormat)):?>
                function jsPriceFormat(_number) {
                    BX.Currency.setCurrencyFormat('<?=CSaleLang::GetLangCurrency(SITE_ID);?>', <?=CUtil::PhpToJSObject($currencyFormat, false, true);?>);

                    return BX.Currency.currencyFormat(_number, '<?=CSaleLang::GetLangCurrency(SITE_ID);?>', true);
                }
            <?endif;?>
        </script>
    <?$frame->end();?>
    </div>
<?if ($arParams['INNER'] !== true && $_SERVER['REQUEST_METHOD'] !== 'POST'):?>
    </div>
<?endif;?>
