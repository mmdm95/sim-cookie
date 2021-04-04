<?php

namespace Sim\Cookie;

use Sim\Cookie\Exceptions\CookieException;
use Sim\Cookie\Interfaces\ICookie;
use Sim\Cookie\Interfaces\ISetCookie;
use Sim\Cookie\Utils\CookieUtil;
use Sim\Cookie\Utils\SameSiteUtil;
use Sim\Crypt\ICrypt;

class SetCookie implements ISetCookie
{
    /**
     * @var ICrypt|null
     */
    protected $crypt;

    /**
     * @var string|null $name
     */
    protected $name = null;

    /**
     * @var string $value
     */
    protected $value = "";

    /**
     * @var int $expire
     */
    protected $expire = 0;

    /**
     * @var string $path
     */
    protected $path = "/";

    /**
     * @var string $domain
     */
    protected $domain = "";

    /**
     * @var bool $secure
     */
    protected $secure = false;

    /**
     * @var bool $httpOnly
     */
    protected $httponly = false;

    /**
     * @var string|null $same_site
     */
    protected $same_site = null;

    /**
     * @var string $extra_string
     */
    protected $extra_string = '';

    /**
     * @var string|null
     */
    protected $useragent = null;

    /**
     * SetCookie constructor.
     * @param string $name
     * @param ICrypt|null $crypt
     * @throws CookieException
     */
    public function __construct(string $name, ICrypt $crypt = null)
    {
        $this->crypt = $crypt;

        if (!$this->isValidName($name)) {
            if ('' === \trim($name)) {
                throw new CookieException("Cookie's name is invalid! Please enter a valid cookie name.");
            }
        }

        // reset all parameters
        $this->setName($name)
            ->setValue('')
            ->setExpiration(0)
            ->setPath('/')
            ->setDomain(null)
            ->setSecure(false)
            ->setHttponly(false)
            ->setSameSite(null)
            ->setExtra('');
    }

    /**
     * {@inheritdoc}
     * @throws CookieException
     */
    public function save(bool $decode = true): bool
    {
        // this is not useless, it'll return value if not encrypted here or
        // encrypted/not-encrypted value if encrypted here
        // here means in this library
        $_COOKIE[$this->getName()] = $this->getValueAccordingToEncryption();
        return $this->setCookieToHeader($this->toString($decode, false));
    }

    /**
     * {@inheritdoc}
     */
    protected function setName(string $name): ISetCookie
    {
        if ($this->isValidName($name)) {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(?string $value, bool $encrypt = true): ISetCookie
    {
        $value = \is_null($value) ? "" : $value;
        $value = $this->prepareSetCookieValue($value, $encrypt);
        $this->value = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        $value = CookieUtil::prepareGetCookieValue($this->crypt, $this->value);
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiration($expire): ISetCookie
    {
        if (\is_string($expire)) {
            $expire = \strtotime($expire);
        } else if (\is_int($expire) && !$this->isValidTimestamp($expire)) {
            $expire = 0;
        }
        if (!\is_string($expire) && !\is_int($expire)) {
            $expire = 0;
        }

        $this->expire = 0 < (int)$expire ? (int)$expire : 0;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiration(): int
    {
        return (int)$this->expire;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath(?string $path): ISetCookie
    {
        $this->path = $path;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(?string $domain): ISetCookie
    {
        $this->domain = $this->normalizeDomain($domain);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecure(?bool $answer): ISetCookie
    {
        $this->secure = $answer;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure(): ?bool
    {
        return $this->secure;
    }

    /**
     * {@inheritdoc}
     */
    public function setHttpOnly(?bool $answer): ISetCookie
    {
        $this->httponly = $answer;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isHttpOnly(): ?bool
    {
        return (bool)$this->httponly;
    }

    /**
     * {@inheritdoc}
     */
    public function setSameSite(?string $same_site): ISetCookie
    {
        if (\is_null($same_site)) {
            $this->same_site = null;
        } elseif (\in_array($same_site, [ICookie::SAME_SITE_NONE, ICookie::SAME_SITE_LAX, ICookie::SAME_SITE_STRICT])) {
            $this->same_site = $same_site;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSameSite(): ?string
    {
        return $this->same_site;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtra(string $extra): ISetCookie
    {
        $this->extra_string = $extra;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtra(): string
    {
        return $this->extra_string;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseragent(?string $useragent = null): ISetCookie
    {
        $this->useragent = \trim((string)$useragent);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUseragent(): ?string
    {
        return $this->useragent;
    }

    /**
     * {@inheritdoc}
     * @see https://github.com/delight-im/PHP-Cookie/blob/a87055f755514f5e3285dbaf02bda2f2f6294f69/src/Cookie.php
     * @param bool $encrypt
     * @param string|null $useragent
     * @return string
     * @throws CookieException
     */
    public function toString(bool $decode = true, bool $decrypt = false): string
    {
        $expireTime = $this->getExpiration();

        $maxAgeStr = 0 === $expireTime ? 0 : ($expireTime - \time());
        $maxAgeStr = ((\PHP_VERSION_ID >= 70019 && \PHP_VERSION_ID < 70100) || \PHP_VERSION_ID >= 70105)
            ? ($maxAgeStr < 0
                ? '0'
                : (string)$maxAgeStr)
            : (string)$maxAgeStr;
        $expireTimeStr = \gmdate('D, d-M-Y H:i:s T', $expireTime);

        $value = $decrypt ? $this->getValue() : $this->getValueAccordingToEncryption();
        $value = $decode ? \urldecode($value) : $value;
        $headerStr = ISetCookie::COOKIE_HEADER . $this->getName() . '=' . $value;

        if ($expireTime > 0) {
            $headerStr .= '; expires=' . $expireTimeStr;
        }
        if ($expireTime > 0) {
            $headerStr .= '; Max-Age=' . $maxAgeStr;
        }

        $path = $this->getPath();
        if (!empty($path)) {
            $headerStr .= '; path=' . $path;
        }

        $domain = $this->getDomain();
        if (!empty($domain)) {
            $headerStr .= '; domain=' . $domain;
        }

        if ($this->isSecure()) {
            $headerStr .= '; secure';
        }
        if ($this->isHttpOnly()) {
            $headerStr .= '; httponly';
        }

        // check for SameSite support
        $useragent = (string)$this->useragent;
        if ('' === $useragent) {
            $useragent = SameSiteUtil::getUserAgent();

        }
        if (!SameSiteUtil::shouldSendSameSiteNone($useragent)) {
            $this->setSameSite(null);
        }

        $sameSite = $this->getSameSite();
        if (!\is_null($sameSite)) {
            if ($sameSite === ICookie::SAME_SITE_NONE) {
                if (!$this->isSecure()) {
                    $headerStr .= '; secure';
                }
                $headerStr .= '; SameSite=None';
            } elseif ($sameSite === ICookie::SAME_SITE_LAX) {
                $headerStr .= '; SameSite=Lax';
            } elseif ($sameSite === ICookie::SAME_SITE_STRICT) {
                $headerStr .= '; SameSite=Strict';
            }
        }

        $extra = \ltrim(\trim((string)$this->getExtra()), ';');
        $headerStr .= !empty($extra) ? '; ' . $extra : '';

        return $headerStr;
    }

    /**
     * {@inheritdoc}
     * @throws CookieException
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param string|null $name
     * @return bool
     */
    protected function isValidName(?string $name)
    {
        return !empty($name) && !(bool)\preg_match('/[=,; \\t\\r\\n\\013\\014]/', $name);
    }

    /**
     * @param $timestamp
     * @return bool
     */
    protected function isValidTimestamp($timestamp): bool
    {
        return ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }

    /**
     * @param string $domain
     * @return string
     */
    protected function normalizeDomain(?string $domain): string
    {
        if (
            empty($domain) ||
            \filter_var($domain, FILTER_VALIDATE_IP) !== false ||
            (\strpos($domain, '.') === false || \strrpos($domain, '.') === 0)
        ) {
            return '';
        }
        $domain = $domain[0] !== '.' ? '.' . $domain : $domain;
        return $domain;
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

    /**
     * Prepare cookie value to store (check if encryption need)
     *
     * @param $value
     * @param $encrypt
     * @return mixed
     */
    protected function prepareSetCookieValue($value, bool $encrypt)
    {
        if (\is_string($value)) {
            $value = \htmlspecialchars($value);
        }
        if ($encrypt && !\is_null($this->crypt)) {
            $value = $this->crypt->encrypt($value);
            $value = $this->crypt->hasError() ? "" : $value;
        }
        return \json_encode([
            '_simplicity__data' => $value,
            '_simplicity__is_encrypted' => $encrypt,
        ]);
    }

    /**
     * @return mixed
     */
    private function getValueAccordingToEncryption()
    {
        return !CookieUtil::isCookieEncryptedHere($this->value)
            ? CookieUtil::prepareGetCookieValue($this->crypt, $this->value)
            : $this->value;
    }
}