<?php

namespace Aspro\Max\Hl;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
    \Bitrix\Iblock\PropertyTable,
	Bitrix\Highloadblock as HL,
	Bitrix\Main\Entity;

class Helper
{
    private static ?Helper $instance = null;
    private static string $dataClass;

    public static function getInstance(string $hlName): static
    {
        if (self::$instance === null) {
            self::$instance = new self($hlName);
        }

        return self::$instance;
    }

    public function add(array $arFields): string
    {
        $entityDataClass = $this->dataClass;

        $result = $entityDataClass::add($arFields);

        $newHLStoreID = '';
        if ($result->isSuccess()) {
            $newHLStoreID = $result->getId();
        } else {
            $errors = $result->getErrorMessages();
            $strErrors = implode('\n', $errors);
            throw new \Exception($strErrors);
        }

        return $newHLStoreID;
    }

    public function update(string $idElement, array $arFields): bool
    {
        $entityDataClass = $this->dataClass;

        $result = $entityDataClass::update($idElement, $arFields);

        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
            $strErrors = implode('\n', $errors);
            throw new \Exception($strErrors);
        }

        return $result->isSuccess();
    }

    public function get(array $arOptions): array
    {
        $arSelect = $arOptions['select'] ?? [];
        $arOrder = $arOptions['order'] ?? ['ID' => 'DESC'];
        $arFilter = $arOptions['filter'] ?? [];

        $entityDataClass = $this->dataClass;

        $rsResult = $entityDataClass::getList([
            'select' => $arSelect,
            'order' => $arOrder,
            'filter' => $arFilter,
        ]);

        $result = [];
        while ($arRow = $rsResult->Fetch()) {
            $result[] = $arRow;
        }

        return $result;
    }

    public function delete(string $idElement): bool
    {
        $entityDataClass = $this->dataClass;

        $result = $entityDataClass::Delete($idElement);

        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
            $strErrors = implode('\n', $errors);
            throw new \Exception($strErrors);
        }

        return $result->isSuccess();
    }

    protected function __construct(string $hlName)
    {
        Loader::includeModule('highloadblock');

        $filter = [
            'LOGIC' => 'OR',
            ['=NAME' => $hlName],
            ['=TABLE_NAME' => $hlName],
        ];

        $hlblock = HL\HighloadBlockTable::getList([
            'filter' => $filter,
        ])->fetch();

        if (!$hlblock) {
            throw new \Exception('hl block not found');
        }

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();

        $this->dataClass = $entityDataClass;
    }

    public static function getHighloadTableName(int $iblockId, string $propertyCode)
    {
        $hlTableName = '';

        $property = PropertyTable::getList([
            'filter' => [
                'IBLOCK_ID' => $iblockId,
                'CODE' => $propertyCode,
            ],
            'select' => ['ID', 'NAME', 'USER_TYPE_SETTINGS'],
        ])->fetch();

        if ($property) {
            $settings = @unserialize($property['USER_TYPE_SETTINGS']);
            if (!is_array($settings)) {
                $settings = @json_decode($property['USER_TYPE_SETTINGS'], true);
            }
        }

        if ($settings && isset($settings['TABLE_NAME'])) {
            $hlTableName = $settings['TABLE_NAME'];
        }

        return $hlTableName;
    }
}
