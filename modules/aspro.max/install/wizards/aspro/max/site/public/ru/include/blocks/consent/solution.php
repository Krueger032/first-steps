<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//options from Aspro\Functions\CAsproMax::showBlockHtml
$pathLicense = TSolution\Validation::getPathLicense();

if(!TSolution\Validation::checkContent($pathLicense)){
    return;
}

$arOptions = $arConfig['PARAMS'];
?>

<div class="licence_block filter onoff label_block">
    <?if(isset($arOptions['HIDDEN_ERROR']) && $arOptions['HIDDEN_ERROR'] === 'Y'):?>
        <label data-for="<?=$arOptions['INPUT_ID'];?>" class="hidden error"><?=GetMessage("ERROR_FORM_LICENSE");?></label>
    <?endif;?>
    <input type="checkbox" id="<?=$arOptions['INPUT_ID'];?>" name="<?=$arOptions['INPUT_NAME'];?>" <?=TSolution::GetFrontParametrValue('LICENCE_CHECKED') === 'Y' ? 'checked' : '';?> required value="Y">
    <label for="<?=$arOptions['INPUT_ID'];?>" class="license">
        <?include $pathLicense;?>
    </label>

   <?=TSolution\Validation::getFormField();?>
</div>

