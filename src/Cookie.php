<?php

namespace Sim\Cookie;

use Sim\Cookie\Interfaces\ICookie;
use Sim\Cookie\Exceptions\CookieException;
use Sim\Cookie\Interfaces\ISetCookie;
use Sim\Cookie\Utils\CookieUtil;
use Sim\Crypt\ICrypt;

class Cookie implements ICookie
{
    /**
     * @var ICrypt|null $crypt
     */
    protected $crypt = null;

    /**
     * Cookie constructor.
     * @param ICrypt|null $crypt
     */
    public function __construct(ICrypt $crypt = null)
    {
        $this->crypt = $crypt;
    }

    /**
     * {@inheritdoc}
     * @see https://github.com/delight-im/PHP-Cookie/blob/a87055f755514f5e3285dbaf02bda2f2f6294f69/src/Cookie.php
     * @throws CookieException
     */
    public function parse(string $cookie_string, bool $decode = false, bool $encrypt = true): ?ISetCookie
    {
        if (empty($cookie_string)) {
            return null;
        }

        // do parse
        if (\preg_match('/^' . ISetCookie::COOKIE_HEADER . '(.*?)=(.*?)(?:; (.*?))?$/i', $cookie_string, $matches)) {
            $cookie = new SetCookie($matches[1]);

            $cookie->setPath(null);
            $cookie->setHttpOnly(false);
            $value = $decode ? \urldecode($matches[2]) : $matches[2];
            $cookie->setValue($value, $encrypt);
            $cookie->setSameSite(null);

            if (\count($matches) >= 4) {
                $attributes = \explode('; ', $matches[3]);

                foreach ($attributes as $attribute) {
                    if (\strcasecmp($attribute, 'HttpOnly') === 0) {
                        $cookie->setHttpOnly(true);
                    } elseif (\strcasecmp($attribute, 'Secure') === 0) {
                        $cookie->setSecure(true);
                    } elseif (\stripos($attribute, 'Expires=') === 0) {
                        $cookie->setExpiration((int)\strtotime(\substr($attribute, 8)));
                    } elseif (\stripos($attribute, 'Domain=') === 0) {
                        $cookie->setDomain(\substr($attribute, 7));
                    } elseif (\stripos($attribute, 'Path=') === 0) {
                        $cookie->setPath(\substr($attribute, 5));
                    } elseif (\stripos($attribute, 'SameSite=') === 0) {
                        $cookie->setSameSite(\substr($attribute, 9));
                    }
                }
            }

            return $cookie;
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     * @throws CookieException
     */
    public function set(string $name): ISetCookie
    {
        return new SetCookie($name, $this->crypt);
    }

    /**
     * {@inheritdoc}
     */
    public function get(?string $name = null, $prefer = null)
    {
        // To key specific cookie
        if (!empty($name)) {
            if ($this->has($name)) {
                return CookieUtil::prepareGetCookieValue($this->crypt, $_COOKIE[$name]);
            }
            return $prefer;
        }

        // To get all cookies
        $cookies = $_COOKIE;
        foreach ($cookies as $k => $value) {
            $cookies[$k] = CookieUtil::prepareGetCookieValue($this->crypt, $value);
        }

        return $cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function getAsString(?string $name = null, bool $decrypt = false): string
    {
        $cookieArr = [];
        if ($decrypt) {
            $cookie = $this->get($name);
        } else {
            $cookie = !\is_null($name) ? $_COOKIE[$name] : $_COOKIE;
        }
        if (!\is_null($cookie)) {
            if (\is_array($cookie)) {
                foreach ($cookie as $name => $cookieValue) {
                    if (!\is_null($cookieValue)) {
                        $cookieArr[] = $name . '=' . $cookieValue;
                    }
                }
            } elseif (!\is_null($cookie)) {
                $cookieArr[] = $name . '=' . $cookie;
            }
        }

        $cookieString = 'Cookie: ' . \implode('; ', $cookieArr) . "\r\n";
        return $cookieString;
    }

    /**
     * {@inheritdoc}
     * @throws CookieException
     */
    public function remove(string $name): ICookie
    {
        $cookie = $this->get($name);
        if (!\is_null($cookie)) {
            $this->set($name)->setValue('')->setExpiration(time() - 3600);
            unset($_COOKIE[$name]);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieValueFromString(string $str)
    {
        return CookieUtil::prepareGetCookieValue($this->crypt, $str);
    }
}