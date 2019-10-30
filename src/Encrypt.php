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
     * @param  string  $string 明文字符串
     * @param  string  $secret 加密密钥
     *
     * @return string  返回 Base64 编码后的加密结果
     */
    public static function encrypt ($string, $secret)
    {
        $iv = str_repeat("\0", openssl_cipher_iv_length("AES-256-CBC"));

        return openssl_encrypt($string, 'AES-256-CBC', $secret, 0, $iv);
    }

    /**
     * 解密
     *
     * @param  string  $string 待解密字符串
     * @param  string  $secret 解密密钥
     *
     * @return string
     */
    public static function decrypt ($string, $secret)
    {
        $iv = str_repeat("\0", openssl_cipher_iv_length("AES-256-CBC"));

        return openssl_decrypt(base64_decode($string), 'AES-256-CBC', $secret, OPENSSL_RAW_DATA, $iv);
    }
}