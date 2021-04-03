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

// then use cookies method like
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
as last parameter of set method.

## It will document soon

Usage got simpler but method are almost same as before with 
different signature...

```php
$value = $cookie->prepareGetCookieValue('the cookie value that is hashed or raw');
```

# Dependencies
There is just one dependency and it is [Crypt][1] library. With this 
feature, if any cookie hijacking happens, they can't see actual 
data because it is encrypted.

# License
Under MIT license.

[1]: https://github.com/mmdm95/sim-crypt
[2]: https://www.chromium.org/updates/same-site/incompatible-clients