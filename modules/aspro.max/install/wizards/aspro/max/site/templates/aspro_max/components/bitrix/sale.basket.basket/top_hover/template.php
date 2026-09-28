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

if (is_array($_SESSION['CATALOG_COMPARE_LIST'][$catalogIblockID]['ITEMS'])) {
    $compareCount = count($_SESSION['CATALOG_COMPARE_LIST'][$catalogIblockID]['ITEMS']);
} else {
    $compareCount = 0;
}

$arParamsExport = $arParams;
$paramsString = urlencode(serialize($arParamsExport));

$title_basket = ($normalCount ? GetMessage('BASKET_COUNT', ['#PRICE#' => $arResult['allSum_FORMATED']]) : GetMessage('EMPTY_BLOCK_BASKET'));

$arCounters = TSolution::updateBasketCounters(['READY' => ['COUNT' => $normalCount, 'TITLE' => $title_basket, 'HREF' => $arParams['PATH_TO_BASKET']]]);
?>

<div class="wrap_cont">
    <?$frame = $this->createFrame()->begin('');?>
        <input type="hidden" name="total_price" value="<?=$arResult['allSum_FORMATED'];?>" />
        <input type="hidden" name="total_discount_price" value="<?=$arResult['allSum_FORMATED'];?>" />
        <input type="hidden" name="total_count" value="<?=$normalCount;?>" />

        <?if ($_POST['firstTime']):?>
            <script src="<?=((COption::GetOptionString('main', 'use_minified_assets', 'N', $siteID) === 'Y') && file_exists($_SERVER['DOCUMENT_ROOT'].$templateFolder.'/script.min.js')) ? $templateFolder.'/script.min.js' : $templateFolder.'/script.js';?>" type="text/javascript"></script>
        <?endif;?>
        <?php
        include $_SERVER['DOCUMENT_ROOT'].$templateFolder.'/functions.php';
        $arUrls = [
            'delete' => SITE_DIR.'ajax/show_basket_fly.php?action=delete&id=#ID#',
            'delay' => SITE_DIR.'ajax/show_basket_fly.php?action=delay&id=#ID#',
            'add' => SITE_DIR.'ajax/show_basket_fly.php?action=add&id=#ID#'
        ];

        if (is_array($arResult['WARNING_MESSAGE']) && !empty($arResult['WARNING_MESSAGE'])) {
            foreach ($arResult['WARNING_MESSAGE'] as $v) {
                echo ShowError($v);
            }
        }

        $arMenu = [['ID' => 'AnDelCanBuy', 'TITLE' => GetMessage('SALE_BASKET_ITEMS'), 'COUNT' => $normalCount, 'FILE' => '/basket_items.php']];
        ?>

        <?$arError = TSolution::checkAllowDelivery($arResult['allSum'], CSaleLang::GetLangCurrency(SITE_ID));?>
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

            <?if ($arParams['AJAX_MODE_CUSTOM'] == 'Y'):?>
                var animateRow = (row) => {
                    const columns = $(row).find("td");

                    $(row).find("td.thumb-cell img").css({
                        "maxHeight": "inherit",
                        "maxWidth": "inherit",
                    }).fadeTo(50, 0);

                    $(columns).wrapInner('<div class="slide"></div>');

                    $(row).find(".summ-cell").wrapInner('<div class="slide"></div>');

                    setTimeout(() => {
                        $(columns).animate({
                            "paddingTop": 0,
                            "paddingBottom": 0,
                        }, 50);
                    }, 0);

                    $(columns).find(".slide").slideUp(333);
                }

                $("#basket_form").ready(() => {
                    $('form[name^=basket_form] .counter_block input[type=text]').change((e) => {
                        e.preventDefault();
                    });

                    $('.basket_action .remove_all_basket').click(function(e) {
                        e.preventDefault();

                        if (!$(this).hasClass('disabled')) {
                            $(this).addClass('disabled');
                            delete_all_items($(this).data("type"), $(this).closest("li").attr("item-section"), 333);
                        }

                        $(this).removeClass('disabled');
                        reloadBasketCounters();
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
