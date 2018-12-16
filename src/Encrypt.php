<?php
/**
 * 加解密
 *
 * Author: Lin07ux
 * Created_at: 2018-12-16 21:43:51
 */

namespace AntOpen;

class Encrypt
{
    /**
     * 加密
     *
     * @param  string  $str
     * @param  string  $secret
     * @return string
     */
    public static function encrypt ($str, $secret)
    {
        // AES, 128 模式加密数据 CBC
        $str = self::addPKCS7Padding(trim($str));

        $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = pack('H*', str_pad('', $size * 2, '0'));
        $encrypt_str = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, base64_decode($secret), $str, MCRYPT_MODE_CBC, $iv);

        return base64_encode($encrypt_str);
    }

    /**
     * 解密
     *
     * @param  string  $str
     * @param  string  $secret
     * @return string
     */
    public static function decrypt($str, $secret)
    {
        // AES, 128 模式加密数据 CBC
        $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = pack('H*', str_pad('', $size * 2, '0'));
        $encrypt_str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, base64_decode($secret), base64_decode($str), MCRYPT_MODE_CBC, $iv);

        return self::stripPKSC7Padding($encrypt_str);
    }

    /**
     * PKCS7 填充
     *
     * @param  string  $source
     * @return string
     */
    public static function addPKCS7Padding($source)
    {
        $source = trim($source);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        $pad = $block - (strlen($source) % $block);

        if ($pad <= $block) {
            $char = chr($pad);
            $source .= str_repeat($char, $pad);
        }

        return $source;
    }

    /**
     * 移去 OKCS7 填充
     *
     * @param  string  $source
     * @return string
     */
    public static function stripPKSC7Padding($source)
    {
        $source = trim($source);
        $num = ord(substr($source, -1));

        return $num === 62 ? $source : substr($source, 0, -$num);
    }
}