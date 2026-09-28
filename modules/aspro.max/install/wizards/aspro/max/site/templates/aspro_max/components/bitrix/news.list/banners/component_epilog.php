<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}?>

<?if ($templateData['ITEMS'] && ($arParams['SLIDER_MODE'] === 'Y' || $arParams['MENU_BANNER'])):?>
    <?Aspro\Max\Functions\Extensions::init('swiper'); ?>
<?endif; ?>
