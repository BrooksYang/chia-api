<?php

namespace BrooksYang\ChiaApi;

use BrooksYang\ChiaApi\Support\BigInteger;
use BrooksYang\ChiaUtils\ChiaUtils;
use BrooksYang\ChiaUtils\Exception\ChiaUtilsException;

trait ChiaAwareTrait
{
    /**
     * Convert from Hex
     *
     * @param $string
     * @return mixed|string
     * @throws ChiaUtilsException
     */
    public function fromHex($string)
    {
        if (strlen($string) == 66 && mb_substr($string, 0, 2) === '0x') {
            return $this->puzzleHash2Address($string);
        }

        return $this->hexString2Utf8($string);
    }

    /**
     * Convert to Hex
     *
     * @param $str
     * @return mixed|string
     * @throws ChiaUtilsException
     */
    public function toHex($str)
    {
        if (mb_strlen($str) == 62 && mb_substr($str, 0, 3) === 'xch') {
            return $this->address2PuzzleHash($str);
        }

        return $str;
    }

    /**
     * Convert Chia Address to PuzzleHash
     *
     * @param $sHexAddress
     * @return mixed|string
     * @throws ChiaUtilsException
     */
    public function address2PuzzleHash($sHexAddress)
    {
        if (strlen($sHexAddress) == 66 && mb_strpos($sHexAddress, '0x') == 0) {
            return $sHexAddress;
        }

        return (new ChiaUtils())->addressToPuzzleHash($sHexAddress);
    }

    /**
     * Convert PuzzleHash to Address
     *
     * @param $sHexString
     * @return mixed|string
     * @throws ChiaUtilsException
     */
    public function puzzleHash2Address($sHexString)
    {
        if (!ctype_xdigit($sHexString)) {
            return $sHexString;
        }

        if (strlen($sHexString) < 2 || (strlen($sHexString) & 1) != 0) {
            return '';
        }

        return (new ChiaUtils())->puzzleHashToAddress($sHexString);
    }

    /**
     * Convert string to hex
     *
     * @param $sUtf8
     * @return string
     */
    public function stringUtf8toHex($sUtf8)
    {
        return bin2hex($sUtf8);
    }

    /**
     * Convert hex to string
     *
     * @param $sHexString
     * @return string
     */
    public function hexString2Utf8($sHexString)
    {
        return hex2bin($sHexString);
    }

    /**
     * Convert to great value
     *
     * @param $str
     * @return BigInteger
     */
    public function toBigNumber($str)
    {
        return new BigInteger($str);
    }

    /**
     * Convert trx to float
     *
     * @param $amount
     * @return float
     */
    public function fromChia($amount): float
    {
        return (float)bcdiv((string)$amount, (string)1e12, 12);
    }

    /**
     * Convert float to trx format
     *
     * @param $double
     * @return int
     */
    public function toChia($double): int
    {
        return (int)bcmul((string)$double, (string)1e12, 0);
    }
}
