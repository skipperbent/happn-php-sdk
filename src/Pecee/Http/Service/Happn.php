<?php
namespace Pecee\Http\Service;

use Pecee\Http\Rest\RestBase;
use Pecee\Http\Service\Exceptions\HappnException;

class Happn extends RestBase {

    const CLIENT_ID = 'FUE-idSEP-f7AqCyuMcPr2K-1iCIU_YlvK-M-im3c';
    const CLIENT_SECRET = 'brGoHSwZsPjJ-lBk0HqEXVtb3UFu-y5l_JcOjD-Ekv';

    // GPS Settings
    const APP_BUILD = '18.0.11';
    const COUNTRY_ID = 'US';
    const GPS_ADID = '05596566-c7c7-4bc7-a6c9-729715c9ad98';
    const IDFA = 'f550c51fa242216c';
    const OS_VERSION = 19;
    const GPS_TOKEN = 'APA91bE3axREMeqEpvjkIOWyCBWRO1c4Zm69nyH5f5a7o9iRitRq96ergzyrRfYK5hsDa_-8J35ar7zi5AZFxVeA6xfpK77_kCVRqFmbayGuYy7Uppy_krXIaTAe8Vdd7oUoXJBA7q2vVnZ6hj9afmju9C3vMKz-KA,';
    const TYPE = 'android';
    const DEVICE_ID = 1830658762;

    protected $serviceUrl = 'https://api.happn.fr/';

    protected $fbToken;
    protected $authToken;
    protected $userId;

    /**
     * Happn constructor.
     *
     * @param string $facebookToken Facebook access token
     */
    public function __construct($facebookToken) {
        parent::__construct();
        $this->fbToken = $facebookToken;

        $this->authenticate();
    }

    /**
     * Set custom headers etc. required for connecting with the Happn api.
     *
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

        //$this->httpRequest->setPostJson(true);

        $this->httpRequest->setHeaders(array(
            'Authorization: OAuth="' . $this->authToken . '"',
            'Content-Type: application/x-www-form-urlencoded',
            'http.useragent: Happn/1.0 AndroidSDK/0',
            'User-Agent: Dalvik/1.6.0 (Linux; U; Android 4.4.2; SCH-I535 Build/KOT49H)',
            'Host: api.happn.fr',
            'platform: android',
            'connection: Keep-Alive'
        ));

        return json_decode(parent::api($url, $method, $data)->getResponse());
    }

    /**
     * Gets the OAuth tokens using Happn's API
     * @throws HappnException
     */
    protected function authenticate() {
        $response = $this->api('connect/oauth/token', self::METHOD_POST, array(
                'client_id' => self::CLIENT_ID,
                'client_secret' => self::CLIENT_SECRET,
                'grant_type' => 'assertion',
                'scope' => 'mobile_app',
                'assertion_type' => 'facebook_access_token',
                'assertion' => $this->fbToken,
                'redirect_uri' => 'http://www.happn.fr'

            )
        );

        if(!$response || !isset($response->access_token)) {
            throw new HappnException('Failed to retrieve valid auth-token');
        }

        $this->authToken = $response->access_token;
        $this->userId = $response->user_id;
    }

    /**
     * Fetches user information
     * Returns dictionary packed with:
     * user id, facebook id, twitter id (not implemented), first name, last name, birth date, login (nulled), workplace, distance
     *
     * @param int $userId User ID of target user.
     * @return object
     */
    public function getUserInfo($userId) {
        $query = '{"fields":"about,is_accepted,age,job,workplace,modification_date,profiles.mode(1).width(720).height(1280).fields(url,width,height,mode),last_meet_position,my_relation,is_charmed,distance,gender,my_conversation"}';
        return $this->api('api/users/' . $userId .'?query=' . urlencode($query));
    }

    public function setDevice() {
        $payload = [
            "app_build" => static::APP_BUILD,
            "country_id" => static::COUNTRY_ID,
            "gps_adid" => static::GPS_ADID,
            "idfa" => static::IDFA,
            "language_id" => "en",
            "os_version" => static::OS_VERSION,
            "token" => static::GPS_TOKEN,
            "type" => static::TYPE
        ];

        return $this->api('api/users/' . $this->userId . '/devices/', self::METHOD_PUT, $payload);
    }

    /**
     * Get recommendations from Happn server
     *
     * @param int $limit Limit
     * @param int $offset Offset
     * @return object
     */
    public function getRecommendations($limit = 16, $offset = 0) {
        $query = '{"types":"468","limit":\''. $limit .'\',"offset":\''. $offset .'\',"fields":"id,user_id,modification_date,notification_type,nb_times,notifier.fields(id,job,is_accepted,workplace,my_relation,distance,gender,my_conversation,is_charmed,nb_photos,first_name,age,profiles.mode(1).width(360).height(640).fields(width,height,mode,url))"}';
        return $this->api('api/users/'. $this->userId .'/notifications/?query=' . urlencode($query));
    }

    /**
     * Fetches the distance from another user
     *
     * @param int $userId User ID of target user.
     * @return object
     */
    public function getDistance($userId) {
        $query = '{"fields":"id,first_name,gender,last_name,birth_date,login,workplace,distance"}';
        return $this->api('api/users/' . $userId . '/?query=' . urlencode($query));
    }

    /**
     * Set Happn settings
     *
     * @param array $settings
     * @return object
     */
    public function setSettings(array $settings) {
        $this->getHttpRequest()->setPostJson(true);
        return $this->api('api/users/' . $this->userId, self::METHOD_POST, $settings);
    }

    /**
     * Set the position of the user using Happn's API
     *
     * @param float $lat Latitude to position the User
     * @param float $lon Longitude to position the User
     * @return object
     */
    public function setPosition($lat, $lon) {
        $this->getHttpRequest()->setPostJson(true);
        return $this->api('api/users/' . $this->userId . '/position/', self::METHOD_PUT, [
            'alt' => 0.0,
            'latitude' => round($lat, 7),
            'longitude' => round($lon, 7)
        ]);
    }

    /**
     * Updates user activity
     *
     * @return object
     */
    public function updateActivity() {
        $this->getHttpRequest()->setPostJson(true);
        return $this->api('/api/users/' . $this->userId, self::METHOD_PUT, array('update_activity' =>  'true'));
    }

    /**
     * Get Facebook auth token
     *
     * @return string
     */
    public function getFbToken() {
        return $this->fbToken;
    }

    /**
     * Get Happn auth token
     *
     * @return string
     */
    public function getAuthToken() {
        return $this->authToken;
    }

    /**
     * Get Happn user id
     *
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

}