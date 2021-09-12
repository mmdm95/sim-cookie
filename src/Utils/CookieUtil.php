<?php

namespace Sim\Cookie\Utils;

use Sim\Crypt\ICrypt;

class CookieUtil
{
    /**
     * @param ICrypt|null $crypt
     * @param $arrPrev
     * @return mixed
     */
    public static function prepareGetCookieValue(?ICrypt $crypt, $arrPrev)
    {
        $arr = \json_decode($arrPrev, true);
        if (\is_null($arr)) return $arrPrev;
        if (!isset($arr['_simplicity__data']) || !isset($arr['_simplicity__is_encrypted'])) return $arrPrev;

        $value = $arr['_simplicity__data'];
        if ($arr['_simplicity__is_encrypted'] && !\is_null($crypt)) {
            $value = $crypt->decrypt($value);
            $value = $crypt->hasError() ? null : $value;
        }
        if (\is_string($value)) {
            $value = \htmlspecialchars_decode($value);
        }
        return $value;
    }

    /**
     * @param string $cookie
     * @return bool
     */
    public static function isCookieEncryptedHere(string $cookie): bool
    {
        $arr = \json_decode($cookie, true);
        return
            \is_array($arr) &&
            isset($arr['_simplicity__data']) &&
            isset($arr['_simplicity__is_encrypted']) &&
            (bool)$arr['_simplicity__is_encrypted'];
    }
}
