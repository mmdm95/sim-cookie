<?php

namespace Sim\Cookie;

use Sim\Cookie\Interfaces\ICookie;
use Sim\Cookie\Exceptions\CookieException;
use Sim\Cookie\Interfaces\ISetCookie;
use Sim\Cookie\Utils\SameSiteUtil;
use Sim\Crypt\Crypt;

class Cookie implements ICookie
{
    /**
     * @var Crypt|null $crypt
     */
    protected $crypt = null;

    /**
     * Cookie constructor.
     * @param Crypt|null $crypt
     */
    public function __construct(Crypt $crypt = null)
    {
        $this->crypt = $crypt;
    }

    /**
     * {@inheritdoc}
     * @see https://github.com/delight-im/PHP-Cookie/blob/a87055f755514f5e3285dbaf02bda2f2f6294f69/src/Cookie.php
     */
    public function parse(string $cookie_string, bool $decode = false): ?ISetCookie
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
            $cookie->setValue($value);
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
    public function set(ISetCookie $cookie, bool $encrypt = true, ?string $useragent = null): ICookie
    {
        if ($cookie instanceof ISetCookie) {
            if (empty($cookie->getName())) {
                throw new CookieException("Cookie's name is invalid! Please enter a valid cookie name.");
            }

            if (empty($useragent)) {
                $useragent = SameSiteUtil::getUserAgent();
            }
            if (!SameSiteUtil::shouldSendSameSiteNone($useragent)) {
                $cookie->setSameSite(null);
            }

            $value = $this->prepareSetCookieValue($cookie->getValue(), $encrypt);
            $this->setCookieToHeader($this->toString($cookie, false, $encrypt));

            $_COOKIE[$cookie->getName()] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(?string $name = null, $prefer = null)
    {
        // To key specific cookie
        if (!empty($name)) {
            if ($this->has($name)) {
                return $this->prepareGetCookieValue($_COOKIE[$name]);
            }
            return $prefer;
        }

        // To get all cookies
        $cookies = $_COOKIE;
        foreach ($cookies as $k => $value) {
            $cookies[$k] = $this->prepareGetCookieValue($value);
        }

        return $cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function getAsString(?string $name = null, bool $decrypt = false): string
    {
        $cookieString = '';
        if ($decrypt) {
            $cookie = $this->get($name);
        } else {
            $cookie = !is_null($name) ? $_COOKIE[$name] : $_COOKIE;
        }
        if (!is_null($cookie)) {
            $cookieString = 'Cookie: ';
            if (is_array($cookie)) {
                foreach ($cookie as $name => $cookieValue) {
                    if (!is_null($cookieValue)) {
                        $cookieString .= $name . '=' . $cookieValue . '; ';
                    }
                }
            } elseif (!is_null($cookie)) {
                $cookieString .= $name . '=' . $cookie . '; ';
            }
        }

        $cookieString = trim(trim($cookieString), ';');
        return $cookieString;
    }

    /**
     * {@inheritdoc}
     * @throws CookieException
     */
    public function remove(string $name): ICookie
    {
        $cookie = $this->get($name);
        if (!is_null($cookie)) {
            $this->set(new SetCookie($name, "", time() - 3600));
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
     * @see https://github.com/delight-im/PHP-Cookie/blob/a87055f755514f5e3285dbaf02bda2f2f6294f69/src/Cookie.php
     */
    public function toString(ISetCookie $cookie, bool $decode = false, bool $encrypt = false): string
    {
        $expireTime = $cookie->getExpiration();

        $maxAgeStr = 0 === $expireTime ? 0 : ($expireTime - \time());
        $maxAgeStr = ((\PHP_VERSION_ID >= 70019 && \PHP_VERSION_ID < 70100) || \PHP_VERSION_ID >= 70105)
            ? ($maxAgeStr < 0
                ? '0'
                : (string)$maxAgeStr)
            : (string)$maxAgeStr;
        $expireTimeStr = \gmdate('D, d-M-Y H:i:s T', $expireTime);

        $value = $this->prepareSetCookieValue($decode ? \urldecode($cookie->getValue()) : $cookie->getValue(), $encrypt);
        $headerStr = ISetCookie::COOKIE_HEADER . $cookie->getName() . '=' . $value;

        if ($expireTime > 0) {
            $headerStr .= '; expires=' . $expireTimeStr;
        }
        if ($expireTime > 0) {
            $headerStr .= '; Max-Age=' . $maxAgeStr;
        }

        $path = $cookie->getPath();
        if (!empty($path)) {
            $headerStr .= '; path=' . $path;
        }

        $domain = $cookie->getDomain();
        if (!empty($domain)) {
            $headerStr .= '; domain=' . $domain;
        }

        if ($cookie->isSecure()) {
            $headerStr .= '; secure';
        }
        if ($cookie->isHttpOnly()) {
            $headerStr .= '; httponly';
        }

        $sameSite = $cookie->getSameSite();
        if (!is_null($sameSite)) {
            if ($sameSite === ISetCookie::SAME_SITE_NONE) {
                if (!$cookie->isSecure()) {
                    $headerStr .= '; secure';
                }
                $headerStr .= '; SameSite=None';
            } elseif ($sameSite === ISetCookie::SAME_SITE_LAX) {
                $headerStr .= '; SameSite=Lax';
            } elseif ($sameSite === ISetCookie::SAME_SITE_STRICT) {
                $headerStr .= '; SameSite=Strict';
            }
        }

        $extra = ltrim(trim((string)$cookie->getExtra()), ';');
        $headerStr .= !empty($extra) ? '; ' . $extra : '';

        return $headerStr;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareGetCookieValue($arrPrev)
    {
        $arr = json_decode($arrPrev, true);
        if (is_null($arr)) return $arrPrev;
        if (!isset($arr['_simplicity__data']) || !isset($arr['_simplicity__is_encrypted'])) return $arrPrev;

        $value = $arr['_simplicity__data'];
        if ($arr['_simplicity__is_encrypted'] && !is_null($this->crypt)) {
            $value = $this->crypt->decrypt($value);
            $value = $this->crypt->hasError() ? null : $value;
        }
        if (is_string($value)) {
            $value = htmlspecialchars_decode($value);
        }
        return $value;
    }

    /**
     * Prepare cookie value to store (check if encryption need)
     *
     * @param $value
     * @param $encrypt
     * @return mixed
     */
    protected function prepareSetCookieValue($value, $encrypt)
    {
        if (is_string($value)) {
            $value = htmlspecialchars($value);
        }
        if ((bool)$encrypt && !is_null($this->crypt)) {
            $value = $this->crypt->encrypt($value);
            $value = $this->crypt->hasError() ? "" : $value;
        }
        return json_encode([
            '_simplicity__data' => $value,
            '_simplicity__is_encrypted' => $encrypt,
        ]);
    }

    /**
     * Add a cookie string to header
     *
     * Note: If headers are already sent,
     *       it'll return false otherwise,
     *       return true
     *
     * @param string $cookie
     * @return bool
     */
    protected function setCookieToHeader(string $cookie): bool
    {
        if (!\headers_sent() && !empty($cookie)) {
            \header($cookie, false);
            return true;
        } else {
            return false;
        }
    }
}