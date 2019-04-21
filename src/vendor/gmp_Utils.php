<?php
/***********************************************************************
 * Copyright (C) 2012 Matyas Danter
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *************************************************************************/

/**
 * The gmp extension in PHP does not implement certain necessary operations
 * for elliptic curve encryption
 * This class implements all neccessary static methods
 *
 */
class gmp_Utils
{
    public static function gmp_mod2($n, $d)
    {
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $res = gmp_div_r($n, $d);
            if (gmp_cmp(0, $res) > 0) {
                $res = gmp_add($d, $res);
            }
            return gmp_strval($res);
        } else {
            throw new Exception("PLEASE INSTALL GMP");
        }
    }

    public static function gmp_hexdec($hex)
    {
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $dec = gmp_strval(gmp_init($hex), 10);

            return $dec;
        } else {
            throw new Exception("PLEASE INSTALL GMP");
        }
    }

    public static function gmp_dechex($dec)
    {
        if (extension_loaded('gmp') && USE_EXT == 'GMP') {
            $hex = gmp_strval(gmp_init($dec), 16);

            return $hex;
        } else {
            throw new Exception("PLEASE INSTALL GMP");
        }
    }

    public static function gmp_dec2base($dec, $base, $digits = FALSE)
    {
        if (extension_loaded('gmp')) {
            if ($base < 2 or $base > 256)
                die("Invalid Base: " . $base);
            $value = "";
            if (!$digits)
                $digits = self::digits($base);
            $dec = gmp_init($dec);
            $base = gmp_init($base);
            while (gmp_cmp($dec, gmp_sub($base, '1')) > 0) {
                $rest = gmp_mod($dec, $base);
                $dec = gmp_div($dec, $base);
                $value = $digits[gmp_intval($rest)] . $value;
            }
            $value = $digits[gmp_intval($dec)] . $value;
            return (string)$value;
        } else {
            throw new \ErrorException("Please install GMP");
        }
    }

    public static function gmp_base2dec($value, $base, $digits = FALSE)
    {
        if (extension_loaded('gmp')) {
            if ($base < 2 or $base > 256)
                die("Invalid Base: " . $base);
            if ($base < 37)
                $value = strtolower($value);
            if (!$digits)
                $digits = self::digits($base);
            $size = strlen($value);
            $dec = "0";
            for ($loop = 0; $loop < $size; $loop++) {
                $element = strpos($digits, $value[$loop]);
                $power = gmp_pow(gmp_init($base), $size - $loop - 1);
                $dec = gmp_add($dec, gmp_mul($element, $power));
            }
            return gmp_strval($dec);
        } else {
            throw new \ErrorException("Please install GMP");
        }
    }

    public static function digits($base)
    {
        if ($base > 64) {
            $digits = "";
            for ($loop = 0; $loop < 256; $loop++) {
                $digits .= chr($loop);
            }
        } else {
            $digits = "0123456789abcdefghijklmnopqrstuvwxyz";
            $digits .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
        }
        $digits = substr($digits, 0, $base);
        return (string)$digits;
    }
}
