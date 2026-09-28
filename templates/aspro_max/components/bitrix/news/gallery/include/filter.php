<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?
$arItems = CMaxCache::CIBLockElement_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CMaxCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), $arItemFilter, false, false, array('ID', 'NAME', 'ACTIVE_FROM'));
$arYears = array();
    if($arItems)
	{
		foreach($arItems as $arItem) {
			if($arItem['ACTIVE_FROM']) {
				if($arDateTime = ParseDateTime($arItem['ACTIVE_FROM'], FORMAT_DATETIME)){}
					$arYears[$arDateTime['YYYY']] = $arDateTime['YYYY'];
			}
		}
		if($arYears) {
			if($arParams['USE_FILTER'] != 'N') {
				rsort($arYears);
				$bHasYear = (isset($_GET['year']) && (int)$_GET['year']);
				$year = ($bHasYear ? (int)$_GET['year'] : 0);?>
				<div class="select_head_wrap">
					<div class="menu_item_selected font_upper_md rounded3 bordered visible-xs font_xs darken"><span></span>
						<?=CMax::showIconSvg("down", SITE_TEMPLATE_PATH.'/images/svg/trianglearrow_down.svg', '', '', true, false);?>
					</div>
					<div class="head-block top bordered-block rounded3 clearfix srollbar-custom">
						<div class="item-link font_upper_md  <?=($bHasYear ? '' : 'active');?>">
							<div class="title">
								<?if($bHasYear):?>
									<a class="btn-inline dark_link" href="<?=$arResult['FOLDER'];?>"><?=GetMessage('ALL_TIME');?></a>
								<?else:?>
									<span class="btn-inline darken"><?=GetMessage('ALL_TIME');?></span>
								<?endif;?>
							</div>
						</div>
						<?foreach($arYears as $value):
							$bSelected = ($bHasYear && $value == $year);?>
							<div class="item-link font_upper_md <?=($bSelected ? 'active' : '');?>">
								<div class="title btn-inline darken">
									<?if($bSelected):?>
										<span class="btn-inline darken"><?=$value;?></span>
									<?else:?>
										<a class="btn-inline dark_link" href="<?=$APPLICATION->GetCurPageParam('year='.$value, array('year'));?>"><?=$value;?></a>
									<?endif;?>
								</div>
							</div>
						<?endforeach;?>
					</div>
				</div>
				<?
				if($bHasYear) {
					$GLOBALS[$arParams["FILTER_NAME"]][] = array(
						">=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$year, FORMAT_DATE,''),
						"<DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".($year+1), FORMAT_DATE,''),
					);
				}
			}
		}
	}?>