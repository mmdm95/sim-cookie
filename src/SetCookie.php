<?php

namespace Sim\Cookie;

use Sim\Cookie\Interfaces\ISetCookie;

class SetCookie implements ISetCookie
{
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
    protected $path = "";

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
     * SetCookie constructor.
     * @param string|null $name
     * @param string $value
     * @param int|null $expire
     * @param string|null $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     */
    public function __construct(?string $name = null,
                                string $value = "",
                                int $expire = null,
                                string $path = null,
                                string $domain = null,
                                bool $secure = null,
                                bool $httponly = null
    )
    {
        $this->setName($name)
            ->setValue($value)
            ->setExpiration($expire)
            ->setPath($path)
            ->setDomain($domain)
            ->setSecure($secure)
            ->setHttponly($httponly);
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): ISetCookie
    {
        if (!is_null($name)) {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(string $value): ISetCookie
    {
        $this->value = is_null($value) ? "" : $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiration($expire): ISetCookie
    {
        if (is_string($expire)) {
            $expire = strtotime($expire);
        } else if (is_int($expire) && !$this->isValidTimestamp($expire)) {
            $expire = 0;
        }
        if (!is_string($expire) && !is_int($expire)) {
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
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(?string $domain): ISetCookie
    {
        $this->path = $domain;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain()
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
    public function isSecure(): bool
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
    public function isHttpOnly(): bool
    {
        return (bool)$this->httponly;
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
}