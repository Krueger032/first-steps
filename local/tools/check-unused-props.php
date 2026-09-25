<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Application;

$iblockId = 25; // Каталог
$sitePath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/";
$searchInFiles = false;

$unusedProps = [];
$properties = PropertyTable::getList([
    'select' => ['ID', 'CODE', 'NAME'],
    'filter' => ['IBLOCK_ID' => $iblockId, '!CODE' => false]
]);

while ($prop = $properties->fetch()) {
    if (empty($prop['CODE'])) continue;

    $found = false;

    // 1. Поиск в умном фильтре
    $filterRes = CIBlock::GetList([], [
        'IBLOCK_ID' => $iblockId,
        'PROPERTY_ID' => $prop['ID'],
        'FILTRABLE' => 'Y'
    ]);
    if ($filterRes->Fetch()) {
        $found = true;
    }

    // 2. Поиск в шаблонах (если включено)
    if (!$found && $searchInFiles) {
        $code = $prop['CODE'];
        $escaped = preg_quote($code, '/');
        $pattern = '/' . $escaped . '\b|PROPERTY_' . $escaped . '/';

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sitePath)) as $file) {
            if ($file->isFile() && in_array($file->getExtension(), ['php', 'html'])) {
                $content = file_get_contents($file->getPathname());
                if (preg_match($pattern, $content)) {
                    $found = true;
                    break;
                }
            }
        }
    }

    if (!$found) {
        $unusedProps[] = $prop;
    }
    
}

echo "<h2>Неиспользуемые свойства (ID " . $iblockId . ")</h2>";
echo "<p>кол-во = ". count($unusedProps) ."</p>";
echo "<ul>";
foreach ($unusedProps as $p) {
    echo "<li>ID: {$p['ID']} | Code: {$p['CODE']} | Name: {$p['NAME']}</li>";
}
echo "</ul>";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");