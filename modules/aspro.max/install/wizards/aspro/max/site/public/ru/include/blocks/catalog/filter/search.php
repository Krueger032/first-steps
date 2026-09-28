<div class="filter-search relative">
    <input type="text" class="filter-search-values filter-search__input width100" name="filter-search-values" data-text-input placeholder="<?=GetMessage("SEARCH_TITLE")?>" autocomplete="off" />
    <div class="filter-search__icons flexbox justify-center">
        <div class="filter-search__icon-search"><?=CMax::showSpriteIconSvg(SITE_TEMPLATE_PATH."/images/svg/header_icons_srite.svg#search", "filter-search__icon ", ['WIDTH' => 17,'HEIGHT' => 17]);?>
        </div>
        <button type="button" class="filter-search__close colored_theme_hover_text"><?=CMax::showSpriteIconSvg(SITE_TEMPLATE_PATH."/images/svg/header_icons_srite.svg#close", "filter-search__icon filter-search__icon--close ", ['WIDTH' => 13,'HEIGHT' => 13]);?></button>
    </div>
</div>
<div class="filter-search-empty-title hidden font_13 muted888">
    <?=GetMessage("SEARCH_VALUES_EMPTY_TITLE")?>
</div>
