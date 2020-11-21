<?php

namespace Sim\Cookie;

use Sim\Cookie\Interfaces\ICookie;
use Sim\Cookie\Exceptions\CookieException;
use Sim\Cookie\Interfaces\ISetCookie;
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
     * @throws CookieException
     */
    public function set(ISetCookie $cookie, bool $encrypt = true): ICookie
    {
        if ($cookie instanceof ISetCookie) {
            if (empty($cookie->getName())) {
                throw new CookieException("Cookie's name is invalid! Please enter a valid cookie name.");
            }

            $value = $this->prepareSetCookieValue($cookie->getValue(), $encrypt);

            \setcookie(
                $cookie->getName(),
                $value,
                $cookie->getExpiration(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttponly()
            );

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
}