<?php
/**
 * 蚂蚁金服开放平台 API 接口请求客户端类
 *
 * Author: Lin07ux
 * Created_at: 2018-12-15 23:59:11
 */

namespace AntOpen;

use AntOpen\Exception\InvalidRequestContentException;
use AntOpen\Request\AbstractRequest;

class AlipayClient
{
    /**
     * API 版本号
     */
    const VERSION = '1.0';

    /**
     * 加密类型
     */
    const ENCRYPT_TYPE = 'AES';

    /**
     * @var string 网关
     */
    protected $gateway = 'https://openapi.alipay.com/gateway.do';

    /**
     * @var string 签名类型
     */
    protected $signType = "RSA2";

    /**
     * @var string 应用 APP ID
     */
    protected $appId;

    /**
     * @var string 支付宝公钥字符串
     */
    protected $alipayRsaPublicKey;

    /**
     * @var string 商户私钥字符串
     */
    protected $customerRsaPrivateKey;

    /**
     * @var string 加密密钥
     */
    protected $encryptKey;

    /**
     * AntClient constructor.
     *
     * @param  string       $appId 应用 ID
     * @param  string|null  $customerRsaPrivateKey 商户私钥
     * @param  string|null  $alipayRsaPublicKey 支付宝公钥
     * @param  string|null  $encryptKey 加密密钥
     */
    public function __construct ($appId, $customerRsaPrivateKey = null, $alipayRsaPublicKey = null, $encryptKey = null)
    {
        if ($this->isEmpty($appId) || !$appId) {
            throw new \InvalidArgumentException('App id is invalid!');
        }

        $this->appId = $appId;

        if ($customerRsaPrivateKey) {
            $this->setCustomerRsaPrivateKey($customerRsaPrivateKey);
        }

        if ($alipayRsaPublicKey) {
            $this->setAlipayRsaPublicKey($alipayRsaPublicKey);
        }

        if ($encryptKey) {
            $this->setEncryptKey($encryptKey);
        }
    }

    /**
     * 获取服务器网关
     *
     * @return string
     */
    public function getGateway ()
    {
        return $this->gateway;
    }

    /**
     * 设置服务器网关
     *
     * @param  string  $gateway
     * @return $this
     */
    public function setGateway ($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * 获取 app id
     *
     * @return string
     */
    public function getAppId ()
    {
        return $this->appId;
    }

    /**
     * 设置 app id
     *
     * @param  string  $appId
     * @return $this
     */
    public function setAppId ($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * 获取支付宝 RSA 公钥
     *
     * @return string
     */
    public function getAlipayRsaPublicKey ()
    {
        return $this->alipayRsaPublicKey;
    }

    /**
     * 设置支付宝 RSA 公钥
     *
     * @param  string  $alipayRsaPublicKey
     * @param  bool    $isFile
     * @return $this
     */
    public function setAlipayRsaPublicKey ($alipayRsaPublicKey, $isFile = false)
    {
        $key = !empty($alipayRsaPublicKey) && $isFile ? file_get_contents($alipayRsaPublicKey) : $alipayRsaPublicKey;

        if ($this->isEmpty($key)) {
            throw new \InvalidArgumentException('The Alipay RSA public key or file is invalid.');
        }

        $this->alipayRsaPublicKey = $key;

        return $this;
    }

    /**
     * 获取商户 RSA 私钥
     *
     * @return string
     */
    public function getCustomerRsaPrivateKey ()
    {
        return $this->customerRsaPrivateKey;
    }

    /**
     * 设置商户 RSA 私钥
     *
     * @param  string  $customerRsaPrivateKey
     * @param  bool    $isFile
     * @return $this
     */
    public function setCustomerRsaPrivateKey ($customerRsaPrivateKey, $isFile = false)
    {
        $key = !empty($customerRsaPrivateKey) && $isFile ? file_get_contents($customerRsaPrivateKey) : $customerRsaPrivateKey;

        if ($this->isEmpty($key)) {
            throw new \InvalidArgumentException('The customer RSA public key or file is invalid.');
        }

        $this->customerRsaPrivateKey = $key;

        return $this;
    }

    /**
     * 获取加密密钥
     *
     * @return string
     */
    public function getEncryptKey ()
    {
        return $this->encryptKey;
    }

    /**
     * 设置加密密钥
     *
     * @param  string  $encryptKey
     * @return $this
     */
    public function setEncryptKey ($encryptKey)
    {
        if ($this->isEmpty($encryptKey)) {
            throw new \InvalidArgumentException('The AES encrypt key is invalid.');
        }

        $this->encryptKey = $encryptKey;

        return $this;
    }

    /**
     * 获取签名类别
     *
     * @return string
     */
    public function getSignType ()
    {
        return $this->signType;
    }

    /**
     * 设置签名类别
     *
     * @param  string  $type
     * @return $this
     */
    public function setSignType ($type)
    {
        $type = strtoupper($type);

        if (! in_array($type, ['RSA', 'RSA2'])) {
            throw new \InvalidArgumentException('The sign type can only be RSA or RSA2');
        }

        $this->signType = $type;

        return $this;
    }

    /**
     * 校验值是否为空
     *
     * @param  mixed  $value
     * @return bool
     **/
    private function isEmpty($value)
    {
        return $value === false || $value === null || trim($value) === "";
    }
}