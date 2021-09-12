<?php

namespace Sim\Cookie\Utils;

use Sim\Cookie\Exceptions\CookieException;

/**
 * Class SameSiteUtil
 * @package Sim\Cookie\Utils
 * @see https://www.chromium.org/updates/same-site/incompatible-clients
 * @see https://github.com/fullfatthings/samesiteexceptions/blob/master/src/SameSiteException.php
 */
class SameSiteUtil
{
    const IS_IOS_VERSION_REGEX = '/\(iP.+; CPU .*OS (\d+)[_\d]*.*\) AppleWebKit\//';
    const IS_MACOSX_VERSION_REGEX = '/\(Macintosh;.*Mac OS X (\d+)_(\d+)[_\d]*.*\) AppleWebKit\//';
    const IS_SAFARI_REGEX = '/Version\/.* Safari\//';
    const IS_MAC_EMBEDDED_BROWSER_REGEX = '/^Mozilla\/[\.\d]+ \(Macintosh;.*Mac OS X [_\d]+\) ' . 'AppleWebKit\/[\.\d]+ \(KHTML, like Gecko\)$/';
    const IS_CHROMIUM_BASED_REGEX = '/Chrom(e|ium)/';
    const IS_CHROMIUM_VERSION_AT_LEAST_REGEX = '/Chrom[^ \/]+\/(\d+)[\.\d]* /';
    const IS_UC_BROWSER_REGEX = '/UCBrowser\//';
    const IS_UC_BROWSER_VERSION_AT_LEAST_REGEX = '/UCBrowser\/(\d+)\.(\d+)\.(\d+)[\.\d]* /';

    /**
     * @param bool $ignore_cli_errors
     * @return string
     * @throws CookieException
     */
    public static function getUserAgent(bool $ignore_cli_errors = true): string
    {
        $useragent = GeneralUtil::getUserAgentFromHeader();
        if (empty($useragent)) {
            if (!GeneralUtil::isCli() || (GeneralUtil::isCli() && !$ignore_cli_errors)) {
                throw new CookieException("No User agent founded!");
            }
        }
        return $useragent;
    }

    /**
     * @param string $useragent
     * @return bool
     */
    public static function shouldSendSameSiteNone(string $useragent): bool
    {
        return !self::isSameSiteNoneIncompatible($useragent);
    }

    /**
     * @param string $useragent
     * @return bool
     */
    public static function isSameSiteNoneIncompatible(string $useragent): bool
    {
        return self::hasWebKitSameSiteBug($useragent) ||
            self::dropsUnrecognizedSameSiteCookies($useragent);
    }

    /**
     * @param string $useragent
     * @return bool
     */
    public static function hasWebKitSameSiteBug(string $useragent): bool
    {
        return self::isIosVersion(12, $useragent) ||
            (self::isMacosxVersion(10, 14, $useragent) &&
                (self::isSafari($useragent) || self::isMacEmbeddedBrowser($useragent)));
    }

    /**
     * @param string $useragent
     * @return bool
     */
    public static function dropsUnrecognizedSameSiteCookies(string $useragent): bool
    {
        if (self::isUcBrowser($useragent)) {
            return !self::isUcBrowserVersionAtLeast(12, 13, 2, $useragent);
        }
        return self::isChromiumBased($useragent) &&
            self::isChromiumVersionAtLeast(51, $useragent) &&
            !self::isChromiumVersionAtLeast(67, $useragent);
    }

    /**
     * @param int $major
     * @param string $useragent
     * @return bool
     */
    private static function isIosVersion(int $major, string $useragent): bool
    {
        $matches = [];
        if (\preg_match(self::IS_IOS_VERSION_REGEX, $useragent, $matches)) {
            return $matches[1] == $major;
        }
        return false;
    }

    /**
     * @param int $major
     * @param int $minor
     * @param string $useragent
     * @return bool
     */
    private static function isMacosxVersion(int $major, int $minor, string $useragent): bool
    {
        $matches = [];
        if (\preg_match(self::IS_MACOSX_VERSION_REGEX, $useragent, $matches)) {
            return $matches[1] == $major && $matches[2] == $minor;
        }
        return false;
    }

    /**
     * @param string $useragent
     * @return bool
     */
    private static function isSafari(string $useragent): bool
    {
        return (bool)\preg_match(self::IS_SAFARI_REGEX, $useragent) && !self::isChromiumBased($useragent);
    }

    /**
     * @param string $useragent
     * @return bool
     */
    private static function isMacEmbeddedBrowser(string $useragent): bool
    {
        return (bool)\preg_match(self::IS_MAC_EMBEDDED_BROWSER_REGEX, $useragent);
    }

    /**
     * @param string $useragent
     * @return bool
     */
    private static function isChromiumBased(string $useragent): bool
    {
        return (bool)preg_match(self::IS_CHROMIUM_BASED_REGEX, $useragent);
    }

    /**
     * @param int $major
     * @param string $useragent
     * @return bool
     */
    private static function isChromiumVersionAtLeast(int $major, string $useragent): bool
    {
        $matches = [];
        if (\preg_match(self::IS_CHROMIUM_VERSION_AT_LEAST_REGEX, $useragent, $matches)) {
            return (int)$matches[1] >= $major;
        }
        return false;
    }

    /**
     * @param string $useragent
     * @return bool
     */
    private static function isUcBrowser(string $useragent): bool
    {
        return \preg_match(self::IS_UC_BROWSER_REGEX, $useragent);
    }

    /**
     * @param int $major
     * @param int $minor
     * @param int $build
     * @param string $useragent
     * @return bool
     */
    private static function isUcBrowserVersionAtLeast(int $major, int $minor, int $build, string $useragent): bool
    {
        $matches = [];
        if (\preg_match(self::IS_UC_BROWSER_VERSION_AT_LEAST_REGEX, $useragent, $matches)) {
            $major_version = (int)$matches[1];
            $minor_version = (int)$matches[2];
            $build_version = (int)$matches[3];

            if ($major_version != $major) return $major_version > $major;
            if ($minor_version != $minor) return $minor_version > $minor;
            return $build_version >= $build;
        }
        return false;
    }
}
