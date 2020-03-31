<?php
/**
 * 蚂蚁金服开放平台基本请求接口类
 *
 * Author: Lin07ux
 * Created_at: 2018-12-16 00:19:02
 */

namespace AntOpen\Requests;

class Request
{
    /**
     * @var string 接口签名
     */
    protected $method;

    /**
     * @var array 接口请求参数
     */
    protected $params;

    /**
     * @var string 接口版本
     */
    protected $version = "1.0";

    /**
     * @var bool 是否需要加密
     */
    protected $needEncrypt;

    /**
     * @var string 查询结束后跳转的 url
     */
    protected $returnUrl;

    /**
     * @var string 支付宝主动通知的 url
     */
    protected $notifyUrl;

    /**
     * Request constructor.
     *
     * @param  array   $params      请求参数
     * @param  bool    $needEncrypt 是否需要加密数据
     * @param  string  $returnUrl   返回地址 url
     */
    public function __construct (array $params = [], $needEncrypt = false, $returnUrl = null, $notifyUrl = null)
    {
        $this->params = $params ?: [];
        $this->setNeedEncrypt($needEncrypt)->setReturnUrl($returnUrl)->setReturnUrl($notifyUrl);
    }

    /**
     * 获取 API 签名
     *
     * @return string
     */
    public function getMethod ()
    {
        // 使用类名格式化得到接口签名，如：对于 ZhimaCustomerCertificationInitializeRequest 类
        // 解析得到的签名字符串为 zhima.customer.certification.initialize
        if (empty($this->method)) {
            $method = explode('\\', static::class);
            $method = str_replace('Request', '', end($method));
            $method = preg_replace('/([A-Z])/', '.$1', $method);

            $this->method = strtolower(trim($method, '.')) ?: 'non.existent';
        }

        return $this->method;
    }

    /**
     * 获取接口请求参数
     *
     * @param  bool  $serialize 是否需要 json 序列化
     *
     * @return string|array
     */
    public function getParams ($serialize = true)
    {
        return $serialize ? json_encode($this->params, JSON_UNESCAPED_UNICODE) : $this->params;
    }

    /**
     * 获取 API 版本
     *
     * @return string
     */
    public function getVersion ()
    {
        return $this->version;
    }

    /**
     * 设置是否需要加密
     *
     * @param  bool  $needEncrypt
     * @return $this
     */
    public function setNeedEncrypt ($needEncrypt)
    {
        $this->needEncrypt = (bool)$needEncrypt;

        return $this;
    }

    /**
     * 获取是否需要加密
     *
     * @return bool
     */
    public function getNeedEncrypt ()
    {
        return $this->needEncrypt;
    }

    /**
     * 设置认证后跳转 url
     *
     * @param  string  $returnUrl
     * @return $this
     */
    public function setReturnUrl ($returnUrl)
    {
        $this->returnUrl = (string)$returnUrl ?: null;

        return $this;
    }

    /**
     * 获取认证后跳转 url
     *
     * @return string
     */
    public function getReturnUrl ()
    {
        return $this->returnUrl;
    }

    /**
     * 设置支付宝主动通知 url
     *
     * @param  string  $notifyUrl
     * @return $this
     */
    public function setNotifyUrl ($notifyUrl)
    {
        $this->notifyUrl = (string)$notifyUrl ?: null;

        return $this;
    }

    /**
     * 获取支付宝主动通知 url
     *
     * @return string
     */
    public function getNotifyUrl ()
    {
        return $this->notifyUrl;
    }
}