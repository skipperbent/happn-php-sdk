# [WIP] happn-php-sdk

**NOTE: under development, please wait for a release - should be availible soon**

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

## Examples

This section contains basic examples on how to use the SDK.

### Authentication

```php
$happn = new \Pecee\Http\Service\Happn($fbToken);
```

## The MIT License (MIT)

Copyright (c) 2015 Simon Sessingø / simple-php-router

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
