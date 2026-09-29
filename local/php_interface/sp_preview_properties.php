<?php
/**
 * Category preview properties, task 2032123 / 4.6.
 * Extends the existing Bitrix category form and enext property rows.
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    return;
}

final class SantehpodborPreviewProperties
{
    const IBLOCK_ID = 25;
    const FIELD = 'UF_SP_PREVIEW';
    const TYPE = 'sp_preview';

    public static function getUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => self::TYPE,
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => 'Характеристики в превью товаров',
            'BASE_TYPE' => 'string',
        ];
    }

    public static function getDBColumnType($field) { return 'text'; }
    public static function prepareSettings($field) { return []; }
    public static function getSettingsHTML($field, $control, $fromForm) { return ''; }

    public static function decode($value)
    {
        $data = is_string($value) ? json_decode($value, true) : null;
        $mode = is_array($data) && in_array($data['mode'] ?? '', ['inherit', 'custom', 'hide'], true)
            ? $data['mode'] : 'inherit';
        $ids = [];
        foreach ((array)($data['ids'] ?? []) as $id) {
            if (is_scalar($id) && ctype_digit((string)$id) && (int)$id > 0) {
                $ids[(int)$id] = (int)$id;
            }
        }
        return ['mode' => $mode, 'ids' => array_values($ids)];
    }

    public static function properties()
    {
        static $properties;
        if ($properties !== null) {
            return $properties;
        }
        $properties = [];
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            return $properties;
        }
        $rows = \CIBlockProperty::GetList(
            ['SORT' => 'ASC', 'NAME' => 'ASC'],
            ['IBLOCK_ID' => self::IBLOCK_ID, 'ACTIVE' => 'Y']
        );
        while ($row = $rows->Fetch()) {
            if (!in_array($row['PROPERTY_TYPE'], ['S', 'N', 'L', 'E', 'G'], true)
                || ($row['PROPERTY_TYPE'] === 'S'
                    && !in_array((string)$row['USER_TYPE'], ['', 'directory', 'ElementXmlID'], true))) {
                continue;
            }
            $properties[(int)$row['ID']] = $row;
        }
        return $properties;
    }

    /** Property IDs with values on products linked to this section or its descendants. */
    public static function availablePropertyIds($sectionId)
    {
        static $available = [];
        $sectionId = (int)$sectionId;
        if ($sectionId <= 0 || !\Bitrix\Main\Loader::includeModule('iblock')) {
            return [];
        }
        if (isset($available[$sectionId])) {
            return $available[$sectionId];
        }
        $available[$sectionId] = [];
        $section = \CIBlockSection::GetByID($sectionId)->Fetch();
        if (!$section || (int)$section['IBLOCK_ID'] !== self::IBLOCK_ID) {
            return [];
        }
        $propertyIds = array_map('intval', array_keys(self::properties()));
        if (!$propertyIds) {
            return [];
        }
        // This catalogue uses Bitrix's shared property storage (VERSION=1).
        // Section-element links include additional category assignments; property links do not.
        // Inactive products are included so editors can prepare their previews before publication.
        $sql = "SELECT DISTINCT P.IBLOCK_PROPERTY_ID
            FROM b_iblock_section S
            INNER JOIN b_iblock_section_element SE
                ON SE.IBLOCK_SECTION_ID=S.ID AND SE.ADDITIONAL_PROPERTY_ID IS NULL
            INNER JOIN b_iblock_element E
                ON E.ID=SE.IBLOCK_ELEMENT_ID AND E.IBLOCK_ID=".self::IBLOCK_ID."
            INNER JOIN b_iblock_element_property P ON P.IBLOCK_ELEMENT_ID=E.ID
            WHERE S.IBLOCK_ID=".self::IBLOCK_ID."
                AND S.LEFT_MARGIN >= ".(int)$section['LEFT_MARGIN']."
                AND S.RIGHT_MARGIN <= ".(int)$section['RIGHT_MARGIN']."
                AND P.IBLOCK_PROPERTY_ID IN (".implode(',', $propertyIds).")
                AND P.VALUE IS NOT NULL
                AND LENGTH(TRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                    P.VALUE, CHAR(0), ''), CHAR(9), ''), CHAR(10), ''), CHAR(11), ''), CHAR(13), ''))) > 0";
        $rows = \Bitrix\Main\Application::getConnection()->query($sql);
        while ($row = $rows->fetch()) {
            $available[$sectionId][(int)$row['IBLOCK_PROPERTY_ID']] = true;
        }
        return $available[$sectionId];
    }

    public static function checkFields($field, $value)
    {
        if ($value === '' || $value === null) {
            return [];
        }
        $raw = is_string($value) ? json_decode($value, true) : null;
        if (!is_array($raw) || !in_array($raw['mode'] ?? '', ['inherit', 'custom', 'hide'], true)
            || !isset($raw['ids']) || !is_array($raw['ids'])) {
            return [['id' => self::FIELD, 'text' => 'Не удалось прочитать настройку характеристик. Откройте категорию заново.']];
        }
        $config = self::decode($value);
        if ($config['mode'] === 'custom' && !array_intersect($config['ids'], array_keys(self::properties()))) {
            return [['id' => self::FIELD, 'text' => 'Выберите хотя бы одну доступную характеристику или режим «Не показывать».']];
        }
        return [];
    }

    public static function onBeforeSave($field, $value)
    {
        return json_encode(self::decode($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function resolve($sectionId, $iblockId = self::IBLOCK_ID)
    {
        $default = ['mode' => 'default', 'ids' => [], 'codes' => [], 'sourceId' => 0, 'sourceName' => 'Общие настройки каталога'];
        if ((int)$iblockId !== self::IBLOCK_ID || (int)$sectionId <= 0
            || !\Bitrix\Main\Loader::includeModule('iblock')) {
            return $default;
        }
        $ids = [];
        $chain = \CIBlockSection::GetNavChain(self::IBLOCK_ID, (int)$sectionId, ['ID', 'IBLOCK_ID']);
        while ($section = $chain->Fetch()) {
            $ids[] = (int)$section['ID'];
        }
        if (!$ids) {
            return $default;
        }
        $sections = \CIBlockSection::GetList(
            ['DEPTH_LEVEL' => 'DESC'],
            ['IBLOCK_ID' => self::IBLOCK_ID, 'ID' => $ids],
            false,
            ['ID', 'IBLOCK_ID', 'NAME', 'DEPTH_LEVEL', self::FIELD]
        );
        while ($section = $sections->Fetch()) {
            $config = self::decode($section[self::FIELD] ?? '');
            if ($config['mode'] === 'inherit') {
                continue;
            }
            $properties = self::properties();
            $config['ids'] = array_values(array_filter($config['ids'], function ($id) use ($properties) {
                return isset($properties[$id]);
            }));
            $config['codes'] = [];
            foreach ($config['ids'] as $id) {
                $config['codes'][] = (string)($properties[$id]['CODE'] ?: $id);
            }
            $config['sourceId'] = (int)$section['ID'];
            $config['sourceName'] = $section['NAME'];
            return $config;
        }
        return $default;
    }

    public static function isConfigured($config)
    {
        return is_array($config) && in_array($config['mode'] ?? '', ['custom', 'hide'], true);
    }

    public static function hasValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $part) {
                if (self::hasValue($part)) {
                    return true;
                }
            }
            return false;
        }
        return $value !== null && $value !== false && trim((string)$value) !== '';
    }

    private static function plainValue($value)
    {
        if (is_array($value)) {
            $parts = [];
            foreach ($value as $part) {
                $part = self::plainValue($part);
                if ($part !== '') {
                    $parts[] = $part;
                }
            }
            return implode(' / ', $parts);
        }
        $value = preg_replace('~<br\s*/?>~i', ' / ', (string)$value);
        return trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public static function render($item, $config)
    {
        if (($config['mode'] ?? '') !== 'custom') {
            return '';
        }
        $byId = [];
        foreach ((array)($item['PROPERTIES'] ?? []) as $property) {
            if (is_array($property) && isset($property['ID'])) {
                $byId[(int)$property['ID']] = $property;
            }
        }
        foreach ((array)($item['DISPLAY_PROPERTIES'] ?? []) as $property) {
            if (is_array($property) && isset($property['ID'])) {
                $id = (int)$property['ID'];
                $byId[$id] = array_merge($byId[$id] ?? [], $property);
            }
        }
        $rows = '';
        foreach ((array)($config['ids'] ?? []) as $id) {
            if (!isset($byId[$id]) || !self::hasValue($byId[$id]['VALUE'] ?? null)) {
                continue;
            }
            $property = $byId[$id];
            if (!array_key_exists('DISPLAY_VALUE', $property)) {
                $property = \CIBlockFormatProperties::GetDisplayValue($item, $property, 'catalog_out');
            }
            $value = self::plainValue($property['DISPLAY_VALUE'] ?? '');
            if ($value === '') {
                continue;
            }
            $rows .= '<div class="product-item-properties" data-preview-property="'.(int)$id.'">'
                .'<div class="product-item-properties-name">'.htmlspecialcharsbx($property['~NAME'] ?? $property['NAME']).'</div>'
                .'<div class="product-item-properties-val">'.htmlspecialcharsbx($value).'</div></div>';
        }
        return $rows === '' ? '' : '<div class="product-item-properties-block sp-preview-properties">'.$rows.'</div>';
    }

    public static function clearSectionCache(&$fields)
    {
        if ((int)($fields['IBLOCK_ID'] ?? 0) === self::IBLOCK_ID
            && array_key_exists(self::FIELD, $fields)
            && is_object($GLOBALS['CACHE_MANAGER'] ?? null)) {
            $GLOBALS['CACHE_MANAGER']->ClearByTag('iblock_id_'.self::IBLOCK_ID);
        }
    }

    public static function getAdminListViewHTML($field, $control)
    {
        $config = self::decode(html_entity_decode((string)($control['VALUE'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $labels = ['inherit' => 'Наследовать', 'custom' => 'Свой список', 'hide' => 'Не показывать'];
        return $labels[$config['mode']];
    }

    public static function getEditFormHTML($field, $control)
    {
        // Bitrix's legacy form manager passes an HTML-escaped VALUE to this callback.
        $config = self::decode(html_entity_decode((string)($control['VALUE'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $sectionId = (int)($field['VALUE_ID'] ?? 0);
        $availableIds = self::availablePropertyIds($sectionId);
        $selectedIds = array_fill_keys($config['ids'], true);
        $properties = [];
        foreach (self::properties() as $id => $property) {
            if (!isset($availableIds[$id]) && !isset($selectedIds[$id])) {
                continue;
            }
            $properties[] = [
                'id' => $id, 'name' => $property['NAME'], 'code' => (string)$property['CODE'],
                'available' => isset($availableIds[$id]),
            ];
        }
        $parentId = (int)($_REQUEST['IBLOCK_SECTION_ID'] ?? $_REQUEST['find_section_section'] ?? 0);
        if ((int)($field['VALUE_ID'] ?? 0) > 0) {
            $section = \CIBlockSection::GetByID((int)$field['VALUE_ID'])->Fetch();
            $parentId = (int)($section['IBLOCK_SECTION_ID'] ?? 0);
        }
        $parent = self::resolve($parentId);
        $source = $parent['sourceName'];
        if ($parent['mode'] === 'hide') {
            $source .= ' — характеристики скрыты';
        }
        $rootId = 'sp-preview-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $control['NAME']);
        $escape = function ($value) { return htmlspecialcharsbx((string)$value); };
        $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        $dataJson = json_encode([
            'config' => $config, 'properties' => $properties,
            'emptyMessage' => $sectionId > 0 ? 'Нет заполненных характеристик' : 'Сначала сохраните категорию и добавьте товары',
            'hasAvailable' => (bool)$availableIds,
        ], $jsonFlags);
        ob_start();
        ?>
        <div id="<?= $escape($rootId) ?>" class="sp-preview-editor">
            <input type="hidden" class="sp-preview-value" name="<?= $escape($control['NAME']) ?>" value="<?= $escape(json_encode($config)) ?>">
            <label for="<?= $escape($rootId) ?>-mode">Режим отображения</label>
            <select id="<?= $escape($rootId) ?>-mode" class="sp-preview-mode">
                <option value="inherit">Наследовать</option>
                <option value="custom">Свой список</option>
                <option value="hide">Не показывать</option>
            </select>
            <p class="sp-preview-inherited">Источник: <?= $escape($source) ?>. После изменения родителя источник обновится при сохранении категории.</p>
            <p class="sp-preview-hidden-note" hidden>Блок характеристик будет скрыт. Дочерние категории могут наследовать этот режим.</p>
            <fieldset class="sp-preview-custom" hidden>
                <legend>Характеристики и порядок</legend>
                <label for="<?= $escape($rootId) ?>-search">Поиск по названию, коду или ID</label>
                <input id="<?= $escape($rootId) ?>-search" class="sp-preview-search" type="search" autocomplete="off" placeholder="Например: ширина, материал">
                <label for="<?= $escape($rootId) ?>-available">Доступные характеристики</label>
                <select id="<?= $escape($rootId) ?>-available" class="sp-preview-available" size="7" aria-describedby="<?= $escape($rootId) ?>-available-description"></select>
                <p id="<?= $escape($rootId) ?>-available-description" class="sp-preview-available-description" aria-live="polite"></p>
                <div class="sp-preview-add-row">
                    <button type="button" class="sp-preview-add">Добавить характеристику</button>
                    <span class="sp-preview-found" role="status"></span>
                </div>
                <ol class="sp-preview-selected" aria-label="Выбранные характеристики"></ol>
                <p class="sp-preview-empty">Пока ничего не выбрано.</p>
            </fieldset>
            <p class="sp-preview-error" role="alert" hidden>Выберите хотя бы одну доступную характеристику или режим «Не показывать».</p>
        </div>
        <style>
            .sp-preview-editor{width:min(700px,40vw);max-width:700px;min-width:0;box-sizing:border-box;color:#252525;text-align:left;overflow-wrap:anywhere}
            .sp-preview-editor label{display:block;margin:12px 0 6px}
            .sp-preview-editor p{margin:10px 0;line-height:1.45}
            .sp-preview-editor [hidden]{display:none!important}
            .sp-preview-editor fieldset{border:0;padding:8px 0 0;margin:12px 0 0;min-width:0}
            .sp-preview-editor legend{font-weight:bold}
            .sp-preview-editor .sp-preview-search,.sp-preview-editor .sp-preview-available{box-sizing:border-box;width:100%;max-width:700px}
            .sp-preview-editor .sp-preview-available{height:160px}
            .sp-preview-selected{margin:12px 0;padding-left:28px}
            .sp-preview-selected li{padding:8px 0;border-bottom:1px solid #d5dce0}
            .sp-preview-selected .sp-preview-property-label{display:block;overflow-wrap:anywhere;line-height:1.4}
            .sp-preview-selected .sp-preview-actions{display:flex;gap:6px;flex-wrap:wrap;margin-top:6px}
            .sp-preview-editor .sp-preview-add-row{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:10px}
            .sp-preview-editor .sp-preview-error,.sp-preview-editor .sp-preview-unavailable{color:#a51c24}
            .sp-preview-editor :focus-visible{outline:2px solid #206a92;outline-offset:2px}
        </style>
        <script>
        (function () {
            var root = document.getElementById(<?= json_encode($rootId) ?>);
            if (!root || root.getAttribute('data-ready')) return;
            root.setAttribute('data-ready', '1');
            var data = <?= $dataJson ?>;
            var ids = data.config.ids.slice();
            var mode = root.querySelector('.sp-preview-mode');
            var value = root.querySelector('.sp-preview-value');
            var search = root.querySelector('.sp-preview-search');
            var available = root.querySelector('.sp-preview-available');
            var selected = root.querySelector('.sp-preview-selected');
            var add = root.querySelector('.sp-preview-add');
            var error = root.querySelector('.sp-preview-error');
            var byId = {};
            data.properties.forEach(function (p) { byId[p.id] = p; });
            mode.value = data.config.mode;
            function label(p) { return p.name + ' [' + (p.code || 'без кода') + ', ID ' + p.id + ']'; }
            function sync() {
                value.value = JSON.stringify({mode:mode.value, ids:ids});
                error.hidden = true;
            }
            function describeAvailable() {
                var property = byId[Number(available.value)];
                root.querySelector('.sp-preview-available-description').textContent = property ? label(property) : '';
            }
            function filter() {
                var query = search.value.toLocaleLowerCase().trim();
                var previous = available.value;
                available.textContent = '';
                data.properties.forEach(function (p) {
                    if (!p.available || ids.indexOf(p.id) !== -1 || label(p).toLocaleLowerCase().indexOf(query) === -1) return;
                    var option = document.createElement('option');
                    option.value = p.id;
                    option.textContent = label(p);
                    available.appendChild(option);
                });
                if (Array.prototype.some.call(available.options, function (o) { return o.value === previous; })) {
                    available.value = previous;
                } else if (available.options.length) {
                    available.selectedIndex = 0;
                }
                describeAvailable();
                add.disabled = !available.options.length;
                root.querySelector('.sp-preview-found').textContent = available.options.length
                    ? 'Найдено: ' + available.options.length
                    : (query ? 'Характеристики не найдены' : (data.hasAvailable ? 'Все характеристики добавлены' : data.emptyMessage));
            }
            function draw() {
                selected.textContent = '';
                ids.forEach(function (id, index) {
                    var li = document.createElement('li');
                    var text = document.createElement('span');
                    text.className = 'sp-preview-property-label';
                    text.textContent = byId[id] ? label(byId[id]) : 'Недоступная характеристика, ID ' + id;
                    if (!byId[id]) text.classList.add('sp-preview-unavailable');
                    li.appendChild(text);
                    var actions = document.createElement('span');
                    actions.className = 'sp-preview-actions';
                    [['Выше',-1],['Ниже',1],['Удалить',0]].forEach(function (action) {
                        var button = document.createElement('button');
                        button.type = 'button';
                        button.textContent = action[0];
                        button.setAttribute('aria-label', action[0] + ': ' + (byId[id] ? byId[id].name : id));
                        button.disabled = (action[1] === -1 && index === 0) || (action[1] === 1 && index === ids.length - 1);
                        button.addEventListener('click', function () {
                            if (action[1] === 0) {
                                ids.splice(index, 1);
                            } else {
                                var other = index + action[1];
                                ids[index] = ids[other];
                                ids[other] = id;
                            }
                            draw();
                            var next = selected.querySelectorAll('li')[Math.max(0, Math.min(ids.length - 1, index + action[1]))];
                            if (next) next.querySelector('button:not(:disabled)').focus();
                            else search.focus();
                        });
                        actions.appendChild(button);
                    });
                    li.appendChild(actions);
                    selected.appendChild(li);
                });
                root.querySelector('.sp-preview-empty').hidden = ids.length > 0;
                root.querySelector('.sp-preview-custom').hidden = mode.value !== 'custom';
                root.querySelector('.sp-preview-inherited').hidden = mode.value !== 'inherit';
                root.querySelector('.sp-preview-hidden-note').hidden = mode.value !== 'hide';
                filter();
                sync();
            }
            function fitEditor() {
                var rect = root.getBoundingClientRect();
                if (!rect.width) return;
                var width = Math.max(240, Math.min(700, document.documentElement.clientWidth - rect.left - 24));
                var value = Math.floor(width) + 'px';
                if (root.style.width !== value) root.style.width = value;
            }
            if (window.ResizeObserver) {
                new ResizeObserver(function () { window.requestAnimationFrame(fitEditor); }).observe(root);
            }
            window.addEventListener('resize', fitEditor);
            window.requestAnimationFrame(fitEditor);
            available.addEventListener('change', describeAvailable);
            mode.addEventListener('change', draw);
            search.addEventListener('input', filter);
            search.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') { event.preventDefault(); add.click(); }
            });
            add.addEventListener('click', function () {
                var id = Number(available.value);
                if (!byId[id] || !byId[id].available || ids.indexOf(id) !== -1) return;
                ids.push(id);
                draw();
                search.focus();
            });
            var form = root.closest('form');
            function validateSelection(event) {
                if (mode.value === 'custom' && !ids.some(function (id) { return !!byId[id]; })) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    error.hidden = false;
                    search.focus();
                }
            }
            if (form) {
                // Validate before Bitrix disables the clicked submit button.
                form.addEventListener('click', function (event) {
                    var button = event.target.closest('input[type="submit"], input[type="image"], button[type="submit"], button:not([type])');
                    if (button && button.form === form) validateSelection(event);
                }, true);
                form.addEventListener('submit', validateSelection, true);
            }
            draw();
        }());
        </script>
        <?php
        return ob_get_clean();
    }
}

AddEventHandler('main', 'OnUserTypeBuildList', ['SantehpodborPreviewProperties', 'getUserTypeDescription']);
AddEventHandler('iblock', 'OnAfterIBlockSectionUpdate', ['SantehpodborPreviewProperties', 'clearSectionCache']);
