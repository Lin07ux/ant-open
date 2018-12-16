<?php
/**
 * 蚂蚁金服开放平台基本请求接口类
 *
 * Author: Lin07ux
 * Created_at: 2018-12-16 00:19:02
 */

namespace AntOpen\Request;

abstract class AbstractRequest
{
    /**
     * @var array 接口请求参数
     */
    protected $apiParams = [];

    /**
     * @var string 接口版本
     */
    protected $apiVersion = "1.0";

    /**
     * @var bool 是否需要加密
     */
    protected $needEncrypt = false;

    /**
     * @var string 查询结果通知 url
     */
    protected $notifyUrl;

    /**
     * @var string 产品码
     */
    protected $productCode;

    /**
     * @var string 查询结束后跳转的 url
     */
    protected $returnUrl;

    /**
     * @var string 终端信息
     */
    protected $terminalInfo;

    /**
     * @var string 终端类别
     */
    protected $terminalType;

    /**
     * 获取 API 签名
     *
     * @return string
     */
    abstract public function getApiMethodName ();

    /**
     * 获取接口请求参数
     *
     * @return array
     */
    public function getApiParams ()
    {
        return $this->apiParams;
    }

    /**
     * 设置 API 版本
     *
     * @param  string $apiVersion
     * @return $this
     */
    public function setApiVersion ($apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * 获取 API 版本
     *
     * @return string
     */
    public function getApiVersion ()
    {
        return $this->apiVersion;
    }

    /**
     * 设置认证内容
     *
     * @param  array|string  $content
     *
     * @return $this
     */
    public function setBizContent (array $content)
    {
        $this->apiParams["biz_content"] = json_encode($content, JSON_UNESCAPED_UNICODE);

        return $this;
    }

    /**
     * 获取认证内容
     *
     * @return string
     */
    public function getBizContent ()
    {
        return $this->apiParams["biz_content"];
    }

    /**
     * 设置是否需要加密
     *
     * @param  bool  $needEncrypt
     * @return $this
     */
    public function setNeedEncrypt ($needEncrypt)
    {
        $this->needEncrypt = $needEncrypt;

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
     * 设置通知 url
     *
     * @param  string  $notifyUrl
     * @return $this
     */
    public function setNotifyUrl ($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;

        return $this;
    }

    /**
     * 获取通知 url
     *
     * @return string
     */
    public function getNotifyUrl ()
    {
        return $this->notifyUrl;
    }

    /**
     * 设置产品码
     *
     * @param  string  $productCode
     *
     * @return $this
     */
    public function setProductCode ($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * 获取产品码
     *
     * @return string
     */
    public function getProductCode ()
    {
        return $this->productCode;
    }

    /**
     * 设置认证后跳转 url
     *
     * @param  string  $returnUrl
     * @return $this
     */
    public function setReturnUrl ($returnUrl)
    {
        $this->returnUrl = $returnUrl;

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
     * 设置终端信息
     *
     * @param  string  $terminalInfo
     * @return $this
     */
    public function setTerminalInfo ($terminalInfo)
    {
        $this->terminalInfo = $terminalInfo;

        return $this;
    }

    /**
     * 获取终端信息
     *
     * @return string
     */
    public function getTerminalInfo ()
    {
        return $this->terminalInfo;
    }

    /**
     * 设置终端类别
     *
     * @param  string  $terminalType
     * @return $this
     */
    public function setTerminalType ($terminalType)
    {
        $this->terminalType = $terminalType;

        return $this;
    }

    /**
     * 获取终端类别
     *
     * @return string
     */
    public function getTerminalType ()
    {
        return $this->terminalType;
    }
}