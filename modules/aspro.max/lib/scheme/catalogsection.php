<?php

namespace Aspro\Max\Scheme;

use CMax as Solution,
    Aspro\Max\Utils as SolutionUtils;

class CatalogSection
{
    protected string $name;
    protected string $url;
    protected string $imageSrc;
    protected string $currency;
    protected string $description;

    public function __construct(array $section)
    {
        global $APPLICATION;

        $imageId = $section['PICTURE'] ?: $section['DETAIL_PICTURE'] ?: $section['UF_CATALOG_ICON'];
        $currencyDefault = \Bitrix\Main\Config\Option::get("sale", "default_currency");
        $defaultImgPath = SITE_TEMPLATE_PATH.'/images/svg/noimage_product.svg';

        $this->name = htmlspecialcharsbx($section['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'] ? $section['IPROPERTY_VALUES']['SECTION_PAGE_TITLE']: $section['NAME']);
        $this->url = SolutionUtils::getCurrentUrl();
        $this->imageSrc = $imageId ? SolutionUtils::getAbsolutePath(\CFile::GetPath($imageId)) : SolutionUtils::getAbsolutePath($defaultImgPath);
        $this->currency = Solution::GetFrontParametrValue('CONVERT_CURRENCY') === 'Y' ? Solution::GetFrontParametrValue('CURRENCY_ID') :  $currencyDefault;
        $this->description = htmlspecialcharsbx($section['IPROPERTY_VALUES']['SECTION_META_DESCRIPTION'] ?: $section['UF_SECTION_DESCR']) ?: $section['NAME'];
        $this->imageAlt = htmlspecialcharsbx($section['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT']) ?: $section['NAME'];
    }

    public function show()
    {
        global $APPLICATION;

        $ratingValue = Solution::GetFrontParametrValue('AVERAGE_RATING');
        $reviewCount = Solution::GetFrontParametrValue('NUMBER_OF_REVIEWS');

        ?>
        <meta itemprop="name" content="<?=$this->name?>" />
        <link itemprop="url" href="<?=$this->url?>" />
        <meta itemprop="description" content="<?=$this->description?>" />
        <img itemprop="image" src="<?=$this->imageSrc?>" alt="<?=$this->imageAlt?>" data-src="" hidden />
        <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" hidden>
            <meta itemprop="priceCurrency" content="<?=htmlspecialchars($this->currency)?>" />
        </div>
        <?\Aspro\Max\Scheme\Common::showAggregateRating($ratingValue, $reviewCount);?>
        <?php
    }
}
