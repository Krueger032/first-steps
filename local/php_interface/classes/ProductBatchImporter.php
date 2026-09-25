<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Diag\Debug;

class ProductBatchImporter
{
    private static $IBLOCK_ID = 25; // ID вашего инфоблока каталога
    private static $LOG_FILE = '/product_importer.log'; // Путь к лог-файлу в корне сайта

    /**
     * Вспомогательная функция для записи логов.
     * @param string $message Сообщение для записи.
     */
    private static function log($message)
    {
        Debug::writeToFile(
            (new \DateTime())->format('Y-m-d H:i:s') . ' - ' . $message,
            "", // title
            self::$LOG_FILE
        );
    }

    /**
     * Основной метод для запуска агентом Bitrix.
     * Обрабатывает все batch-файлы из папки /batch/ за один запуск.
     *
     * @return string - Имя функции для повторного вызова агентом.
     */
    public static function runAgent()
    {
        self::log("--- Agent Start ---");

        if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
            self::log("ERROR: Modules iblock or catalog not loaded. Agent terminated.");
            return ""; // Завершаем работу, если модули не подключены
        }

        $batchDir = $_SERVER['DOCUMENT_ROOT'] . '/batch/';
        $files = glob($batchDir . '*_batch_*.json');

        if (empty($files)) {
            self::log("No batch files found. Agent finished.");
            return "ProductBatchImporter::runAgent();";
        }

        self::log("Found " . count($files) . " files to process.");

        foreach ($files as $file) {
            self::log("Processing file: " . basename($file));
            $data = json_decode(file_get_contents($file), true);

            $parentSectionName = $data['parent_section'] ?? null;
            $sectionTitle = $data['section_title'] ?? null;
            $products = $data['products'] ?? [];

            if (empty($products)) {
                self::log("File is empty or has no products. Deleting file.");
                unlink($file);
                continue;
            }

            // --- Логика определения раздела ---
            $parentSectionId = null;
            $childSectionId = null;
            $finalSectionId = null;

            if (!empty($parentSectionName)) {
                $parentSectionId = self::getOrCreateSection($parentSectionName, null);
                $finalSectionId = $parentSectionId;
                self::log("Parent section: '$parentSectionName' (ID: $parentSectionId)");
            }

            if (!empty($sectionTitle)) {
                $childSectionId = self::getOrCreateSection($sectionTitle, $parentSectionId);
                self::log("Child section: '$sectionTitle' (ID: $childSectionId)");
                if (!$finalSectionId) {
                    $finalSectionId = $childSectionId;
                }
            }

            // --- Импорт товаров ---
            if ($finalSectionId) {
                self::log("Importing products into final section ID: $finalSectionId");
                foreach ($products as $productData) {
                    self::importSimpleProduct($finalSectionId, $productData);
                }
            } else {
                self::log("ERROR: Could not determine final section for file " . basename($file) . ". Products skipped.");
            }
            
            self::log("Finished processing file. Deleting: " . basename($file));
            unlink($file);
        }
        
        self::log("--- Agent Finish ---");
        return "ProductBatchImporter::runAgent();";
    }

    /**
     * Создает или находит раздел.
     */
    private static function getOrCreateSection($name, $parentId = null)
    {
        $filter = [
            'IBLOCK_ID' => self::$IBLOCK_ID,
            'NAME' => $name,
        ];

        // Правильное поле для родительского раздела в D7
        if ($parentId) {
            $filter['IBLOCK_SECTION_ID'] = $parentId;
        } else {
            // Для разделов верхнего уровня
            $filter['=IBLOCK_SECTION_ID'] = null;
        }

        $section = SectionTable::getRow(['filter' => $filter, 'select' => ['ID']]);

        if ($section) {
            return $section['ID'];
        }

        // В CIBlockSection используется IBLOCK_SECTION_ID
        $bs = new \CIBlockSection;
        $arFields = [
            "ACTIVE" => "Y",
            "IBLOCK_ID" => self::$IBLOCK_ID,
            "NAME" => $name,
            "CODE" => self::translitCode($name),
            "IBLOCK_SECTION_ID" => $parentId,
        ];

        $id = $bs->Add($arFields);
        if ($id) {
            self::log("Created new section '$name' (ID: $id)");
        } else {
            self::log("ERROR: Failed to create section '$name'. Reason: " . $bs->LAST_ERROR);
        }
        return $id ?: null;
    }

    /**
     * Импортирует один товар.
     */
    private static function importSimpleProduct($sectionId, $productData)
    {
        $externalId = md5($productData['url']);
        $element = ElementTable::getRow([
            'filter' => ['IBLOCK_ID' => self::$IBLOCK_ID, 'XML_ID' => $externalId],
            'select' => ['ID']
        ]);
        
        $el = new \CIBlockElement;

        $name = $productData['product_name'];
        $code = self::translitCode($name);

        // --- Подготовка полей ---
        $arFields = [
            "IBLOCK_ID" => self::$IBLOCK_ID,
            "IBLOCK_SECTION_ID" => $sectionId,
            "XML_ID" => $externalId,
            "NAME" => $name,
            "CODE" => $code,
            "ACTIVE" => "Y",
            "DETAIL_TEXT" => $productData['description'] ?? '',
            "DETAIL_TEXT_TYPE" => 'html',
            "PROPERTY_VALUES" => [], // Инициализируем массив свойств
        ];

        // --- Обработка картинок ---
        $images = array_unique($productData['images'] ?? []);
        if (!empty($images[0])) {
            $mainImageFile = self::downloadImage($images[0]);
            if ($mainImageFile) {
                $arFields["PREVIEW_PICTURE"] = $mainImageFile;
                $arFields["DETAIL_PICTURE"] = $mainImageFile;
            } else {
                self::log("WARNING: Could not download main image for product '{$name}' from URL: {$images[0]}");
            }
        }
        
        // --- Подготовка свойств ---
        if (!empty($productData['specs'])) {
            foreach ($productData['specs'] as $propName => $propValue) {
                $cleanPropName = preg_replace('/[^a-zA-Z0-9_ \/.-]/', '', $propName);
                $propCode = 'PROP_' . self::translitCode($cleanPropName, '_');
                $propId = self::getOrCreateProperty($propCode, $cleanPropName);
                if ($propId) {
                    $arFields["PROPERTY_VALUES"][$propId] = $propValue;
                }
            }
        }
        
        $productId = null;
        if ($element) {
            $productId = $element['ID'];
            // Сначала обновляем основные поля и картинки
            if ($el->Update($productId, $arFields)) {
                self::log("SUCCESS: Updated product main fields for '" . $name . "' (ID: $productId)");
                // Затем отдельно и надежно обновляем свойства
                if (!empty($arFields["PROPERTY_VALUES"])) {
                    \CIBlockElement::SetPropertyValuesEx($productId, self::$IBLOCK_ID, $arFields["PROPERTY_VALUES"]);
                    self::log("INFO: Updated properties for product ID: $productId");
                }
            } else {
                self::log("ERROR: Failed to update product '" . $name . "'. Reason: " . $el->LAST_ERROR);
            }
        } else {
            // Для нового элемента все добавляется в одном вызове
            $productId = $el->Add($arFields);
            if ($productId) {
                 self::log("SUCCESS: Added new product '" . $name . "' (ID: $productId)");
            } else {
                self::log("ERROR: Failed to add new product '" . $name . "'. Reason: " . $el->LAST_ERROR);
            }
        }

        // --- Обработка цены и остатков ---
        if ($productId && isset($productData['wholesale_price'])) {
            $price = self::cleanPrice($productData['wholesale_price']);
            
            \CPrice::SetBasePrice($productId, $price, "RUB");
            
            $productFields = ['ID' => $productId, 'QUANTITY' => 100, 'AVAILABLE' => 'Y'];
            if (!\CCatalogProduct::Add($productFields)) {
                 \CCatalogProduct::Update($productId, ['QUANTITY' => 100, 'AVAILABLE' => 'Y']);
            }
        }
    }
    
    /**
     * Скачивает изображение по URL.
     */
    private static function downloadImage($url)
    {
        if (empty($url)) return null;
        $fileContent = @file_get_contents($url);
        if ($fileContent === false) return null;
        
        $fileName = basename(parse_url($url, PHP_URL_PATH));
        $tempFile = $_SERVER['DOCUMENT_ROOT'] . '/upload/temp/' . uniqid() . '_' . $fileName;
        file_put_contents($tempFile, $fileContent);

        return \CFile::MakeFileArray($tempFile);
    }
    
    /**
     * Создает или находит свойство инфоблока.
     */
    private static function getOrCreateProperty($code, $name, $type = 'S')
    {
        $property = PropertyTable::getRow([
            'filter' => ['IBLOCK_ID' => self::$IBLOCK_ID, 'CODE' => $code]
        ]);
        if ($property) {
            return $property['ID'];
        }

        $ibp = new \CIBlockProperty;
        $propId = $ibp->Add([
            "IBLOCK_ID" => self::$IBLOCK_ID,
            "NAME" => $name,
            "CODE" => $code,
            "ACTIVE" => "Y",
            "PROPERTY_TYPE" => $type, // <-- 'F' для файла, 'S' для строки
            "MULTIPLE" => ($type === 'F' ? 'Y' : 'N'), // Множественное для галереи
            "SORT" => "500",
        ]);
        if ($propId) {
             self::log("Created new property '$name' (CODE: $code)");
        }
        return $propId;
    }
    
    /**
     * Вспомогательные функции.
     */
    private static function cleanPrice($price) {
        return (float)preg_replace('/[^0-9.]/', '', $price);
    }

    private static function translitCode($str, $replacement = '-') {
        $str = mb_strtolower(trim($str));
        $str = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $str);
        $str = trim($str);
        $str = str_replace(' ', $replacement, $str);
        return CUtil::translit($str, "ru", ["replace_space" => $replacement, "replace_other" => $replacement]);
    }
} 