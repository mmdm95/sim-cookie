<?php

namespace Sim\Cookie\Utils;

class GeneralUtil
{
    /**
     * @return string|null
     */
    public static function getUserAgentFromHeader(): string
    {
        return (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT']))
            ? $_SERVER['HTTP_USER_AGENT']
            : '';
    }

    /**
     * @return bool
     */
    public static function isCli(): bool
    {
        return \php_sapi_name() === 'cli';
    }
}
