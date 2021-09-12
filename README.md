# Simplicity Cookie
A library for cookie management.

## Features

- Manage your cookies
- Encrypt cookie with [Crypt][1] library(Optional)
- Manage SameSite
- Check useragent's support for SameSite

## Install
**composer**
```php 
composer require mmdm/sim-cookie
```

Or you can simply download zip file from github and extract it, 
then put file to your project library and use it like other libraries.

Just add line below to autoload files:

```php
require_once 'path_to_library/autoloader.php';
```

and you are good to go.

## How to use
```php
// to instantiate a cookie object
$cookie = new Cookie();

// then use cookie methods like
$cookie->get($cookie_name);
```

## Use with Crypt library
If you need more security on cookies, use [Crypt][1] library.

```php
// send crypt instance through cookie
// constructure (dependency injection)
$cookie = new Cookie($crypt);
// now your cookies are safe
```

If you don't need some of your cookies to be secure, pass false 
as last parameter of setValue of `ISetCookie` method.

## Available methods

### Cookie

#### `parse(string $cookie_string, bool $decode = false, bool $encrypt = true): ?ISetCookie`

Send a cookie string to parse it into `SetCookie` object or null if 
it can't parse.

**Note:** 

To decode value of parsed string, pass true to `$decode` parameter.

To encrypt parsed value, pass true as `$encrypt` parameter.

|  Parameter   |    Type    |  Default  |
|--------------|------------|-----------|
|$cookie_string| ISetCookie |           |
|   $decode    |    bool    |   false   |
|   $encrypt   |    bool    |   true    |

#### `set(string $name): ISetCookie`

Create new cookie object and can modify all inputs after `set`
method.

Please refer to [SetCookie Available Methods][setCookie] for 
more information.

#### `get(?string $name = null, $prefer = null)`

Get cookie's value or `$prefer` if not defined.

**Note**

To get all cookies, do not send any parameter.

#### `getAsString(?string $name = null, bool $decode = true, bool $decrypt = false): string`

Get string for a cookie.

**Note**

To get all cookies as string, send null as `$name`

#### `remove(string $name): ICookie`

Remove a cookie(event from `$_COOKIE` variable).

#### `has(string $name): bool`

Check if there is cookie with specific name.

#### `getCookieValueFromString(string $str)`

Get(decrypted) value.

**Note** 

It is useful when there is a cookie value is inside a 
header and want get actual value and check it.

```php
$value = $cookie->getCookieValueFromString($the_cookie_value_that_is_encrypted_or_raw);
```

---

### SetCookie Available Methods

#### `getName(): ?string`

Get cookie name.

#### `setValue(?string $value, bool $encrypt = true): ISetCookie`

Set value of cookie.

**Note**

To encrypt value pass *true* as second parameter.

#### `getValue(): string`

Get cookie value.

#### `setExpiration($expire): ISetCookie`

Set expiration to cookie.

#### `getExpiration(): int`

Get expiration of cookie.

#### `setPath(?string $path): ISetCookie`

Set cookie path.

#### `getPath(): ?string`

Get cookie path.

#### `setDomain(?string $domain): ISetCookie`

Set domain of cookie.

#### `getDomain(): ?string`

Get cookie domain.

#### `setSecure(?bool $answer): ISetCookie`

Set secure string to cookie.

#### `isSecure(): ?bool`

Check if secure string set or not.

#### `setHttpOnly(?bool $answer): ISetCookie`

Set httponly string to cookie.

#### `isHttpOnly(): ?bool`

Check if httponly set or not.

#### `setSameSite(?string $same_site): ISetCookie`

Set same site for modern browsers.

**Note**

Acceptable values are:

- ICookie::SAME_SITE_NONE - is `None` string 

- ICookie::SAME_SITE_LAX - is `Lax` string

- ICookie::SAME_SITE_STRICT - is `Strict` string

#### `getSameSite(): ?string`

Get samesite if set before or `null`.

#### `setExtra(string $extra): ISetCookie`

Set extra string for cookie (if needed and supported).

#### `getExtra(): string`

Get extra cookie string

#### `setUseragent(?string $useragent = null): ISetCookie`

Set useragent for check against to see if it support samesite 
or not.

**Note**

By default it'll get browser from `$_SERVER`.

#### `getUseragent(): ?string`

Get useragent.

**Note**

This method will return the useragent you set not useragent from 
`$_SERVER` variable.

#### `toString(bool $decode = true, bool $decrypt = false): string`

Get cookie header of this cookie as string

Example of output:

```php
"Set-Cookie: tmp-cookie=A simple cookie; expires=Sat, 03-Apr-2021 10:45:55 GMT; Max-Age=60"
"Set-Cookie: tmp-cookie=A simple cookie number 2; expires=Sat, 03-Apr-2021 10:45:55 GMT; Max-Age=60; secure; SameSite=None"
```

#### `save(bool $encode = true): bool`

Save configured cookie. `$encode` will encode `value`.

**Note**

You **MUST** call this method to save cookie otherwise you missed 
your cookie.

# Dependencies
There is just one dependency and it is [Crypt][1] library. With this 
feature, if any cookie hijacking happens, they can't see actual 
data because it is encrypted.

# License
Under MIT license.

[1]: https://github.com/mmdm95/sim-crypt
[2]: https://www.chromium.org/updates/same-site/incompatible-clients
[setCookie]: #setcookie-available-methods