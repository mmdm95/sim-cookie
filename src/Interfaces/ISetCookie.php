<?php

namespace Sim\Cookie\Interfaces;

interface ISetCookie
{
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
}