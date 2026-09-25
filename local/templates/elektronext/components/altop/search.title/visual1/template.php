<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if($arParams["SHOW_INPUT"] !== "N") {?>
	<div id="<?=$CONTAINER_ID?>">
		<form action="<?=$arResult['FORM_ACTION']?>">			
			<input type="text" name="q" id="<?=$INPUT_ID?>" maxlength="50" autocomplete="off" placeholder="<?=GetMessage('CT_BST_SEARCH')?>" value="" />
			<span class="title-search-icon"><i class="icon-search"></i></span>
		</form>
	</div>
<?}?>


<script>
	BX.ready(function(){
		new JCTitleSearch({
			'WAIT_IMAGE': '<div><span></span></div>',
			'AJAX_PAGE' : '<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
			'CONTAINER_ID': '<?=$CONTAINER_ID?>',
			'INPUT_ID': '<?=$INPUT_ID?>',
			'MIN_QUERY_LEN': 3
		});
	});
</script>

