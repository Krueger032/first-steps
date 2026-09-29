<?php

namespace Itb\Event;

use Bitrix\Main\Loader;

class Catalog
{
    public static function OnBeforeIBlockElementDeleteHandler($ID)
    {
        Loader::IncludeModule("iblock");

        $iblockId = \CIBlockElement::GetIBlockByID($ID);

        if($iblockId && $iblockId == \Itb\Settings\Catalog::IBLOCK_ID) {
            global $APPLICATION;
            $APPLICATION->throwException("Элементы каталог удалять нельзя, деактивируй !!!");
            return false;
        }
    }
}