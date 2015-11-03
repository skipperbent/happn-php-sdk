<?php
namespace Pecee\Http\Service;

use Pecee\Http\Rest\RestBase;

class Happn extends RestBase {

    const CLIENT_ID = 'FUE-idSEP-f7AqCyuMcPr2K-1iCIU_YlvK-M-im3c';
    const CLIENT_SECRET = 'brGoHSwZsPjJ-lBk0HqEXVtb3UFu-y5l_JcOjD-Ekv';

    protected $serviceUrl = 'https://api.happn.fr/';

    protected $fbToken;
    protected $authToken;
    protected $userId;

    public function __construct($facebookToken) {
        parent::__construct();
        $this->fbToken = $facebookToken;

        $this->authenticate();
    }

    /**
     * @param null $url
     * @param string $method
     * @param array $data
     * @return object
     * @throws \Pecee\Http\Rest\RestException
     */
    public function api($url = null, $method = self::METHOD_GET, array $data = array()) {

        $this->httpRequest->setOptions(array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ));

        $this->httpRequest->setPostJson(true);

        $this->httpRequest->setHeaders(array(
            'Content-type: application/x-www-form-urlencoded; charset=UTF-8',
            'http.useragent: Happn/1.0 AndroidSDK/0',
            'User-Agent: Dalvik/1.6.0 (Linux; U; Android 4.4.2; SCH-I535 Build/KOT49H)',
            'Host: api.happn.fr',
            'platform: ios',
            'connection: Keep-Alive',
            'Accept-Encoding: gzip'
        ));

        return json_decode(parent::api($url, $method, $data)->getResponse());
    }


    protected function authenticate() {
        $response = $this->api('connect/oauth/token', self::METHOD_POST, array(
                'client_id' => self::CLIENT_ID,
                'client_secret' => self::CLIENT_SECRET,
                'grant_type' => 'assertion',
                'assertion' => $this->fbToken,
                'scope' => 'mobile_app'
            )
        );
        if($response && isset($response->access_token)) {
            $this->authToken = $response->access_token;
            $this->userId = $response->user_id;
        }
    }

    /**
     * @return string
     */
    public function getFbToken() {
        return $this->fbToken;
    }

    /**
     * @return string
     */
    public function getAuthToken() {
        return $this->authToken;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

}