<?php

namespace Sim\Cookie\Interfaces;

interface ICookie
{
    /**
     * Parse a cookie string and save cookie data
     *
     * @param string $cookie_string
     * @param bool $decode
     * @return ISetCookie|null
     */
    public function parse(string $cookie_string, bool $decode = false): ?ISetCookie;

    /**
     * Set and save cookie data
     *
     * @param ISetCookie $cookie
     * @param bool $encrypt
     * @param string|null $useragent
     * @return ICookie
     */
    public function set(ISetCookie $cookie, bool $encrypt = true, ?string $useragent = null): ICookie;

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
     * @param ISetCookie $cookie
     * @param bool $decode
     * @param bool $encrypt
     * @param string|null $useragent
     * @return string
     */
    public function toString(ISetCookie $cookie, bool $decode = false, bool $encrypt = true, ?string $useragent = null): string;

    /**
     * Prepare cookie value to retrieve (check if decryption need)
     *
     * @param $arrPrev
     * @return mixed
     */
    public function prepareGetCookieValue($arrPrev);
}