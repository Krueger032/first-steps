<?php

namespace Aspro\Max\Scheme;

class Common
{
    public static function showAggregateRating($ratingValue, $reviewCount)
    {
        $ratingValue = str_replace(',', '.', $ratingValue);
        $ratingValue = is_numeric($ratingValue) && $ratingValue > 0 ? min(5, $ratingValue) : 5;
        $ratingValue = (float) number_format($ratingValue, 1, '.', '');

        $reviewCount = is_numeric($reviewCount) ? max(1, (int)$reviewCount) : 1;

        \TSolution\Functions::showBlockHtml([
            'FILE' => '/scheme/aggregate_rating.php',
            'PARAMS' => [
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
            ]
        ]);
    }
}
