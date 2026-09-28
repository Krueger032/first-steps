<?

$MESS['MAX_MODULE_SYNC_OK'] = 'Синхронизация остатков успешно произведена';
$MESS['MAX_MODULE_SAVE_OK'] = 'Настройки успешно сохранены';
$MESS['MAX_NO_RIGHTS_FOR_VIEWING'] = 'Доступ закрыт';
$MESS['MAX_MODULE_NOT_INCLUDED'] = 'Не удалось подключить модуль Аспро: Max';
$MESS['MAX_MODULE_CONTROL_CENTER_ERROR'] = 'Не удалось получить информацию об установке решения';

$MESS["ASPRO_MAX_NO_SITE_INSTALLED"] = 'Не найдено сайтов с установленным решением &laquo;Аспро: Максимум - интернет-магазин&raquo;<br />
<input type="button" value="Установить" style="margin-top: 10px;" onclick="document.location.href=\'/bitrix/admin/wizard_install.php?lang=ru&wizardName=aspro:max&#SESSION_ID#\'">';

$MESS["MAIN_OPTIONS_STORES_TITLE"] = "Общие";
$MESS["MAX_MODULE_SETTINGS"] = "Настройки";
$MESS["MAX_MODULE_PARAMS"] = "Параметры";
$MESS["MAX_MODULE_STORES_INFO"] = "На этой странице будет произведена синхронизация общего наличия товара с наличием на складах. Если у вас <b>включен количественный учет</b>, не активируйте опцию «Установить синхронизацию остатков на событии обновления/добавления продукта», чтобы общее количество у товара было верное. <br><br>Нажмите кнопку <b>«Сохранить настройки»</b> после активации/деактивации опции «Установить синхронизацию остатков на событии обновления/добавления продукта».<br><br>Нажмите кнопку <b>«Синхронизировать»</b> после того, как пропишите ID в соответствующих полях.";
$MESS["MAX_MODULE_SYNC_STORES"] = "Синхронизировать";
$MESS["MAX_MODULE_SAVE"] = "Сохранить настройки";

$MESS["MAX_MODULE_IBLOCK_SECTION_ID"] = "ID раздела инфоблока (для увеличения скорости)";
$MESS["MAX_MODULE_EVENT_SYNC_TITLE"] = "Установить синхронизацию остатков на событии обновления/добавления продукта";
$MESS["MAX_MODULE_NO_IBLOCK_ID"] = "Не задан ID инфоблока";
$MESS["MAX_MODULE_NO_CATALOG_IBLOCK_ID"] = "Инфоблок не является торговым каталогом";
$MESS["MAX_MODULE_NO_CATALOG_CAN_SELECT"] = "Нельзя выбирать инфоблок с торговыми предложениями, элементы данного инфоблока будут автоматически обновлены через привязку к торговому каталогу";


$MESS['MAX_PAGE_TITLE'] = 'Фильтр по складам';
$MESS["MAX_MODULE_EVENT_SYNC_PRODUCT_STORES_TITLE"] = "Автоматически синхронизировать свойства фильтра по складам при обновлении и добавлении товаров";
$MESS["MAX_MODULE_EVENT_SYNC_STORES_TITLE"] = "Установить синхронизацию списка значений свойства фильтра по складам на событии обновления/добавления/удаления склада";
$MESS['MAX_MODULE_SYNC_STEP'] = 'Количество товаров обрабатываемых, при синхронизации за один шаг';
$MESS["MAX_MODULE_IBLOCK_ID"] = "Инфоблок каталога <br>(берется из <a href=\"/bitrix/admin/aspro.max_options.php\" target=\"_blank\">настроек решения</a> \"ID каталога товаров\" )";
$MESS['MAX_MODULE_CHOOSE_IBLOCK_ID'] = 'Инфоблок не выбран';
$MESS['ASPRO_MAX_SYNC_ERROR'] = 'Ошибка';
$MESS['MAX_MODULE_SYNC_ADD_PROP_STORES'] = 'Создать свойства';
$MESS['MAX_MODULE_USE_STORES_FILTER_TITLE'] = 'Включить функционал фильтра по складам';
$MESS['MAX_MODULE_STORES_FILTER_PROP_CODE_TITLE'] = 'Символьный код свойства для складов';

$MESS['ASPRO_OPERATION_COMPLETE'] = 'Операция завершена';
$MESS['ASPRO_PROP_NOT_EXIST'] = 'Свойство #PROP_CODE# отсутствует в ИБ #IBLOCK_ID#';
$MESS['ASPRO_PROP_NOT_TABLE'] = 'Свойство #PROP_CODE# ИБ #IBLOCK_ID# не привязано к справочнику складов #ASPRO_HL_TABLE#. Свойство должно быть множественным, тип Справочник и привязано к справочнику #ASPRO_HL_TABLE#';
$MESS['ASPRO_PROP_NO_HL_TABLE'] = 'Отсутствует HL складов #ASPRO_HL_TABLE#';
$MESS['ASPRO_CHECK_PROP_EXIST'] = 'Проверка существования свойства';
$MESS['ASPRO_SYNC_HL_STORES'] = 'Синхронизация складов и справочника';
$MESS['ASPRO_SYNC_PRODUCT_PROP_STORES'] = 'Синхронизация товаров';

$MESS['ASPRO_CREATE_PROP_STORES_COMPLETE'] = 'Свойство #PROP_CODE# в ИБ #IBLOCK_ID# успешно создано';
$MESS['ASPRO_SYNC_OFFERS_TITLE'] = 'Торговые предложения ';
$MESS['ASPRO_EDIT_PROP_CODE_NOTE'] = 'После изменения кода свойства необходимо сохранить настройки';

$MESS['ASPRO_PROP_STORES_ALREADY_EXIST'] = 'Свойство #PROP_CODE# в ИБ #IBLOCK_ID# уже существует';
$MESS["MAIN_OPTIONS_SITE_TITLE"] = "Настройки сайта &laquo;#SITE_NAME#&raquo; (#SITE_ID#)";
$MESS['TABS_SETTINGS'] = 'Настройки отображения табов';
$MESS['MAX_MODULE_SYNC_TITLE'] = 'Синхронизация';
$MESS['ASPRO_NEED_CREATE_PROP'] = 'Нажмите кнопку "Создать свойства" для автоматического создания недостающих свойств';
$MESS['MAX_MODULE_AUTO_SYNC_NOTE'] = 'Все автоматические действия оказывают дополнительную нагрузку на сайт. Во время полного обмена с 1С или импорта товаров на сайт крайне не рекомендуем использовать опцию. В остальных случаях стоит оценивать индивидуально исходя из общей нагрузки сайта.';
$MESS['MAX_MODULE_SYNC_TOP_NOTE'] = 'Для корректной работы функционала активируйте галку "Включить функционал фильтра по складам" и создайте свойства по кнопке. Подробнее о работе функционала читайте в <a href="https://aspro.ru/docs/course/course46/lesson3918/">документации</a>.';
?>