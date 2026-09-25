<?php
/**
 * AWEB: compatibility fix for ALTOP ENEXT deferred script loading.
 *
 * Problem:
 * The ALTOP option "Отложенная загрузка скриптов" can move/delay Bitrix core scripts.
 * Then inline component initializers run while window.BX is still only a lightweight stub,
 * so catalog, smart filter and basket scripts fail with:
 * - BX.namespace is not a function
 * - BX.ready is not a function
 * - BX.hasClass is not a function
 * - babelHelpers is not defined
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    return;
}

if (!function_exists('awebEnextDeferredScriptsCoreFix')) {
    function awebEnextDeferredScriptsCoreFix(&$content)
    {
        if (!is_string($content) || $content === '') {
            return;
        }

        if (defined('ADMIN_SECTION') && ADMIN_SECTION === true) {
            return;
        }

        // Work only on pages where ENEXT/ALTOP script moving can be present.
        if (
            strpos($content, '/bitrix/js/altop.enext/') === false
            && strpos($content, '/local/templates/enext/') === false
        ) {
            return;
        }

        $criticalScripts = array(
            '/bitrix/js/main/core/core.js',
            '/bitrix/js/main/core/core_ajax.js',
            '/bitrix/js/main/core/core_promise.js',
            '/bitrix/js/main/core/core_fx.js',
            '/bitrix/js/main/core/core_window.js',
            '/bitrix/js/main/core/core_date.js',
            '/bitrix/js/main/ajax.js',
            '/bitrix/js/main/loadext/loadext.js',
            '/bitrix/js/main/loadext/extension.js',
            '/bitrix/js/main/polyfill/core/dist/polyfill.bundle.js',
            '/bitrix/js/main/polyfill/promise/js/promise.js',
            '/bitrix/js/main/polyfill/find/js/find.js',
            '/bitrix/js/main/polyfill/includes/js/includes.js',
            '/bitrix/js/main/polyfill/matches/js/matches.js',
            '/bitrix/js/main/jquery/jquery-2.2.4.min.js',
            '/bitrix/js/main/popup/dist/main.popup.bundle.js',
            '/bitrix/js/ui/buttons/dist/ui.buttons.bundle.js',
            '/bitrix/js/currency/currency-core/dist/currency-core.bundle.js',
            '/bitrix/js/currency/core_currency.js',
            '/bitrix/js/altop.enext/intlTelInput/intlTelInput.min.js'
        );

        $charset = defined('SITE_CHARSET') && SITE_CHARSET ? SITE_CHARSET : 'UTF-8';

        $content = preg_replace_callback(
            '#<script\b([^>]*\bsrc\s*=\s*(["\'])([^"\']+)\2[^>]*)>\s*</script>#i',
            function ($matches) use ($criticalScripts, $charset) {
                $tag = $matches[0];
                $src = html_entity_decode($matches[3], ENT_QUOTES, $charset);

                $isCritical = false;
                foreach ($criticalScripts as $criticalScript) {
                    if (strpos($src, $criticalScript) !== false) {
                        $isCritical = true;
                        break;
                    }
                }

                if (!$isCritical) {
                    return $tag;
                }

                // Critical Bitrix core scripts must not be moved by ALTOP.
                if (stripos($tag, 'data-skip-moving') === false) {
                    $tag = preg_replace('#<script\b#i', '<script data-skip-moving="true"', $tag, 1);
                }

                // Critical scripts must execute synchronously in original order.
                $tag = preg_replace('#\s+defer(?:\s*=\s*(["\']).*?\1|\s*=\s*[^\s>]+)?#i', '', $tag);
                $tag = preg_replace('#\s+async(?:\s*=\s*(["\']).*?\1|\s*=\s*[^\s>]+)?#i', '', $tag);

                return $tag;
            },
            $content
        );
    }

    AddEventHandler('main', 'OnEndBufferContent', 'awebEnextDeferredScriptsCoreFix', 1);
    AddEventHandler('main', 'OnEndBufferContent', 'awebEnextDeferredScriptsCoreFix', 10000);
}
