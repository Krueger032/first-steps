<?php

namespace Aspro\Max\Social\OAuth;

use Aspro\Max\Social\Vk;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\Json;
use CMax as Solution;

class VKID
{
    use \Aspro\Max\Social\Traits\Common;

    protected const REQUEST_URL = 'https://id.vk.ru/';

    protected string $code;
    protected string $codeChallenge;
    protected string $verifiyCode;
    protected string $stateParamName;

    public function __construct(string $siteId)
    {
        $application = Application::getInstance();

        $this->request = $application->getContext()->getRequest();
        $this->session = $application->getSession();

        $this->siteId = $siteId;
        $this->clientId = Solution::getFrontParametrValue('VK_API_CLIENT_ID', $this->siteId);

        $this->stateParamName = Solution::moduleID.'_vk_oauth_state_'.$this->siteId;
        $this->apiClientIdParamName = Solution::moduleID.'_vk_api_client_id_'.$this->siteId;
    }

    public function authorize()
    {
        LocalRedirect($this->getAuthorizationUrl());
    }

    public function getAccessToken(): string
    {
        $tokenData = Option::get(Solution::moduleID, '_VK_API_TOKEN_DATA', '', $this->siteId);
        if ($tokenData) {
            /**
             * @var string $tokenData[access_token] token for VK API requests
             * @var string $tokenData[refresh_token] token for refreshing acess_token if it expires
             * @var string $tokenData[expires] timestamp of token expires
             * @var string $tokenData[device_id] id of device for refreshing token
             */
            $tokenData = Json::decode($tokenData);

            if ($tokenData['expires'] <= time()) {
                $data = $this->getPostRequest(static::REQUEST_URL.'oauth2/auth', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $tokenData['refresh_token'],
                    'client_id' => $this->clientId,
                    'device_id' => $tokenData['device_id'],
                    'state' => bin2hex(random_bytes(16)),
                ]);

                if (!$data['error']) {
                    $tokenData['access_token'] = $data['access_token'];
                    $tokenData['refresh_token'] = $data['refresh_token'];
                    $tokenData['expires'] = time() + $data['expires_in'];

                    Option::set(Solution::moduleID, '_VK_API_TOKEN_DATA', Json::encode($tokenData), $this->siteId);
                }
            }

            return $tokenData['access_token'];
        }

        return '';
    }

    public function checkState(): bool
    {
        $sessionState = $this->session->get($this->stateParamName);
        $this->session->remove($this->stateParamName);

        return $sessionState === $this->request['state'];
    }

    public function setAccessTokenWithAuthorizationCode(string $code)
    {
        $clientId = $this->session->get($this->apiClientIdParamName);
        Option::set(Solution::moduleID, 'VK_API_CLIENT_ID', $clientId, $this->siteId);
        Option::set(Solution::moduleID, 'VK_API_METHOD', 'ID', $this->siteId);

        $deviceId = trim(strip_tags(htmlspecialcharsbx($this->request['device_id'])));
        $codeVerifier = $this->session->get(Solution::moduleID.'_vk_code_verifier');
        $this->session->remove(Solution::moduleID.'_vk_code_verifier');

        $data = $this->getPostRequest(static::REQUEST_URL.'oauth2/auth', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'code_verifier' => $codeVerifier,
            'client_id' => $clientId,
            'device_id' => $deviceId,
            'redirect_uri' => static::getRedirectUrl($this->siteId),
            'state' => bin2hex(random_bytes(16)),
        ]);

        if (!$data['error']) {
            $tokenData = [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'device_id' => $deviceId,
                'expires' => time() + $data['expires_in'],
                'client_id' => $clientId,
            ];

            Option::set(Solution::moduleID, '_VK_API_TOKEN_DATA', Json::encode($tokenData), $this->siteId);
            ?>
            <script>
                if (window.opener) {
                    window.opener.postMessage({
                        type: 'vk_auth_callback',
                        status: 'ok',
                    }, "<?= ($_SERVER['HTTPS'] ? 'https://' : 'http://').$_SERVER['SERVER_NAME']; ?>");

                    window.close();
                }
            </script>
            <?php
        }
    }

    protected function getAuthorizationURL()
    {
        $clientId = trim(htmlspecialcharsbx($this->request['client_id']));
        if (!$clientId) {
            $clientId = Solution::getFrontParametrValue('VK_API_CLIENT_ID', $this->siteId);
        }

        $this->session->set($this->apiClientIdParamName, $clientId);

        $codeVerifier = bin2hex(random_bytes(32));
        $this->session->set(Solution::moduleID.'_vk_code_verifier', $codeVerifier);

        $codeChallenge = strtr(
            rtrim(
                base64_encode(hash('sha256', $codeVerifier, true)),
                '='
            ),
            '+/',
            '-_'
        );
        $state = bin2hex(random_bytes(16));

        $this->session->set($this->stateParamName, $state);

        return static::REQUEST_URL.'authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'scope' => 'video wall offline',
            'redirect_uri' => static::getRedirectUrl($this->siteId),
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);
    }

    public static function getRedirectUrl(?string $siteId = ''): string
    {
        $protocol = ($_SERVER['HTTPS'] ? 'https://' : 'http://');
        $baseUrl = $protocol.$_SERVER['SERVER_NAME'].'/bitrix/tools/'.Solution::moduleID.'/vk_oauth.php';
        $urlParams = [];
        if ($siteId) {
            $urlParams['site_id'] = $siteId;
        }

        return $baseUrl.($urlParams ? '?'.http_build_query($urlParams) : '');
    }
}
