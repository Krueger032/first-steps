<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) {
    exit();
}

//options from TSolution\Functions::showBlockHtml
$arOptions = $arConfig['PARAMS'];
?>
<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" hidden>
    <meta itemprop="ratingValue" content="<?=$arOptions['ratingValue']?>" />
    <meta itemprop="reviewCount" content="<?=$arOptions['reviewCount']?>" />
    <meta itemprop="bestRating" content="5" />
    <meta itemprop="worstRating" content="1" />
</div>
