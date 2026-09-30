<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);

global $arSettings;

$arResult["JS_FILTER_PARAMS"]["HIDE_DISABLED_PROPS"] = $arSettings["SMART_FILTER_HIDE_DISABLED_PROPS"]["VALUE"];?>

<div class="bx-filter">				
	<div class="bx-filter-title-container">
		<i class="icon-sliders"></i>
		<span class="bx-filter-title"><?=GetMessage("CT_BCSF_FILTER_TITLE")?></span>
		<span class="bx-filter-close"><i class="icon-close"></i></span>
	</div>
	<div class="bx-filter-inner">
		<form name="<?=$arResult['FILTER_NAME'].'_form'?>" action="<?=$arResult['FORM_ACTION']?>" method="get" class="smartfilter">
			<?foreach($arResult["HIDDEN"] as $arItem) {?>
				<input type="hidden" name="<?=$arItem['CONTROL_NAME']?>" id="<?=$arItem['CONTROL_ID']?>" value="<?=$arItem['HTML_VALUE']?>" />
			<?}
			//PRICES//
			foreach($arResult["ITEMS"] as $key => $arItem) {
				$key = $arItem["ENCODED_ID"];
				if(isset($arItem["PRICE"])) {
					if($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
						continue;
					$precision = $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0;
					if(Bitrix\Main\Loader::includeModule("currency")) {
						$res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
						$precision = $res["DECIMALS"];
					}?>
					<div class="bx-filter-parameters-box bx-active">						
						<div class="bx-filter-parameters-box-title" onclick="smartFilter.hideFilterProps(this)">
							<i data-role="prop_angle" class="icon-arrow-down"></i>
							<span><?=$arItem["NAME"]?></span>
						</div>
						<div class="bx-filter-block" data-role="bx_filter_block">
							<div class="bx-filter-parameters-box-block-container">
								<div class="bx-filter-parameters-box-block">									
									<div class="bx-filter-input-container">
										<input class="min-price" type="text" name="<?=$arItem['VALUES']['MIN']['CONTROL_NAME']?>" id="<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>" value="<?=$arItem['VALUES']['MIN']['HTML_VALUE']?>" placeholder="<?=number_format($arItem['VALUES']['MIN']['VALUE'], $precision, '.', '')?>" size="5" onkeyup="smartFilter.keyup(this)" />
									</div>
								</div>
								<div class="bx-filter-parameters-box-block bx-filter-separate">-</div>
								<div class="bx-filter-parameters-box-block">									
									<div class="bx-filter-input-container">
										<input class="max-price" type="text" name="<?=$arItem['VALUES']['MAX']['CONTROL_NAME']?>" id="<?=$arItem['VALUES']['MAX']['CONTROL_ID']?>" value="<?=$arItem['VALUES']['MAX']['HTML_VALUE']?>" placeholder="<?=number_format($arItem['VALUES']['MAX']['VALUE'], $precision, '.', '')?>" size="5" onkeyup="smartFilter.keyup(this)" />
									</div>
								</div>
							</div>
							<div class="bx-ui-slider-track-container">
								<div class="bx-ui-slider-track" id="drag_track_<?=$key?>">
									<div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
									<div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
									<div class="bx-ui-slider-pricebar-v" style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
									<div class="bx-ui-slider-range" id="drag_tracker_<?=$key?>" style="left: 0%; right: 0%;">
										<a class="bx-ui-slider-handle left" style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
										<a class="bx-ui-slider-handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
									</div>
								</div>
							</div>						
						</div>
					</div>
					<?$arJsParams = array(
						"leftSlider" => 'left_slider_'.$key,
						"rightSlider" => 'right_slider_'.$key,
						"tracker" => "drag_tracker_".$key,
						"trackerWrap" => "drag_track_".$key,
						"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
						"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
						"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
						"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
						"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
						"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
						"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
						"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
						"precision" => $precision,
						"colorUnavailableActive" => 'colorUnavailableActive_'.$key,
						"colorAvailableActive" => 'colorAvailableActive_'.$key,
						"colorAvailableInactive" => 'colorAvailableInactive_'.$key,
					);?>
					<script type="text/javascript">
						BX.ready(function(){
							window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
						});
					</script>
				<?}
			}

			//NOT_PRICES//
			foreach($arResult["ITEMS"] as $key => $arItem) {
				if(empty($arItem["VALUES"]) || isset($arItem["PRICE"]))
					continue;
				if($arItem["DISPLAY_TYPE"] == "A" && ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0))
					continue;?>
				<div class="bx-filter-parameters-box<?=($arItem['DISPLAY_EXPANDED'] == 'Y' ? ' bx-active' : '');?>">					
					<div class="bx-filter-parameters-box-title" onclick="smartFilter.hideFilterProps(this)">
						<i data-role="prop_angle" class="icon-arrow-down"></i>
						<span><?=$arItem["NAME"]?></span>
					</div>
					<?if(!empty($arItem["FILTER_HINT"])) {?>
						<i id="item_title_hint_<?=$arItem['ID']?>" class="icon-question bx-filter-parameters-box-hint"></i>
						<script type="text/javascript">
							new top.BX.CHint({
								parent: top.BX("item_title_hint_<?=$arItem['ID']?>"),
								show_timeout: 10,
								hide_timeout: 200,
								dx: 2,
								preventHide: true,
								min_width: 250,
								hint: '<?= CUtil::JSEscape($arItem["FILTER_HINT"])?>'
							});
						</script>
					<?}?>
					<div class="bx-filter-block" data-role="bx_filter_block">
						<?$arCur = current($arItem["VALUES"]);
						switch($arItem["DISPLAY_TYPE"]) {
							//NUMBERS_WITH_SLIDER//
							case "A":
								$precision = $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0;?>
								<div class="bx-filter-parameters-box-block-container">
									<div class="bx-filter-parameters-box-block">										
										<div class="bx-filter-input-container">
											<input class="min-price" type="text" name="<?=$arItem['VALUES']['MIN']['CONTROL_NAME']?>" id="<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>" value="<?=$arItem['VALUES']['MIN']['HTML_VALUE']?>" placeholder="<?=number_format($arItem['VALUES']['MIN']['VALUE'], $precision, '.', '')?>" size="5" onkeyup="smartFilter.keyup(this)" />
										</div>
									</div>
									<div class="bx-filter-parameters-box-block bx-filter-separate">-</div>
									<div class="bx-filter-parameters-box-block">										
										<div class="bx-filter-input-container">
											<input class="max-price" type="text" name="<?=$arItem['VALUES']['MAX']['CONTROL_NAME']?>" id="<?=$arItem['VALUES']['MAX']['CONTROL_ID']?>" value="<?=$arItem['VALUES']['MAX']['HTML_VALUE']?>" placeholder="<?=number_format($arItem['VALUES']['MAX']['VALUE'], $precision, '.', '')?>" size="5" onkeyup="smartFilter.keyup(this)" />
										</div>
									</div>
								</div>
								<div class="bx-ui-slider-track-container">
									<div class="bx-ui-slider-track" id="drag_track_<?=$key?>">
										<div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
										<div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
										<div class="bx-ui-slider-pricebar-v" style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
										<div class="bx-ui-slider-range" id="drag_tracker_<?=$key?>" style="left: 0;right: 0;">
											<a class="bx-ui-slider-handle left" style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
											<a class="bx-ui-slider-handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
										</div>
									</div>
								</div>
								<?$arJsParams = array(
									"leftSlider" => 'left_slider_'.$key,
									"rightSlider" => 'right_slider_'.$key,
									"tracker" => "drag_tracker_".$key,
									"trackerWrap" => "drag_track_".$key,
									"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
									"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
									"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
									"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
									"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
									"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
									"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
									"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
									"precision" => $arItem["DECIMALS"]? $arItem["DECIMALS"]: 0,
									"colorUnavailableActive" => 'colorUnavailableActive_'.$key,
									"colorAvailableActive" => 'colorAvailableActive_'.$key,
									"colorAvailableInactive" => 'colorAvailableInactive_'.$key,
								);?>
								<script type="text/javascript">
									BX.ready(function(){
										window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
									});
								</script>
								<?break;
							
							//NUMBERS//
							case "B":
								$precision = $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0;?>
								<div class="bx-filter-parameters-box-block-container">
									<div class="bx-filter-parameters-box-block">										
										<div class="bx-filter-input-container">
											<input class="min-price" type="text" name="<?=$arItem['VALUES']['MIN']['CONTROL_NAME']?>" id="<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>" value="<?=$arItem['VALUES']['MIN']['HTML_VALUE']?>" placeholder="<?=number_format($arItem['VALUES']['MIN']['VALUE'], $precision, '.', '')?>" size="5" onkeyup="smartFilter.keyup(this)" />
										</div>
									</div>
									<div class="bx-filter-parameters-box-block bx-filter-separate">-</div>
									<div class="bx-filter-parameters-box-block">										
										<div class="bx-filter-input-container">
											<input class="max-price" type="text" name="<?=$arItem['VALUES']['MAX']['CONTROL_NAME']?>" id="<?=$arItem['VALUES']['MAX']['CONTROL_ID']?>" value="<?=$arItem['VALUES']['MAX']['HTML_VALUE']?>" placeholder="<?=number_format($arItem['VALUES']['MAX']['VALUE'], $precision, '.', '')?>" size="5" onkeyup="smartFilter.keyup(this)" />
										</div>
									</div>
								</div>
								<?break;
							
							//CHECKBOXES_WITH_PICTURES//
							case "G":?>
								<div class="bx-filter-param-btn-inline">
									<?foreach($arItem["VALUES"] as $val => $ar) {?>
										<input style="display: none;" type="checkbox" name="<?=$ar['CONTROL_NAME']?>" id="<?=$ar['CONTROL_ID']?>" value="<?=$ar['HTML_VALUE']?>"<?=($ar["CHECKED"] ? ' checked="checked"' : '');?> />
										<?$class = "";
										if($ar["CHECKED"])
											$class.= " bx-active";
										if($ar["DISABLED"])
											$class.= " disabled";?>
										<label for="<?=$ar['CONTROL_ID']?>" data-role="label_<?=$ar['CONTROL_ID']?>" class="bx-filter-param-label <?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
											<span class="bx-filter-param-btn">
												<span class="bx-filter-btn-color-icon-container">
													<span class="bx-filter-btn-color-icon" title="<?=$ar['VALUE'];?>" style="<?=(!empty($ar['CODE']) ? 'background-color: #'.$ar['CODE'].';' : (isset($ar["FILE"]) && !empty($ar['FILE']['SRC']) ? 'background-image: url('.$ar['FILE']['SRC'].');' : ''));?>"></span>
												</span>
											</span>
										</label>
									<?}?>
								</div>
								<?break;

							//CHECKBOXES_WITH_PICTURES_AND_LABELS//
							case "H":?>
								<div class="bx-filter-param-btn-block scrollbar-inner" data-role="scrollbar">
									<?foreach($arItem["VALUES"] as $val => $ar) {?>
										<input style="display: none;" type="checkbox" name="<?=$ar['CONTROL_NAME']?>" id="<?=$ar['CONTROL_ID']?>" value="<?=$ar['HTML_VALUE']?>"<?=($ar["CHECKED"] ? ' checked="checked"' : '');?> />
										<?$class = "";
										if($ar["CHECKED"])
											$class.= " bx-active";
										if($ar["DISABLED"])
											$class.= " disabled";?>
										<label for="<?=$ar['CONTROL_ID']?>" data-role="label_<?=$ar['CONTROL_ID']?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'bx-active');">
											<span class="bx-filter-param-btn">
												<span class="bx-filter-btn-color-icon-container">
													<span class="bx-filter-btn-color-icon" title="<?=$ar['VALUE'];?>" style="<?=(!empty($ar['CODE']) ? 'background-color: #'.$ar['CODE'].';' : (isset($ar["FILE"]) && !empty($ar['FILE']['SRC']) ? 'background-image: url('.$ar['FILE']['SRC'].');' : ''));?>"></span>
												</span>
											</span>
											<span class="bx-filter-param-text" title="<?=$ar['VALUE'];?>">
												<?=$ar["VALUE"];
												if($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])) {?>
													<span data-role="count_<?=$ar['CONTROL_ID']?>"><?=$ar["ELEMENT_COUNT"]?></span>
												<?}?>
											</span>
										</label>
									<?}?>
								</div>
								<?break;
							
							//DROPDOWN//
							case "P":
								$checkedItemExist = false;?>
								<div class="bx-filter-select-container">
									<div class="bx-filter-select-block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
										<div class="bx-filter-select-text" data-role="currentOption">
											<?foreach($arItem["VALUES"] as $val => $ar) {
												if($ar["CHECKED"]) {
													echo $ar["VALUE"];
													$checkedItemExist = true;
												}
											}
											if(!$checkedItemExist) {
												echo GetMessage("CT_BCSF_FILTER_ALL");
											}?>
										</div>
										<div class="bx-filter-select-arrow"><i class="icon-arrow-down"></i></div>
										<input style="display: none;" type="radio" name="<?=$arCur['CONTROL_NAME_ALT']?>" id="all_<?=$arCur['CONTROL_ID']?>" value="" />
										<?foreach($arItem["VALUES"] as $val => $ar) {?>
											<input style="display: none;" type="radio" name="<?=$ar['CONTROL_NAME_ALT']?>" id="<?=$ar['CONTROL_ID']?>" value="<?=$ar['HTML_VALUE_ALT']?>"<?=($ar["CHECKED"] ? ' checked="checked"' : '');?> />
										<?}?>
										<div class="bx-filter-select-popup" data-role="dropdownContent" style="display: none;">
											<ul>
												<li>
													<label for="all_<?=$arCur['CONTROL_ID']?>" class="bx-filter-param-label" data-role="label_all_<?=$arCur['CONTROL_ID']?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')"><?=GetMessage("CT_BCSF_FILTER_ALL");?></label>
												</li>
												<?foreach($arItem["VALUES"] as $val => $ar) {
													$class = "";
													if($ar["CHECKED"])
														$class.= " selected";
													if($ar["DISABLED"])
														$class.= " disabled";?>
													<li>
														<label for="<?=$ar['CONTROL_ID']?>" class="bx-filter-param-label<?=$class?>" data-role="label_<?=$ar['CONTROL_ID']?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')"><?=$ar["VALUE"]?></label>
													</li>
												<?}?>
											</ul>
										</div>
									</div>
								</div>
								<?break;

							//DROPDOWN_WITH_PICTURES_AND_LABELS//
							case "R":?>
								<div class="bx-filter-select-container">
									<div class="bx-filter-select-block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
										<div class="bx-filter-select-text fix" data-role="currentOption">
											<?$checkedItemExist = false;
											foreach($arItem["VALUES"] as $val => $ar) {
												if($ar["CHECKED"]) {?>
													<span class="bx-filter-btn-color-icon-container">
														<span class="bx-filter-btn-color-icon" title="<?=$ar['VALUE'];?>" style="<?=(isset($ar["FILE"]) && !empty($ar['FILE']['SRC']) ? 'background-image: url('.$ar['FILE']['SRC'].');' : (!empty($ar['CODE']) ? 'background-color: #'.$ar['CODE'].';' : ''));?>"></span>
													</span>
													<span class="bx-filter-param-text"><?=$ar["VALUE"]?></span>
													<?$checkedItemExist = true;
												}
											}
											if(!$checkedItemExist) {
												echo GetMessage("CT_BCSF_FILTER_ALL");
											}?>
										</div>
										<div class="bx-filter-select-arrow"><i class="icon-arrow-down"></i></div>
										<input style="display: none;" type="radio" name="<?=$arCur['CONTROL_NAME_ALT']?>" id="all_<?=$arCur['CONTROL_ID']?>" value="" />
										<?foreach($arItem["VALUES"] as $val => $ar) {?>
											<input style="display: none;" type="radio" name="<?=$ar['CONTROL_NAME_ALT']?>" id="<?=$ar['CONTROL_ID']?>" value="<?=$ar['HTML_VALUE_ALT']?>"<?=($ar["CHECKED"] ? ' checked="checked"' : '');?> />
										<?}?>
										<div class="bx-filter-select-popup fix" data-role="dropdownContent" style="display: none;">
											<ul>
												<li>
													<label for="all_<?=$arCur['CONTROL_ID']?>" class="bx-filter-param-label" data-role="label_all_<?=$arCur['CONTROL_ID']?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
														<?=GetMessage("CT_BCSF_FILTER_ALL");?>
													</label>
												</li>
												<?foreach($arItem["VALUES"] as $val => $ar) {
													$class = "";
													if($ar["CHECKED"])
														$class.= " selected";
													if($ar["DISABLED"])
														$class.= " disabled";?>
													<li>
														<label for="<?=$ar['CONTROL_ID']?>" data-role="label_<?=$ar['CONTROL_ID']?>" class="bx-filter-param-label<?=$class?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')">
															<span class="bx-filter-btn-color-icon-container">
																<span class="bx-filter-btn-color-icon" title="<?=$ar['VALUE'];?>" style="<?=(!empty($ar['CODE']) ? 'background-color: #'.$ar['CODE'].';' : (isset($ar["FILE"]) && !empty($ar['FILE']['SRC']) ? 'background-image: url('.$ar['FILE']['SRC'].');' : ''));?>"></span>
															</span>
															<span class="bx-filter-param-text"><?=$ar["VALUE"]?></span>
														</label>
													</li>
												<?}?>
											</ul>
										</div>
									</div>
								</div>
								<?break;
							
							//RADIO_BUTTONS//
							case "K":?>
								<div class="bx-filter-input-radio scrollbar-inner" data-role="scrollbar">
									<label class="bx-filter-param-label" for="all_<?=$arCur['CONTROL_ID']?>">
										<input style="display: none;" type="radio" value="" name="<?=$arCur['CONTROL_NAME_ALT']?>" id="all_<?=$arCur['CONTROL_ID']?>" onclick="smartFilter.click(this)" />
										<span class="bx-filter-param-check-container">
											<span class="bx-filter-param-check"><i class="icon-ok-b"></i></span>
										</span>
										<span class="bx-filter-param-text"><?=GetMessage("CT_BCSF_FILTER_ALL");?></span>
									</label>
									<?foreach($arItem["VALUES"] as $val => $ar) {?>
										<label data-role="label_<?=$ar['CONTROL_ID']?>" class="bx-filter-param-label<?=($ar["DISABLED"] ? ' disabled' : '');?>" for="<?=$ar['CONTROL_ID']?>">
											<input style="display: none;" type="radio" value="<?=$ar['HTML_VALUE_ALT']?>" name="<?=$ar['CONTROL_NAME_ALT']?>" id="<?=$ar['CONTROL_ID']?>"<?=($ar["CHECKED"] ? ' checked="checked"' : '');?> onclick="smartFilter.click(this)" />
											<span class="bx-filter-param-check-container">
												<span class="bx-filter-param-check"><i class="icon-ok-b"></i></span>
											</span>
											<span class="bx-filter-param-text" title="<?=$ar['VALUE']?>">
												<?=$ar["VALUE"];
												if($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])) {?>
													<span data-role="count_<?=$ar['CONTROL_ID']?>"><?=$ar["ELEMENT_COUNT"]?></span>
												<?}?>
											</span>
										</label>
									<?}?>
								</div>
								<?break;
							
							//CALENDAR//
							case "U":?>
								<div class="bx-filter-parameters-box-container-block">
									<div class="bx-filter-input-container bx-filter-calendar-container">
										<?$APPLICATION->IncludeComponent('bitrix:main.calendar', '',
											array(
												'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
												'SHOW_INPUT' => 'Y',
												'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
												'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
												'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
												'SHOW_TIME' => 'N',
												'HIDE_TIMEBAR' => 'Y',
											),
											null,
											array('HIDE_ICONS' => 'Y')
										);?>
									</div>
									<div class="bx-filter-input-container bx-filter-calendar-container">
										<?$APPLICATION->IncludeComponent('bitrix:main.calendar', '',
											array(
												'FORM_NAME' => $arResult["FILTER_NAME"]."_form",
												'SHOW_INPUT' => 'Y',
												'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="'.FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]).'" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
												'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
												'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
												'SHOW_TIME' => 'N',
												'HIDE_TIMEBAR' => 'Y',
											),
											null,
											array('HIDE_ICONS' => 'Y')
										);?>
									</div>
								</div>
								<?break;
							
							//CHECKBOXES//
							default:?>
								<div class="bx-filter-input-checkbox scrollbar-inner" data-role="scrollbar">
									<?foreach($arItem["VALUES"] as $val => $ar) {?>
										<label data-role="label_<?=$ar['CONTROL_ID']?>" class="bx-filter-param-label<?=($ar["DISABLED"] ? ' disabled' : '');?>" for="<?=$ar['CONTROL_ID']?>">
											<input style="display: none;" type="checkbox" value="<?=$ar['HTML_VALUE']?>" name="<?=$ar['CONTROL_NAME']?>" id="<?=$ar['CONTROL_ID']?>"<?=($ar["CHECKED"] ? ' checked="checked"' : '');?> onclick="smartFilter.click(this)" />
											<span class="bx-filter-param-check-container">
												<span class="bx-filter-param-check"><i class="icon-ok-b"></i></span>
											</span>
											<span class="bx-filter-param-text" title="<?=$ar['VALUE']?>">
												<?=$ar["VALUE"];
												if($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])) {?>
													<span data-role="count_<?=$ar['CONTROL_ID']?>"><?=$ar["ELEMENT_COUNT"]?></span>
												<?}?>
											</span>
										</label>
									<?}?>
								</div>
						<?}?>
						<div class="clr"></div>
					</div>
				</div>
			<?}?>
			<div class="bx-filter-button-box">
				<div class="bx-filter-block">
					<button class="btn btn-buy" type="submit" id="set_filter" name="set_filter"><span><?=GetMessage("CT_BCSF_SET_FILTER")?></span><span class="bx-filter-count" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?> style="display: inline-block;"><?=intval($arResult["ELEMENT_COUNT"])?></span></button>
					<button class="btn btn-default" type="submit" id="del_filter" name="del_filter"><i class="icon-trash"></i><span><?=GetMessage("CT_BCSF_DEL_FILTER")?></span></button>
				</div>
			</div>
		</form>
	</div>
</div>

<?//FILTER_LINKS//
$isFilterLinks = false;
foreach($arResult["ITEMS"] as $arItem) {
	if(((isset($arItem["PRICE"]) || $arItem["DISPLAY_TYPE"] == "A") && $arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] > 0) || $arItem["DISPLAY_TYPE"] == "B" || $arItem["DISPLAY_TYPE"] == "U") {
		if(!empty($arItem["VALUES"]["MIN"]["HTML_VALUE"]) || !empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) {
			$isFilterLinks = true;
			break;
		}
	} elseif(!empty($arItem["VALUES"])) {		
		foreach($arItem["VALUES"] as $val) {
			if($val["CHECKED"]) {
				$isFilterLinks = true;
				break;
			}
		}
		unset($val);
	}
}
unset($arItem);

if($isFilterLinks) {
	$this->SetViewTarget("CATALOG_SECTION_FILTER_LINKS");?>
	<div class="catalog-section-filter-links">
		<?//PRICES//
		foreach($arResult["ITEMS"] as $arItem) {
			if(isset($arItem["PRICE"]) && $arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] > 0) {
				if(!empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) {?>
					<div class="catalog-section-filter-link" id="<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>"><span><?=GetMessage("CT_BCSF_FILTER_FROM")." ".CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MIN"]["HTML_VALUE"], $arItem["VALUES"]["MIN"]["CURRENCY"])?></span><i class="icon-close"></i></div>
				<?}
				if(!empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) {?>
					<div class="catalog-section-filter-link" id="<?=$arItem['VALUES']['MAX']['CONTROL_ID']?>"><span><?=GetMessage("CT_BCSF_FILTER_TO")." ".CCurrencyLang::CurrencyFormat($arItem["VALUES"]["MAX"]["HTML_VALUE"], $arItem["VALUES"]["MAX"]["CURRENCY"])?></span><i class="icon-close"></i></div>
				<?}
			}
		}
		unset($arItem);

		//NOT_PRICES//
		foreach($arResult["ITEMS"] as $arItem) {
			if(!empty($arItem["VALUES"]) && !isset($arItem["PRICE"])) {
				if(($arItem["DISPLAY_TYPE"] == "A" && $arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] > 0) || $arItem["DISPLAY_TYPE"] == "B" || $arItem["DISPLAY_TYPE"] == "U") {
					if(!empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) {?>
						<div class="catalog-section-filter-link" id="<?=$arItem['VALUES']['MIN']['CONTROL_ID']?>"><span><?=GetMessage("CT_BCSF_FILTER_FROM")." ".$arItem["VALUES"]["MIN"]["HTML_VALUE"]?></span><i class="icon-close"></i></div>
					<?}
					if(!empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) {?>
						<div class="catalog-section-filter-link" id="<?=$arItem['VALUES']['MAX']['CONTROL_ID']?>"><span><?=GetMessage("CT_BCSF_FILTER_TO")." ".$arItem["VALUES"]["MAX"]["HTML_VALUE"]?></span><i class="icon-close"></i></div>
					<?}
				} else {
					foreach($arItem["VALUES"] as $val) {
						if($val["CHECKED"]) {?>
							<div class="catalog-section-filter-link" id="<?=$val['CONTROL_ID']?>"><span><?=$val["VALUE"]?></span><i class="icon-close"></i></div>
						<?}
					}
					unset($val);
				}
			}
		}
		unset($arItem);?>
	</div>
	<?$this->EndViewTarget();
}?>

<script type="text/javascript">
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
</script>