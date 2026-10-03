<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$this->addExternalCss("/bitrix/components/altop/add.review.enext/templates/slide_panel/style.min.css");

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/jquery.filer/jquery.filer.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/jquery.filer/jquery.filer.min.js");

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.pack.js");

if(!empty($arResult["NAV_RESULT"])) {
	$navParams =  array(
		"NavPageCount" => !empty($arResult["NAV_RESULT"]->NavPageCount) ? $arResult["NAV_RESULT"]->NavPageCount : 1,
		"NavPageNomer" => !empty($arResult["NAV_RESULT"]->NavPageNomer) ? $arResult["NAV_RESULT"]->NavPageNomer : 1,
		"NavNum" => !empty($arResult["NAV_RESULT"]->NavNum) ? $arResult["NAV_RESULT"]->NavNum : $this->randString()
	);
} else {
	$navParams = array(
		"NavPageCount" => 1,
		"NavPageNomer" => 1,
		"NavNum" => $this->randString()
	);
}

$showLazyLoad = false;
if($arParams["NEWS_COUNT"] > 0 && $navParams["NavPageCount"] > 1) {		
	$showLazyLoad = $navParams["NavPageNomer"] != $navParams["NavPageCount"];
}

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("REVIEWS_ITEM_DELETE_CONFIRM"));

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$parentContainerName = "reviews-".$obName;
$containerName = "container-".$navParams["NavNum"];?>

<div class="row reviews" id="<?=$parentContainerName?>">	
	<div class="col-xs-12 col-md-9">
		<div class="row">
			<div class="hidden-xs hidden-sm col-xs-3"></div>
			<div class="col-xs-12 col-md-9">
				<div class="reviews-evaluation">
					<div class="reviews-evaluation-title"><?=Loc::getMessage("REVIEWS_EVALUATION_TITLE")?></div>
					<div class="reviews-evaluation-stars">
						<?foreach($arResult["RATING_LIST"] as $ratingId => $arRating) {?>
							<i class="icon-star-s reviews-evaluation-star" data-rating-id="<?=$ratingId?>" data-value="<?=$arRating['VALUE']?>"></i>
						<?}
						unset($ratingId, $arRating);?>
					</div>
					<div class="hidden-md reviews-evaluation-val"><?=Loc::getMessage("REVIEWS_EVALUATION_VALUE")?></div>
				</div>
			</div>
		</div>
		<?if(!empty($arResult["ITEMS"])) {?>
			<script type="text/javascript">	
				BX.message({
					REVIEWS_TEMPLATE_PATH: '<?=CUtil::JSEscape($templateFolder)?>'
				});
			</script>
			<div class="reviews-items-container">
				<!-- items-container -->
				<div class="reviews-items" data-entity="<?=$containerName?>">				
					<?foreach($arResult["ITEMS"] as $arItem) {
						$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
						$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);

						$strMainID = $this->GetEditAreaId($arItem["ID"]);
						$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>
						
						<div class="row reviews-item" id="<?=$strMainID?>" data-entity="item" itemprop="review" itemscope itemtype="http://schema.org/Review">			
							<div class="col-xs-12 col-md-3">
								<div class="reviews-item-user-name" itemprop="author"><?=(!empty($arItem["USER_NAME"]) ? $arItem["USER_NAME"] : $arItem["DISPLAY_PROPERTIES"]["NAME"]["VALUE"])?></div>
								<?if(!empty($arItem["ACTIVE_FROM"])) {?>
									<div class="reviews-item-date" itemprop="datePublished" content="<?=$arItem['DATE_PUBLISHED']?>"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
								<?}
								if(!empty($arItem["DISPLAY_PROPERTIES"]["CITY"]["VALUE"])) {?>
									<div class="reviews-item-user-city"><?=$arItem["DISPLAY_PROPERTIES"]["CITY"]["VALUE"]?></div>
								<?}?>
							</div>
							<div class="col-xs-12 col-md-9">
								<div class="reviews-item-caption">
									<div class="reviews-item-rating-container">
										<div class="reviews-item-rating">
											<div class="reviews-item-stars">
												<?foreach($arResult["RATING_LIST"] as $arRating) {?>
													<i class="icon-star-s reviews-item-star<?=($arItem['DISPLAY_PROPERTIES']['RATING']['VALUE_XML_ID'] < $arRating['XML_ID'] ? ' reviews-item-star-empty' : '')?>"></i>
												<?}
												unset($arRating);?>
											</div>
											<div class="reviews-item-term"><?=$arItem["DISPLAY_PROPERTIES"]["TERM"]["VALUE"].(!empty($arItem["DISPLAY_PROPERTIES"]["TERM"]["HINT"]) ? " ".$arItem["DISPLAY_PROPERTIES"]["TERM"]["HINT"] : "")?></div>
										</div>
										<div class="reviews-item-likes<?=($arItem['DISPLAY_PROPERTIES']['LIKES']['VALUE'] < 1 ? ' reviews-item-likes-empty' : '')?>"><?=($arItem["DISPLAY_PROPERTIES"]["LIKES"]["VALUE"] > 0 ? "+" : "").$arItem["DISPLAY_PROPERTIES"]["LIKES"]["VALUE"]?></div>
									</div>
									<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
										<?$firstRating = reset($arResult["RATING_LIST"]);
										$lastRating = end($arResult["RATING_LIST"]);?>
										<meta itemprop="worstRating" content="<?=$firstRating['XML_ID']?>" />
										<meta itemprop="ratingValue" content="<?=$arItem['DISPLAY_PROPERTIES']['RATING']['VALUE_XML_ID']?>" />
										<meta itemprop="bestRating" content="<?=$lastRating['XML_ID']?>" />
										<?unset($lastRating, $firstRating);?>
									</span>
									<?if(!empty($arItem["DISPLAY_PROPERTIES"]["COMMENT"]["VALUE"]["TEXT"])) {?>
										<div class="reviews-item-user-text" itemprop="description"><?=$arItem["DISPLAY_PROPERTIES"]["COMMENT"]["VALUE"]["TEXT"]?></div>
									<?}
									if(!empty($arItem["DISPLAY_PROPERTIES"]["ADVANTAGES"]["VALUE"]["TEXT"]) || !empty($arItem["DISPLAY_PROPERTIES"]["DEFECTS"]["VALUE"]["TEXT"])) {?>
										<div class="reviews-item-user-props">								
											<div class="reviews-item-user-prop">
												<div class="reviews-item-user-prop-title"><?=$arItem["PROPERTIES"]["ADVANTAGES"]["NAME"]?>:</div>
												<div class="reviews-item-user-prop-text"><?=$arItem["DISPLAY_PROPERTIES"]["ADVANTAGES"]["VALUE"]["TEXT"]?></div>
											</div>								
											<div class="reviews-item-user-prop">
												<div class="reviews-item-user-prop-title"><?=$arItem["PROPERTIES"]["DEFECTS"]["NAME"]?>:</div>
												<div class="reviews-item-user-prop-text"><?=$arItem["DISPLAY_PROPERTIES"]["DEFECTS"]["VALUE"]["TEXT"]?></div>
											</div>
										</div>
									<?}
									if(!empty($arItem["DISPLAY_PROPERTIES"]["FILES"]["FILE_VALUE"])) {?>
										<div class="reviews-item-photos">
											<?if(isset($arItem["DISPLAY_PROPERTIES"]["FILES"]["FILE_VALUE"]["ID"])) {?>
												<a class="reviews-item-photo fancyimage" title="<?=$arResult['ELEMENT_NAME']?>" href="<?=$arItem['DISPLAY_PROPERTIES']['FILES']['FILE_VALUE']['SRC']?>" data-fancybox-group="gallery"><img src="<?=$arItem['DISPLAY_PROPERTIES']['FILES']['PREVIEW']['SRC']?>" width="<?=$arItem['DISPLAY_PROPERTIES']['FILES']['PREVIEW']['WIDTH']?>" height="<?=$arItem['DISPLAY_PROPERTIES']['FILES']['PREVIEW']['HEIGHT']?>" alt="<?=$arResult['ELEMENT_NAME']?>" /></a>
											<?} else {
												foreach($arItem["DISPLAY_PROPERTIES"]["FILES"]["FILE_VALUE"] as $key => $val) {?>
													<a class="reviews-item-photo fancyimage" title="<?=$arResult['ELEMENT_NAME']?>" href="<?=$val['SRC']?>" data-fancybox-group="gallery"><img src="<?=$arItem['DISPLAY_PROPERTIES']['FILES']['PREVIEW'][$key]['SRC']?>" width="<?=$arItem['DISPLAY_PROPERTIES']['FILES']['PREVIEW'][$key]['WIDTH']?>" height="<?=$arItem['DISPLAY_PROPERTIES']['FILES']['PREVIEW'][$key]['HEIGHT']?>" alt="<?=$arResult['ELEMENT_NAME']?>" /></a>
												<?}
												unset($key, $val);
											}?>
										</div>
									<?}
									if(!empty($arItem["DETAIL_TEXT"])) {?>
										<div class="reviews-item-store">
											<div class="reviews-item-store-title"><?=Loc::getMessage("REVIEWS_ITEM_STORE_TITLE")?></div>
											<div class="reviews-item-store-text"><?=$arItem["DETAIL_TEXT"]?></div>
										</div>
									<?}?>						
									<div class="reviews-item-like" data-entity="like"><i class="icon-heart-b" data-entity="like-icon"></i><span><?=Loc::getMessage("REVIEWS_ITEM_LIKE")?></span></div>
								</div>
							</div>
						</div>
						<?$arJSParams = array(				
							"ITEM" => array(
								"IBLOCK_ID" => $arItem["IBLOCK_ID"],
								"ID" => $arItem["ID"]
							),
							"VISUAL" => array(
								"ID" => $strMainID
							)
						);?>
						<script type="text/javascript">
							var <?=$strObName;?> = new JCNewsListReviews(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
						</script>
					<?}?>				
				</div>
				<!-- items-container -->

				<?if($showLazyLoad) {?>
					<!-- show-more-container -->
					<div class="reviews-more" data-entity="reviews-show-more-container">
						<div class="row">
							<div class="hidden-xs hinnen-sm col-xs-3"></div>
							<div class="col-xs-12 col-md-9">
								<button type="button" class="btn btn-more" data-use="show-more-<?=$navParams['NavNum']?>"><?=Loc::getMessage("REVIEWS_SHOW_MORE_ITEMS")?></button>
							</div>
						</div>
					</div>
					<!-- show-more-container -->
				<?}?>
			</div>
		<?}?>
	</div>
	<div class="col-xs-12 col-md-3">
		<?if(!empty($arResult["ITEMS"])) {?>
			<div class="reviews-general-stats">
				<div class="reviews-general-stats-rating">
					<?$lastRating = end($arResult["RATING_LIST"]);?>
					<div class="reviews-general-stats-rating-circle p<?=round(($arResult["RATING"]["GENERAL_VALUE"] / $lastRating["XML_ID"]) * 100)?>">
						<div class="reviews-general-stats-rating-circle-val"><?=$arResult["RATING"]["GENERAL_VALUE"]?></div>
						<div class="reviews-general-stats-rating-circle-slice">
							<div class="reviews-general-stats-rating-circle-bar"></div>
							<div class="reviews-general-stats-rating-circle-fill"></div>
						</div>
					</div>
					<?unset($lastRating);?>
					<div class="reviews-general-stats-rating-title"><?=Loc::getMessage("REVIEWS_RATING")?></div>				
				</div>
				<?if($arResult["RATING"]["USERS_RECOMMEND_PERCENT"] >= 85) {?>
					<div class="reviews-general-stats-users-recommend"><?=$arResult["RATING"]["USERS_RECOMMEND_PERCENT"].Loc::getMessage("REVIEWS_USERS_RECOMMEND")?></div>
				<?}?>
				<div class="reviews-general-stats-btn">
					<button type="button" class="btn btn-buy" data-entity="addReview"><i class="icon-comment"></i><span><?=Loc::getMessage("REVIEWS_ADD_REVIEW")?></span></button>
				</div>
				<div class="reviews-general-stats-rating-list">
					<div class="reviews-general-stats-rating-item-container active" data-rating-id="0">
						<div class="reviews-general-stats-rating-item">
							<div class="reviews-general-stats-rating-item-title"><?=Loc::getMessage("REVIEWS_ALL_REVIEWS")?></div>
							<div class="reviews-general-stats-rating-item-val"><?=$arResult["REVIEWS_COUNT"]?></div>
						</div>
					</div>				
					<?foreach(array_reverse($arResult["RATING_LIST"], true) as $ratingId => $arRatingRev) {?>
						<div class="reviews-general-stats-rating-item-container<?=(count($arRatingRev["ELEMENTS"]) == 0 ? ' disabled' : '')?>" data-rating-id="<?=$ratingId?>">
							<div class="reviews-general-stats-rating-item">
								<div class="reviews-general-stats-rating-block">
									<div class="reviews-general-stats-rating-stars">
										<?foreach($arResult["RATING_LIST"] as $arRating) {?>
											<i class="icon-star-s reviews-general-stats-rating-star<?=($arRatingRev['XML_ID'] < $arRating['XML_ID'] ? ' reviews-general-stats-rating-star-empty' : '')?>"></i>
										<?}
										unset($arRating);?>
									</div>
									<?if(count($arRatingRev["ELEMENTS"]) > 0) {?>
										<div class="reviews-general-stats-rating-percent"><?=round((count($arRatingRev["ELEMENTS"]) / $arResult["REVIEWS_COUNT"]) * 100)?>%</div>
									<?}?>
								</div>
								<div class="reviews-general-stats-rating-item-val"><?=count($arRatingRev["ELEMENTS"])?></div>
							</div>
							<div class="reviews-general-stats-rating-progress-bar-container">
								<div class="reviews-general-stats-rating-progress-bar" style="width: <?=round((count($arRatingRev["ELEMENTS"]) / $arResult["REVIEWS_COUNT"]) * 100)?>%;"></div>
							</div>
						</div>					
					<?}
					unset($ratingId, $arRatingRev);?>
				</div>
			</div>
		<?}?>
	</div>
</div>

<?if(!empty($GLOBALS[$arParams["FILTER_NAME"]]))
	$arParams["GLOBAL_FILTER"] = $GLOBALS[$arParams["FILTER_NAME"]];

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'news.list');
$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'news.list');?>

<script type="text/javascript">	
	BX.message({
		REVIEWS_EVALUATION_VALUE: '<?=GetMessageJS("REVIEWS_EVALUATION_VALUE")?>',
		REVIEWS_YOUR_REVIEW: '<?=GetMessageJS("REVIEWS_YOUR_REVIEW");?>',
		REVIEWS_LOADING: '<?=GetMessageJS("REVIEWS_LOADING");?>'
	});
	var <?=$obName?> = new JCNewsListReviewsComponent({		
		siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
		templatePath: '<?=CUtil::JSEscape($templateFolder)?>',
		navParams: <?=CUtil::PhpToJSObject($navParams)?>,
		lazyLoad: '<?=$showLazyLoad?>',
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		parentContainer: '<?=$parentContainerName?>',
		container: '<?=$containerName?>'
	});
</script>