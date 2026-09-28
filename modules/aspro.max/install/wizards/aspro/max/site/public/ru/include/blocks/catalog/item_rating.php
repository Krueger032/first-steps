<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?//$arOptions from \Aspro\Functions\CAsproPremier::showBlockHtml?>

<?
$arOptions = $arConfig['PARAMS'];
$arItem = $arConfig['ITEM'];
$ratingValue = $arItem['PROPERTIES']['EXTENDED_REVIEWS_RAITING']['VALUE'];
$reviewCount = $arItem['PROPERTIES']['EXTENDED_REVIEWS_COUNT']['VALUE'];
?>

<div class="blog-info__rating--top-info <?=$arOptions['ITEM_CLASSES']?>">

    <?\Aspro\Max\Scheme\Common::showAggregateRating($ratingValue, $reviewCount);?>

    <div class="votes_block nstar with-text">
        <div class="ratings">
            <?$message = $arItem['PROPERTIES']['EXTENDED_REVIEWS_COUNT']['VALUE'] ? GetMessage('VOTES_RESULT', ['#VALUE#' => $arItem['PROPERTIES']['EXTENDED_REVIEWS_RAITING']['VALUE']]) : GetMessage('VOTES_RESULT_NONE'); ?>
            <div class="inner_rating" title="<?=$message; ?>">
                <?for($i = 1; $i <= 5; ++$i):?>
                    <div class="item-rating <?=$i <= $arItem['PROPERTIES']['EXTENDED_REVIEWS_RAITING']['VALUE'] ? 'filed' : ''; ?>"><?=CMax::showIconSvg('star', SITE_TEMPLATE_PATH.'/images/svg/catalog/star_small.svg'); ?></div>
                <?endfor; ?>
            </div>
        </div>
    </div>
    <?if($arItem['PROPERTIES']['EXTENDED_REVIEWS_COUNT']['VALUE']):?>
        <span class="font_sxs"><?=$arItem['PROPERTIES']['EXTENDED_REVIEWS_COUNT']['VALUE']; ?></span>
    <?endif; ?>
</div>
