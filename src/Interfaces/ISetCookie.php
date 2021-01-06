<?php

namespace Sim\Cookie\Interfaces;

interface ISetCookie
{
    const COOKIE_HEADER = 'Set-Cookie: ';
    const SAME_SITE_NONE = 'None';
    const SAME_SITE_LAX = 'Lax';
    const SAME_SITE_STRICT = 'Strict';

    /**
     * @param string|null $name
     * @return ISetCookie
     */
    public function setName(?string $name): ISetCookie;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $value
     * @return ISetCookie
     */
    public function setValue(string $value): ISetCookie;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @param int|string $expire
     * @return ISetCookie
     */
    public function setExpiration($expire): ISetCookie;

    /**
     * @return int|null
     */
    public function getExpiration(): int;

    /**
     * @param string|null $path
     * @return ISetCookie
     */
    public function setPath(?string $path): ISetCookie;

    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @param string|null $domain
     * @return ISetCookie
     */
    public function setDomain(?string $domain): ISetCookie;

    /**
     * @return string|null
     */
    public function getDomain(): ?string;

    /**
     * @param bool|null $answer
     * @return ISetCookie
     */
    public function setSecure(?bool $answer): ISetCookie;

    /**
     * @return bool|null
     */
    public function isSecure(): ?bool;

    /**
     * @param bool|null $answer
     * @return ISetCookie
     */
    public function setHttpOnly(?bool $answer): ISetCookie;

    /**
     * @return bool|null
     */
    public function isHttpOnly(): ?bool;

    /**
     * @param string|null $same_site
     * @return ISetCookie
     */
    public function setSameSite(?string $same_site): ISetCookie;

    /**
     * @return string|null
     */
    public function getSameSite(): ?string;

    /**
     * Set extra string for cookie
     *
     * @param string $extra
     * @return ISetCookie
     */
    public function setExtra(string $extra): ISetCookie;

    /**
     * @return string
     */
    public function getExtra(): string;
}