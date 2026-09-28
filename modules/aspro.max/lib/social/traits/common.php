<?php

namespace Aspro\Max\Social\Traits;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

trait Common
{
    protected $error;

    protected function prepareHttpClient(string $contentType = '')
    {
        $httpClient = new HttpClient();
        $httpClient->setTimeout(30);
        if ($contentType) {
            $httpClient->setHeader('Content-Type', $contentType);
        }

        $httpClient->setStreamTimeout(30);

        return $httpClient;
    }

    protected function getPostRequest(string $url, array $params): array
    {
        $data = [];
        try {
            $httpClient = $this->prepareHttpClient('application/x-www-form-urlencoded');

            $data = $httpClient->post($url, $params);
            $data = Json::decode($data);
        } catch (SystemException $e) {
            $data = $this->getErrorArray($e->getMessage());
        }

        return $data;
    }

    protected function getRequest(string $url): array
    {
        try {
            $httpClient = $this->prepareHttpClient();

            $data = $httpClient->get($url);
            $data = Json::decode($data);
        } catch (SystemException $e) {
            $data = $this->getErrorArray($e->getMessage());
        }

        return $data;
    }

    protected function checkApiToken()
    {
        if (!strlen($this->access_token)) {
            $arString = explode('\\', static::class);
            $service = end($arString);

            $this->error = Loc::getMessage('NO_API_TOKEN', ['#SERVICE#' => strtoupper($service)]);
        }
    }

    protected function getErrorArray($errorCode): array
    {
        return [
            'error' => [
                'error_msg' => Loc::getMessage($errorCode) ?: $errorCode,
            ],
        ];
    }
}
