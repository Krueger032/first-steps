<?

$MESS['MAX_MODULE_SYNC_OK'] = 'Remainder synchronization completed successfully';
$MESS['MAX_MODULE_SAVE_OK'] = 'Settings saved successfully';
$MESS['MAX_NO_RIGHTS_FOR_VIEWING'] = 'Access Denied';
$MESS['MAX_MODULE_NOT_INCLUDED'] = 'Failed to connect module Aspro: Max';
$MESS['MAX_MODULE_CONTROL_CENTER_ERROR'] = 'Failed to get solution installation information';

$MESS["ASPRO_MAX_NO_SITE_INSTALLED"] = 'No sites found with "Aspro: Maximum - Online Store" solution installed<br />
<input type="button" value="Set" style="margin-top: 10px;" onclick="document.location.href=\'/bitrix/admin/wizard_install.php?lang=ru&wizardName=aspro:max&#SESSION_ID#\'">';

$MESS["MAIN_OPTIONS_STORES_TITLE"] = "General";
$MESS["MAX_MODULE_SETTINGS"] = "Settings";
$MESS["MAX_MODULE_PARAMS"] = "Parameters";
$MESS["MAX_MODULE_STORES_INFO"] = "This page will sync total inventory with inventory. If you have <b>quantity enabled</b>, do not check the \"Set inventory sync on product update/add event\" option <br><br>Click the <b>\"Save settings\"</b> button after activating/deactivating the option \"Set inventory sync on product update/add event\".<br><br> br>Press the <b>Synchronize</b> button after you type in the ID in the corresponding fields.";
$MESS["MAX_MODULE_SYNC_STORES"] = "Sync";
$MESS["MAX_MODULE_SAVE"] = "Save Settings";

$MESS["MAX_MODULE_IBLOCK_SECTION_ID"] = "ID of the infoblock section (to increase speed)";
$MESS["MAX_MODULE_EVENT_SYNC_TITLE"] = "Set balance sync on product update/add event";
$MESS["MAX_MODULE_NO_IBLOCK_ID"] = "Infoblock ID not set";
$MESS["MAX_MODULE_NO_CATALOG_IBLOCK_ID"] = "Infoblock is not a trade catalog";
$MESS["MAX_MODULE_NO_CATALOG_CAN_SELECT"] = "You cannot select an infoblock with trade offers, the elements of this infoblock will be automatically updated by linking to the trade catalog";


$MESS['MAX_PAGE_TITLE'] = 'Filter by warehouses';
$MESS["MAX_MODULE_EVENT_SYNC_PRODUCT_STORES_TITLE"] = "Automatically sync storage filter properties when updating and adding products";
$MESS["MAX_MODULE_EVENT_SYNC_STORES_TITLE"] = "Set filter property value list sync by warehouses on update/add/delete warehouse event";
$MESS['MAX_MODULE_SYNC_STEP'] = 'Number of items processed when synchronizing in one step';
$MESS["MAX_MODULE_IBLOCK_ID"] = "Infoblock of the <br> catalog (taken from <a href=\"/bitrix/admin/aspro.max_options.php\" target=\"_blank\">solution settings</a> \"Product catalog ID\" )";
$MESS['MAX_MODULE_CHOOSE_IBLOCK_ID'] = 'No info block selected';
$MESS['ASPRO_MAX_SYNC_ERROR'] = 'Error';
$MESS['MAX_MODULE_SYNC_ADD_PROP_STORES'] = 'Create properties';
$MESS['MAX_MODULE_USE_STORES_FILTER_TITLE'] = 'Enable storage filter functionality';
$MESS['MAX_MODULE_STORES_FILTER_PROP_CODE_TITLE'] = 'Property character code for warehouses';

$MESS['ASPRO_OPERATION_COMPLETE'] = 'Operation completed';
$MESS['ASPRO_PROP_NOT_EXIST'] = 'Property #PROP_CODE# is missing from IB #IBLOCK_ID#';
$MESS['ASPRO_PROP_NOT_TABLE'] = 'The #PROP_CODE# property of the #IBLOCK_ID# IB is not linked to the #ASPRO_HL_TABLE# warehouse directory. The property must be multiple, type Directory, and bound to the directory #ASPRO_HL_TABLE#';
$MESS['ASPRO_PROP_NO_HL_TABLE'] = 'No HL warehouse #ASPRO_HL_TABLE#';
$MESS['ASPRO_CHECK_PROP_EXIST'] = 'Check for property existence';
$MESS['ASPRO_SYNC_HL_STORES'] = 'Synchronization of warehouses and directory';
$MESS['ASPRO_SYNC_PRODUCT_PROP_STORES'] = 'Product Sync';

$MESS['ASPRO_CREATE_PROP_STORES_COMPLETE'] = 'Property #PROP_CODE# in IB #IBLOCK_ID# successfully created';
$MESS['ASPRO_SYNC_OFFERS_TITLE'] = 'Offers ';
$MESS['ASPRO_EDIT_PROP_CODE_NOTE'] = 'Settings must be saved after changing the property code';

$MESS['ASPRO_PROP_STORES_ALREADY_EXIST'] = 'Property #PROP_CODE# in IB #IBLOCK_ID# already exists';
$MESS["MAIN_OPTIONS_SITE_TITLE"] = "Site settings '#SITE_NAME#' (#SITE_ID#)";
$MESS['TABS_SETTINGS'] = 'Tab display settings';
$MESS['MAX_MODULE_SYNC_TITLE'] = 'Sync';
$MESS['ASPRO_NEED_CREATE_PROP'] = 'Click the \'Create Properties\' button to automatically create missing properties';
$MESS['MAX_MODULE_AUTO_SYNC_NOTE'] = 'All automatic actions put additional load on the site. During a complete exchange with 1C or import of goods to the site, we strongly do not recommend using the option. In other cases, it is worth evaluating individually based on the total load of the site.';
$MESS['MAX_MODULE_SYNC_TOP_NOTE'] = 'For the functionality to work correctly, check the \'Enable warehouse filter functionality\' checkbox and create properties by button. Read more about how the functionality works in <a href="https://aspro.ru/docs/course/course46/lesson3918/">documentation</a>.';
?>