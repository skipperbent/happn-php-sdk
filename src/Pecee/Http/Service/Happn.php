<?php

namespace Pecee\Http\Service;

use Pecee\Http\Rest\RestBase;
use Pecee\Http\Service\Exceptions\HappnException;

class Happn extends RestBase
{

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

    const HAPPN_ENDPOINT = 'https://api.happn.fr/';

    protected $fbToken;
    protected $authToken;
    protected $userId;
    protected $lat;
    protected $lon;
    protected $deviceId;

    /**
     * Happn constructor.
     *
     * @param string $facebookToken Facebook access token
     * @param float|null $lat Latitude of your current position
     * @param float|null $lon Longitude for your current position
     * @throws HappnException
     */
    public function __construct($facebookToken, $lat = null, $lon = null)
    {
        parent::__construct();
        $this->fbToken = $facebookToken;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->serviceUrl = static::HAPPN_ENDPOINT;

        $this->authenticate();

        if ($this->lat !== null && $this->lon !== null) {
            $this->setPosition($this->lat, $this->lon);
        }
    }

    /**
     * Set custom headers etc. required for connecting with the Happn api.
     *
     * @param string|null $url
     * @param string $method
     * @param array $data
     * @return \stdClass
     * @throws HappnException
     */
    public function api($url = null, $method = self::METHOD_GET, array $data = [])
    {
        $this->httpRequest->setHeaders([
            'Authorization: OAuth="' . $this->authToken . '"',
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Happn/19.1.0 AndroidSDK/19',
            'Host: api.happn.fr',
            'platform: android',
            'connection: Keep-Alive',
        ]);

        try {

            return json_decode(parent::api($url, $method, $data)->getResponse());
        } catch (\Exception $e) {
            throw new HappnException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
        }
    }

    /**
     * Gets the OAuth tokens using Happn's API
     * @throws HappnException
     */
    protected function authenticate()
    {
        $response = $this->api('connect/oauth/token', static::METHOD_POST, [
                'client_id'      => static::CLIENT_ID,
                'client_secret'  => static::CLIENT_SECRET,
                'grant_type'     => 'assertion',
                'scope'          => 'mobile_app',
                'assertion_type' => 'facebook_access_token',
                'assertion'      => $this->fbToken,
                'redirect_uri'   => 'http://www.happn.fr',

            ]
        );

        if (!$response || !isset($response->access_token)) {
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
     * @return \stdClass
     * @throws HappnException
     */
    public function getUserInfo($userId)
    {
        $query = '{"fields":"about,is_accepted,age,job,workplace,modification_date,profiles.mode(1).width(720).height(1280).fields(url,width,height,mode),last_meet_position,my_relation,is_charmed,distance,gender,my_conversation"}';

        return $this->api('api/users/' . $userId . '?query=' . urlencode($query));
    }

    /**
     * @return \stdClass
     * @throws HappnException
     */
    public function setDevice()
    {
        $payload = [
            "app_build"   => static::APP_BUILD,
            "country_id"  => static::COUNTRY_ID,
            "gps_adid"    => static::GPS_ADID,
            "idfa"        => static::IDFA,
            "language_id" => "en",
            "os_version"  => static::OS_VERSION,
            "token"       => static::GPS_TOKEN,
            "type"        => static::TYPE,
        ];

        return $this->api('api/users/' . $this->userId . '/devices/' . static::DEVICE_ID, static::METHOD_PUT, $payload);
    }

    /**
     * Get recommendations from Happn server
     *
     * @param int $limit Limit
     * @param int $offset Offset
     * @return \stdClass
     * @throws HappnException
     */
    public function getRecommendations($limit = 16, $offset = 0)
    {
        $query = '{"types":"468","limit":\'' . $limit . '\',"offset":\'' . $offset . '\',"fields":"id,user_id,modification_date,notification_type,nb_times,notifier.fields(id,job,is_accepted,workplace,my_relation,distance,gender,my_conversation,is_charmed,nb_photos,first_name,age,profiles.mode(1).width(360).height(640).fields(width,height,mode,url))"}';

        return $this->api('api/users/' . $this->userId . '/notifications/?query=' . urlencode($query));
    }

    /**
     * Fetches the distance from another user
     *
     * @param int $userId User ID of target user.
     * @return \stdClass
     * @throws HappnException
     */
    public function getDistance($userId)
    {
        $query = '{"fields":"id,first_name,gender,last_name,birth_date,login,workplace,distance"}';

        return $this->api('api/users/' . $userId . '/?query=' . urlencode($query));
    }

    /**
     * Set Happn settings
     *
     * @param array $settings
     * @return \stdClass
     * @throws HappnException
     */
    public function setSettings(array $settings)
    {
        $this->getHttpRequest()->setPostJson(true);

        return $this->api('api/users/' . $this->userId, static::METHOD_POST, $settings);
    }

    /**
     * Get user device id
     *
     * @return int
     * @throws HappnException
     */
    public function getDevice()
    {
        if ($this->deviceId === null) {

            $devices = $this->api('api/users/' . $this->userId . '/devices', static::METHOD_GET, []);

            if (isset($devices->data) === false || count($devices->data) === 0) {
                $this->deviceId = static::DEVICE_ID;
            } else {
                $rand = mt_rand(0, count($devices->data) - 1);
                $this->deviceId = $devices->data[$rand]->id;
            }
        }

        return $this->deviceId;
    }

    /**
     * Set the position of the user using Happn's API
     *
     * @param float $lat Latitude to position the User
     * @param float $lon Longitude to position the User
     * @throws HappnException
     * @return \stdClass
     */
    public function setPosition($lat, $lon)
    {
        $this->getHttpRequest()->setPostJson(true);
        $response = $this->api('api/users/' . $this->userId . '/devices/' . $this->getDevice(), static::METHOD_PUT, [
            'alt'       => 0.0,
            'latitude'  => round($lat, 7),
            'longitude' => round($lon, 7),
        ]);

        if (isset($response->data, $response->data->latitude, $response->data->longitude)) {
            $this->lat = $response->data->latitude;
            $this->lon = $response->data->longitude;

            return $response;
        }

        throw new HappnException('Failed to update position');
    }

    /**
     * Updates user activity
     *
     * @return \stdClass
     * @throws HappnException
     */
    public function updateActivity()
    {
        $this->getHttpRequest()->setPostJson(true);

        return $this->api('/api/users/' . $this->userId, static::METHOD_PUT, ['update_activity' => 'true']);
    }

    /**
     * Get Facebook auth token
     *
     * @return string
     */
    public function getFbToken()
    {
        return $this->fbToken;
    }

    /**
     * Get Happn auth token
     *
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * Get Happn user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

}