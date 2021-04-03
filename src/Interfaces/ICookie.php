<?php

namespace Sim\Cookie\Interfaces;

interface ICookie
{
    const SAME_SITE_NONE = 'None';
    const SAME_SITE_LAX = 'Lax';
    const SAME_SITE_STRICT = 'Strict';

    /**
     * Parse a cookie string and save cookie data
     *
     * @param string $cookie_string
     * @param bool $decode
     * @param bool $encrypt
     * @return ISetCookie|null
     */
    public function parse(string $cookie_string, bool $decode = false, bool $encrypt = true): ?ISetCookie;

    /**
     * Set and save cookie data
     *
     * @param string $name
     * @return ISetCookie
     */
    public function set(string $name): ISetCookie;

    /**
     * Get a/all cookie/cookies
     *
     * Note:
     *   To get all cookies, do not send any parameter to method
     *
     * @param string|null $name
     * @param null $prefer
     * @return mixed
     */
    public function get(?string $name = null, $prefer = null);

    /**
     * Note:
     *   To get all cookies as string, send null as $name
     *
     * @param string|null $name
     * @param bool $decrypt
     * @return string
     */
    public function getAsString(?string $name = null, bool $decrypt = false): string;

    /**
     * Unset and remove a cookie data
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

    /**
     * Prepare cookie value to retrieve (check if decryption need)
     *
     * @param string $str
     * @return mixed
     */
    public function getCookieValueFromString(string $str);
}