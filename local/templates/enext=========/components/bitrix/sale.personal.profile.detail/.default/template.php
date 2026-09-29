<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

global $USER;
if(!$USER->IsAuthorized())
	return;

CJSCore::Init(array("date"));

if(strlen($arResult["ID"]) > 0) {
	ShowError($arResult["ERROR_MESSAGE"]);?>
	
	<div class="sale-personal-profile-detail">
		<form method="post" class="" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
			<div class="sppd-top-block-container">
				<div class="row">
					<div class="col-xs-12 col-md-4 sppd-block">
						<div class="sppd-formgroup-container">
							<div class="sppd-label-container"><?=Loc::getMessage("SALE_PERS_TYPE")?></div>
							<div class="sppd-input-container"><?=$arResult["PERSON_TYPE"]["NAME"]?></div>
						</div>
						<div class="sppd-formgroup-container">
							<div class="sppd-label-container"><?=Loc::getMessage("SALE_PNAME")?><span class="sppd-label__req">*</span></div>
							<div class="sppd-input-container">
								<input type="text" name="NAME" maxlength="50" value="<?=htmlspecialcharsbx($arResult['NAME'])?>" class="form-control" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<?foreach($arResult["ORDER_PROPS"] as $block) {
				if(!empty($block["PROPS"])) {?>
					<div class="sppd-window-block">
						<div class="sppd-title-container">
							<div class="sppd-title">
								<?if(!empty($arParams["ICON_PROPS_GROUP_".$block["ID"]])) {?>
									<div class="sppd-title__icon">
										<i class="<?=$arParams['ICON_PROPS_GROUP_'.$block['ID']]?>"></i>
									</div>
								<?}?>
								<div class="sppd-title__val"><?=$block["NAME"]?></div>
							</div>
						</div>						
						<div class="sppd-block-container">
							<div class="row">
								<div class="col-xs-12 col-md-4 sppd-block">
									<?foreach($block["PROPS"] as $key => $property) {
										$name = "ORDER_PROP_".$property["ID"];
										$currentValue = $arResult["ORDER_PROPS_VALUES"][$name];
										$alignTop = ($property["TYPE"] === "LOCATION" && $arParams["USE_AJAX_LOCATIONS"] === "Y")? "vertical-align-top": "";?>
										<div class="sppd-formgroup-container">
											<div class="sppd-label-container">
												<?=$property["NAME"].($property["REQUIED"] == "Y" ? " <span class='sppd-label__req'>*</span>" : "");
												if(strlen($property["DESCRIPTION"]) > 0) {?>
													<small>(<?=$property["DESCRIPTION"]?>)</small>
												<?}?>
											</div>
											<?if($property["TYPE"] == "CHECKBOX") {?>
												<div class="form-group">
													<div class="checkbox">
														<label>
															<input id="sppd-property-<?=$key?>" type="checkbox" name="<?=$name?>" value="Y"<?=($currentValue == "Y" || !isset($currentValue) && $property["DEFAULT_VALUE"] == "Y" ? " checked" : "")?> />
															<span class="check-cont">
																<span class="check">
																	<i class="icon-ok-b"></i>
																</span>
															</span>
														</label>
													</div>
												</div>
											<?} elseif($property["TYPE"] == "TEXT") {?>
												<div class="sppd-input-container form-group">
													<?if($property["MULTIPLE"] == "Y") {
														if(empty($currentValue) || !is_array($currentValue))
															$currentValue = array("");
														foreach($currentValue as $elementValue) {?>
															<input class="form-control" type="text" name="<?=$name?>[]" maxlength="50" id="sppd-property-<?=$key?>" value="<?=$elementValue?>" />
														<?}?>
														<button type="button" class="btn btn-buy input-add-multiple" data-add-type=<?=$property["TYPE"]?> data-add-name="<?=$name?>[]"><?=Loc::getMessage("SPPD_ADD")?></button>
													<?} else {?>
														<input class="form-control" type="text" name="<?=$name?>" maxlength="50" id="sppd-property-<?=$key?>" value="<?=$currentValue?>" />
													<?}?>
												</div>
											<?} elseif($property["TYPE"] == "SELECT") {?>
												<div class="sppd-input-container form-group">
													<select class="form-control" name="<?=$name?>" id="sppd-property-<?=$key?>" size="<?=(intval($property['SIZE1']) > 0 ? $property['SIZE1'] : 1);?>">
														<?foreach($property["VALUES"] as $value) {?>
															<option value="<?=$value['VALUE']?>"<?=($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"] ? " selected" : "");?>>
																<?=$value["NAME"]?>
															</option>
														<?}?>
													</select>
												</div>
											<?} elseif($property["TYPE"] == "MULTISELECT") {?>
												<div class="sppd-input-container form-group">
													<select class="form-control" id="sppd-property-<?=$key?>" multiple name="<?=$name?>[]" size="<?=(intval($property['SIZE1']) > 0 ? $property['SIZE1'] : 5);?>">
														<?$arCurVal = array();
														$arCurVal = explode(",", $currentValue);
														for($i = 0, $cnt = count($arCurVal); $i < $cnt; $i++)
															$arCurVal[$i] = trim($arCurVal[$i]);
														$arDefVal = explode(",", $property["DEFAULT_VALUE"]);
														for($i = 0, $cnt = count($arDefVal); $i < $cnt; $i++)
															$arDefVal[$i] = trim($arDefVal[$i]);
														foreach($property["VALUES"] as $value) {?>
															<option value="<?=$value['VALUE']?>"<?=(in_array($value["VALUE"], $arCurVal) || !isset($currentValue) && in_array($value["VALUE"], $arDefVal) ? " selected" : "");?>>
																<?=$value["NAME"]?>
															</option>
														<?}?>
													</select>
												</div>
											<?} elseif($property["TYPE"] == "TEXTAREA") {?>
												<div class="sppd-input-container form-group">
													<textarea class="form-control" id="sppd-property-<?=$key?>" rows="<?=((int)($property['SIZE2']) > 0 ? $property['SIZE2'] : 4);?>" cols="<?=((int)($property['SIZE1']) > 0 ? $property['SIZE1'] : 40);?>" name="<?=$name?>"><?=(isset($currentValue) ? $currentValue : $property["DEFAULT_VALUE"]);?></textarea>
												</div>
											<?} elseif($property["TYPE"] == "LOCATION") {?>
												<div class="sppd-input-container form-group">
													<?$locationTemplate = ($arParams["USE_AJAX_LOCATIONS"] !== "Y") ? "popup" : "";
													if($property["MULTIPLE"] == "Y") {
														if(empty($currentValue) || !is_array($currentValue))
															$currentValue = array($property["DEFAULT_VALUE"]);
														foreach($currentValue as $key => $elementValue) {
															$locationValue = intval($elementValue) ? $elementValue : $property["DEFAULT_VALUE"];
															CSaleLocation::proxySaleAjaxLocationsComponent(
																array(
																	"ID" => "propertyLocation".$name."[$key]",
																	"AJAX_CALL" => "N",
																	"CITY_OUT_LOCATION" => "Y",
																	"COUNTRY_INPUT_NAME" => $name."_COUNTRY",
																	"CITY_INPUT_NAME" => $name."[$key]",
																	"LOCATION_VALUE" => $locationValue,
																),
																array(
																),
																$locationTemplate,
																true,
																$locationClassName
															);
														}?>
														<button type="button" class="btn btn-buy input-add-multiple" data-add-type=<?=$property["TYPE"]?> data-add-name="<?=$name?>" data-add-last-key="<?=$key?>" data-add-template="<?=$locationTemplate?>"><?=Loc::getMessage("SPPD_ADD")?></button>
													<?} else {
														$locationValue = intval($currentValue) ? $currentValue : $property["DEFAULT_VALUE"];
														CSaleLocation::proxySaleAjaxLocationsComponent(
															array(
																"AJAX_CALL" => "N",
																"CITY_OUT_LOCATION" => "Y",
																"COUNTRY_INPUT_NAME" => $name."_COUNTRY",
																"CITY_INPUT_NAME" => $name,
																"LOCATION_VALUE" => $locationValue,
															),
															array(),
															$locationTemplate,
															true,
															"location-block-wrapper"
														);
													}?>
												</div>
											<?} elseif($property["TYPE"] == "RADIO") {
												foreach($property["VALUES"] as $value) {?>
													<div class="sppd-input-container form-group">
														<div class="radio">
															<label>
																<input class="form-control" type="radio" id="sppd-property-<?=$key?>" name="<?=$name?>" value="<?=$value['VALUE']?>"<?=($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"] ? " checked" : "");?> />
																<span class="check-cont">
																	<span class="check">
																		<i class="icon-ok-b"></i>
																	</span>
																</span>
																<span class="check-title"><?=$value["VALUE"]?></span>
															</label>
														</div>
													</div>
												<?}
											} elseif($property["TYPE"] == "FILE") {
												$multiple = ($property["MULTIPLE"] === "Y") ? "multiple" : "";
												$profileFiles = is_array($currentValue) ? $currentValue : array($currentValue);?>
												<div class="sppd-input-container sppd-input-container-property-file">
													<label class="sppd-input-file">
														<span class="btn btn-default"><?=Loc::getMessage("SPPD_SELECT")?></span>
														<span class="sppd-load-file-info"><?=Loc::getMessage("SPPD_FILE_NOT_SELECTED")?></span>
														<?=CFile::InputFile($name."[]", 20, null, false, 0, "IMAGE", "class='btn hidden' ".$multiple)?>
													</label>
													<span class="sppd-load-file-cancel hidden"></span>
													<?if(count($currentValue) > 0) { ?>
														<input type="hidden" name="<?=$name?>_del" class="sppd-property-input-delete-file" />
														<?foreach($profileFiles as $file) {
															$fileId = $file["ID"];?>
															<div class="sppd-form-file form-group">
																<div class="sppd-form-file-info">
																	<div class="sppd-file-info-block__title"><?=Loc::getMessage("SPPD_NAME_FILE_TITLE")?></div>
																	<div class="sppd-file-info-block__val"><?=$file["FILE_NAME"]?></div>
																	<?if(CFile::IsImage($file["FILE_NAME"])) {
																		echo CFile::ShowImage($fileId, 150, 150, "border=0", "", true);
																	} else {?>
																		<a download="<?=$file['ORIGINAL_NAME']?>" href="<?=CFile::GetFileSRC($file)?>">
																			<?=Loc::getMessage("SPPD_DOWNLOAD_FILE", array("#FILE_NAME#" => $file["ORIGINAL_NAME"]))?>
																		</a>
																	<?}?>
																</div>
																<div class="checkbox">
																	<label>
																		<input type="checkbox" value="<?=$fileId?>" class="sppd-property-check-file" id="sppd-property-check-file-<?=$fileId?>" />
																		<span class="check-cont">
																			<span class="check">
																				<i class="icon-ok-b"></i>
																			</span>
																		</span>
																		<a class="check-title check-form"><?=Loc::getMessage("SPPD_DELETE_FILE")?></a>
																	</label>
																</div>
															</div>
														<?}
													}?>
												</div>
											<?} elseif($property["TYPE"] == "DATE") {?>
												<div class="sppd-input-container form-group">
													<div class="input-group">
														<input type="text" name="<?=$name?>" size="10" id="sppd-property-<?=$key?>" class="form-control" value="<?=$currentValue?>" />
														<div class="input-group-addon" onclick="BX.calendar({node: this, field: BX.findParent(this, {tagName: 'DIV', className: 'input-group'}).querySelector('input[type=text]').name, bTime: <?=($property['TIME'] == 'Y' ? 'true' : 'false')?>});">
															<i class="bx-calendar"></i>
														</div>
													</div>
												</div>
											<?}?>
										</div>
									<?}?>
								</div>
							</div>
						</div>
					</div>
				<?}
			}?>
			<div class="sppd-formgroup-container">
				<div class="sppd-buttons-container">
					<input type="submit" class="btn btn-buy" name="save" value="<?=Loc::getMessage('SALE_SAVE')?>" />
					<input type="submit" class="btn btn-buy-ok"  name="apply" value="<?=Loc::getMessage('SALE_APPLY')?>" />
					<input type="submit" class="btn btn-default"  name="reset" value="<?=Loc::getMessage('SALE_RESET')?>" />
				</div>
			</div>
		</form>
		<?$javascriptParams = array(
			"ajaxUrl" => CUtil::JSEscape($this->__component->GetPath()."/ajax.php")
		);
		$javascriptParams = CUtil::PhpToJSObject($javascriptParams);?>
		<script>
			BX.message({
				SPPD_FILE_COUNT: "<?=Loc::getMessage('SPPD_FILE_COUNT')?>",
				SPPD_FILE_NOT_SELECTED: "<?=Loc::getMessage('SPPD_FILE_NOT_SELECTED')?>"
			});
			BX.Sale.PersonalProfileComponent.PersonalProfileDetail.init(<?=$javascriptParams?>);
		</script>
	</div>
<?} else {
	ShowError($arResult["ERROR_MESSAGE"]);
}?>