<?
namespace Linkor\Redirect;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;

use \Bitrix\Main\Loader;
Loader::includeModule('landing');

class Redirect{
    public $options;                 // Настройки из файла конфигов config/options.php
    public $redirects;               // Массив редиректов из файла config/urls.php
    public $host;                    // Доменное имя
    public $protocol;                // Протокол http/https
    public $port;                    // Порт, на тот случай, если используется 80 или 443
    public $currentUri;              // Текущий uri
    public $uri = null;              // Конечный uri
    public $isAbsoluteUrl = false;   // Полный или относительный путь
    public $changed = false;         // Флажок для проверки изменений в формировании url
    public $arUrl;                   // Url разбитый по кускам
    public $status;                  // Будущий статус для редиректов из списка (301/302)

    function __construct($options, $redirects){
        // Инициализируем основные параметры
        $this->options = $options;
        $this->redirects = $redirects;
        $this->host = $_SERVER["SERVER_NAME"];
        $this->protocol = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off"? "https" : "http";
        $this->port = !empty($_SERVER["SERVER_PORT"])
                && $_SERVER["SERVER_PORT"] != "80"
                && $_SERVER["SERVER_PORT"] != "443"?
                (":" . $_SERVER["SERVER_PORT"]) : "";
        $this->currentUri = $_SERVER["REQUEST_URI"];

        if (substr( $this->currentUri, 0, 2 ) === "//") {
            $this->currentUri = substr_replace($this->currentUri, "/", 0, 2);
            $this->changed = true;
        }
        $this->arUrl = parse_url(\Bitrix\Landing\Domain::getHostUrl() . $this->currentUri);

        // Формируем uri пробегаясь по основным редиректам
        $this->createBaseRedirectsUri();
        // Формируем конечную ссылку
        $this->createUri();
        // Пробегаемся по редиректам из списка config/urls.php
        $this->useRedirectList();

        // Инициируем редирект
        $this->executeFinalRedirect();
    }

    function createBaseRedirectsUri(){
        $this->redirectWWW();
        $this->redirectIndexPHP();
        $this->redirectIndexHTML();
        $this->redirectSlash();
        $this->redirectMultislash();
        $this->redirectFromUppercase();
    }

    function redirectWWW(){
        if ($this->options["redirect_www"] == "Y"
            && substr($_SERVER["SERVER_NAME"], 0, 4) == "www.") {
            $this->host = substr($_SERVER["SERVER_NAME"], 4);
            $this->url = $this->currentUri;
        }
    }

    function redirectIndexPHP(){
        if ($this->options["redirect_index_php"] == "Y") {
            $tmp = rtrim($this->arUrl["path"], "/");
            if (basename($tmp) == "index.php") {
                $dname = dirname($tmp);
                $this->arUrl["path"] = ($dname != DIRECTORY_SEPARATOR? $dname : "") . "/";
                $this->changed = true;
            }
        }
    }

    function redirectIndexHTML(){
        if ($this->options["redirect_index_html"] == "Y") {
            $tmp = rtrim($this->arUrl["path"], "/");
            if (basename($tmp) == "index.html") {
                $dname = dirname($tmp);
                $this->arUrl["path"] = ($dname != DIRECTORY_SEPARATOR? $dname : "") . "/";
                $this->changed = true;
            }
        }
    }

    function redirectSlash(){
        if ($this->options["redirect_slash"] == "Y") {
            $tmp = basename(rtrim($this->arUrl["path"], "/"));
            if (substr($this->arUrl["path"], -1, 1) != "/"
                && substr($tmp, -4) != ".php"
                && substr($tmp, -4) != ".htm"
                && substr($tmp, -5) != ".html") {
                $this->arUrl["path"] .= "/";
                $this->changed = true;
            }
        }
    }

    function redirectMultislash(){
        if ($this->options["redirect_multislash"] == "Y") {
            if (strpos($this->arUrl["path"], "//") !== false) {
                $this->arUrl["path"] = preg_replace('{/+}s', "/", $this->arUrl["path"]);
                $this->changed = true;
            }
        }
    }

    function redirectFromUppercase(){
        if ($this->options["redirect_from_uppercase"] == "Y") {
            if ($this->arUrl["path"] != strtolower($this->arUrl["path"])) {
                $this->arUrl["path"] = strtolower($this->arUrl["path"]);
                $this->changed = true;
            }
        }
    }

    function createUri(){
        if ($this->changed) {
            $this->url = $this->arUrl["path"];
            if (!empty($this->arUrl["query"])) {
                $this->url .= "?" . $this->arUrl["query"];
            }
        }
    }

    function useRedirectList(){
        if ($this->options["use_redirect_urls"] == "Y") {
            if (isset($this->redirects[$this->currentUri])) {
                list($this->url, $this->status) = $this->redirects[$this->currentUri];
                if (substr($this->url, 0, 4) == "http") {
                    $this->isAbsoluteUrl = true;
                }

            } else {
                foreach ($this->redirects as $fromUri => $v) {
                    list($toUri, $this->status, $partUrl) = $v;
                    if ($partUrl != "Y") {
                        continue;
                    }
                    $reFromUri = '{' . str_replace("\*", "(.+?)", preg_quote($fromUri)) . '}s';
                    if (preg_match($reFromUri, $this->currentUri, $m)) {
                        $tmp = [];
                        foreach ($m as $matchIdx => $matchValue) {
                            if ($matchIdx > 0) {
                                $tmp['{' . $matchIdx . '}'] = $matchValue;
                            }
                        }
                        $this->url = str_replace(array_keys($tmp), array_values($tmp), $toUri);
                        break;
                    }
                }

            }
        }
        $this->status = ($this->status == "302")? "302 Found" : "301 Moved Permanently";
    }

    function executeFinalRedirect(){
        if (!empty($this->url)) {
            if ($this->isAbsoluteUrl) {
                LocalRedirect($this->url, true, $this->status);
            } else {
                LocalRedirect($this->protocol . "://" . $this->host . $this->port . $this->url, true, $this->status);
            }
            exit;
        }
    }
}
