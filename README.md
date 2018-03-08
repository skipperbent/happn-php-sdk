# happn-php-sdk

Easy to use PHP SDK for accessing Happn data.

## Installation
Add the latest version of happn-php-sdk to your ```composer.json```

```json
{
    "require": {
        "pecee/happn-php-sdk": "1.*"
    },
    "require-dev": {
        "pecee/happn-php-sdk": "1.*"
    }
}
```

## Like this?

Like this SDK? Then you'll definetly love our "tinder-php-sdk", check it out here:
https://github.com/skipperbent/tinder-php-sdk

## Examples

This section contains basic examples on how to use the SDK.

### Getting Facebook access token

To use the SDK - the api requires you to get a valid Facebook access token. This can be accomplished by following [this link](https://www.facebook.com/dialog/oauth?client_id=247294518656661&redirect_uri=https://www.happn.fr&scope=basic_info,email,public_profile,user_about_me,user_activities,user_birthday,user_education_history,user_friends,user_interests,user_likes,user_location,user_photos,user_relationship_details&response_type=token) and copying the ```access_token``` parameter provided by Facebook after a successful login.

### Authentication

Connect to the service, using your Facebook token.

```php
$happn = new \Pecee\Http\Service\Happn($fbToken);
```

### Get user information

Fetches user information

Returns dictionary packed with:
user id, facebook id, twitter id (not implemented), first name, last name, birth date, login (nulled), workplace, distance

```php
$response = $happn->getUserInfo($userId);
```

### Get recommendations

Get recommendations from Happn server to the user authenticated.

```php
$response = $happn->getRecommendations($limit = 16, $offset = 0);
```

### Get distance

Fetches the distance from another user

```php
$response = $happn->getDistance($userId);
```

### Get Happn settings

**Note: Will be updated soon with correct parameters**

Set Happn settings.

```php
$response = $happn->setSettings(array $userId);
```

### Set position

Set the position of the user using Happn's API

```php
$response = $happn->setPosition($lat, $lon);
```

### Set position

Updates user activity

```php
$response = $happn->updateActivity();
```

### Get user id

Returns user id of the currently authenticated user

```php
$response = $happn->getUserId();
```

### Get Facebook access token

Returns Facebook access token of the currently authenticated user

```php
$response = $happn->getFbToken();
```

### Custom query

Make custom query upon Happn api.

```php
$response = $happn->api($url, $method, array $data);
```

## The MIT License (MIT)

Copyright (c) 2015 Simon Sessingø / happn-php-sdk

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
