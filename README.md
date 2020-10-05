# Simplicity Cookie
A library for cookie management.

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

## Available functions

#### SetCookie

If you need to set a cookie, then you should pass an instance 
of `SetCookie` class.

```php
$the_cookie = new SetCookie($cookie_name, $cookie_value, $cookie_expiration);
// or
$the_cookie = new SetCookie();
$the_cookie->setName($cookie_name)
           ->setValue($cookie_value)
           ->setExpire($cookie_expiration);
```

The parameters you send to construct is the same type as using 
available methods.

__construct(?string $name = null, string $value = "", ?int $expire = null, ?string $path = '/', $domain = null, $secure = null, $httponly = null)

|  Parameter  |  Type  |  Default  |
|:------------|:-------|:----------|
|   $name     |?string |    null   |
|   $value    | string |    ""     |
|   $expire   |  int   |    null   |
|   $path     | string |    null   |
|   $domain   | string |    null   |
|   $secur e  |  bool  |    null   |
|  $httponly  |  bool  |    null   |

`setName($name): ISetCookie`

Set cookie name with this method.

```php
// to add an event
$set_cookie->setName($cookie_name);
```

`getName()`

Get cookie name.

```php
$cookie_name = $set_cookie->getName();
```

`setValue(string $value): ISetCookie`

Set cookie's value

```php
$cookie_name = $set_cookie->setValue("A not important but need to store value");
```

`getValue()`

Get cookie value.

```php
$cookie_value = $set_cookie->getValue();
```

`setExpiration($expire): ISetCookie`

Set cookie expiration.

Note: Just enter the amount of time you need from now in seconds 
like 300.

Note: Also can pass string time like [+5 days]

```php
// following code will set expiration of cookie 300s after now
$set_cookie->setExpiration(time() + 300);
```

`getExpiration(): int`

Get cookie expiration.

```php
$expiration = $set_cookie->getExpiration();
```

`setPath(string $path): ISetCookie`

Set cookie path.

```php
$set_cookie->setPath('/');
```

`getPath(): string`

Get cookie path.

```php
$path = $set_cookie->getPath();
```

`setDomain(string $domain): ISetCookie`

Set cookie domain.

```php
$set_cookie->setDomain('www.yourdomain.com');
// or
$set_cookie->setDomain('.yourdomain.com');
```

`getDomain()`

Get cookie domain.

```php
$domain = $set_cookie->getDomain();
```

`setSecure(bool $answer): ISetCookie`

Set cookie secure parameter.

```php
$set_cookie->setSecure(true);
```

`isSecure(): bool`

Get cookie is secure or not.

```php
$secure = $set_cookie->isSecure();
```

`setHttpOnly(?bool $answer): ISetCookie`

Set the cookie is http only or not.

```php
$set_cookie->setHttpOnly(true);
```

`isHttpOnly(): bool`

Get cookie is http only or not.

```php
$httponly = $set_cookie->isHttpOnly();
```

#### Cookie

`set(ISetCookie $cookie, bool $encrypt = true): ICookie`

Set a cookie by passing an ISetCookie as parameter.

Note: If you use [Crypt][1] library as a dependency, you can pass a 
boolean to tell this library use encryption on cookies or not.

|  Parameter  |    Type    |  Default  |
|-------------|------------|-----------|
|   $cookie   | ISetCookie |           |
|   $encrypt  |    bool    |   true    |

```php
// set a cookie
$cookie->set(ISetCookie $set_cookie);
``` 

`get(?string $name = null, $prefer = null)`

Get a cookie by passing the name of it. If cookie is not exists, the 
$prefer parameter will return.

Note: If you need all cookies to return, do not pass any parameter or 
pass null as first parameter.

|  Parameter  |    Type    |  Default  |
|-------------|------------|-----------|
|    $name    |  ?string   |    null   |
|   $prefer   |   mixed    |    null   |

```php
// get a cookie with name cart_items
$cart_items = $cookie->get('cart_items');
```

`getAsString(?string $name = null, bool $decrypt = false): string`

Get cookie as a semicolon separated key=value.

You can use this string in headers.

Note: If you need all cookies to return as this type, do not pass any 
parameter or pass nul as first parameter.
Note: If you use [Crypt][1] library and need decrypted cookies, pass 
$decrypt as true boolean.

|  Parameter  |    Type    |  Default  |
|-------------|------------|-----------|
|    $name    |  ?string   |    null   |
|  $decrypt   |    bool    |   false   |

```php
// get all cookies as string value
$cookie_string = $cookie->getAsString();

// an example of output
"Cookie: cart_items=encoded_json; another-cookie-val=apropriate value"
```

`remove(string $name): ICookie`

Remove a cookie with specific name.

```php
$cookie->remove('cart_items');
```

`has(string $name): bool`

Check if a cookie exists.

```php
$has_cart = $cookie->has('cart_items');
```

# Dependencies
There is just one dependency and it is [Crypt][1] library. With this 
feature, if any cookie hijacking happens, they can't see actual 
data because it is encrypted.

# License
Under MIT license.

[1]: https://github.com/mmdm95/sim-crypt
