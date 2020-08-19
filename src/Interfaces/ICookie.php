<?php

namespace Sim\Cookie\Interfaces;

interface ICookie
{
    /**
     * Set a cookie data
     *
     * @param ISetCookie $cookie
     * @param bool $encrypt
     * @return ICookie
     */
    public function set(ISetCookie $cookie, bool $encrypt = true): ICookie;

    /**
     * Get a/all cookie/cookies
     * Note: To get all cookies, do not send any parameter to function
     *
     * @param string|null $name
     * @param null $prefer
     * @return mixed
     */
    public function get(?string $name = null, $prefer = null);

    /**
     * @param string|null $name
     * @param bool $decrypt
     * @return string
     */
    public function getAsString(?string $name = null, bool $decrypt = false): string;

    /**
     * Unset a cookie data
     *
     * @param string $name
     * @return ICookie
     */
    public function remove(string $name): ICookie;

    /**
     * Check that a cookie is set or not
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}