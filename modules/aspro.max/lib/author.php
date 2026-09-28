<?php

namespace Aspro\Max;

use Aspro\Max\Hl\Helper as HelperHL;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Uri;
use CMax as Solution;

class Author
{
    private string $id = '';
    private string $table = '';

    public function __construct(?array $property = null)
    {
        $this->init($property);
    }

    private function init(?array $property = null)
    {
        if (!$property) {
            return;
        }

        $this->setId($property['VALUE']);
        $this->setTable($property['USER_TYPE_SETTINGS']['TABLE_NAME'] ?? '');
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function getHighloadTableNameByIblockId(string $iblockId): string
    {
        return HelperHL::getHighloadTableName($iblockId, Solution::getFrontParametrValue('AUTHOR_PROPERTY_CODE'));
    }

    public function getFilterUrl(string $path): string
    {
        if (!$this->isFilled()) {
            return '';
        }

        $uri = new Uri($path);

        $uri->addParams([
            'author' => $this->getId(),
        ]);

        return $uri->getUri();
    }

    public function isFilled(): bool
    {
        return $this->getId() && $this->getTable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getInfo(): array
    {
        if (!$this->isFilled()) {
            return [];
        }

        $dataManager = HelperHL::getInstance($this->getTable());

        if (!$dataManager) {
            return [];
        }

        $result = $dataManager->get([
            'select' => ['ID', 'UF_NAME', 'UF_FILE', 'UF_DESCRIPTION', 'UF_FULL_DESCRIPTION'],
            'filter' => ['UF_XML_ID' => $this->id],
        ]);

        $result = $result[0] ?? [];

        if (!empty($result['UF_FILE'])) {
            $result['UF_FILE'] = \CFile::ResizeImageGet(
                $result['UF_FILE'],
                ['width' => 100, 'height' => 100],
                BX_RESIZE_IMAGE_PROPORTIONAL_ALT
            );
        }

        return $result;
    }

    public function makeSectionFilter(): array
    {
        return ['PROPERTY_'.Solution::getFrontParametrValue('AUTHOR_PROPERTY_CODE') => $this->getId()];
    }

    public static function isNeedShowByExistedInRequest(): bool
    {
        return self::isEnabled() && self::getIdFromRequest();
    }

    public static function isEnabled(): bool
    {
        return Solution::getFrontParametrValue('USE_AUTHOR') === 'Y';
    }

    public static function getIdFromRequest(): string
    {
        $request = Context::getCurrent()->getRequest();

        return (string) $request->get('author');
    }

    public static function isNeedShowByFilledProperty(?array $arItem): bool
    {
        return self::isEnabled() && !empty($arItem['VALUE']);
    }
}
