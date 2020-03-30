<?php
/**
 * 芝麻企业认证初始化
 *
 * Author: Lin07ux
 * Created_at: 2020-03-13 15:15:19
 */

namespace AntOpen\Requests;

use AntOpen\Identity\Enterprise;

class ZhimaCustomerEpCertificationInitializeRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'zhima.customer.ep.certification.initialize';

    /**
     * ZhimaCustomerEpCertificationInitializeRequest constructor.
     *
     * @param array $params
     * @param bool  $needEncrypt
     * @param null  $returnUrl
     */
    public function __construct (array $params = [], $needEncrypt = false, $returnUrl = null)
    {
        parent::__construct($params, $needEncrypt, $returnUrl);

        $this->params['product_code'] = 'w1010100003000001889';
        $this->params['biz_code'] = 'EP_ALIPAY_ACCOUNT';
    }

    /**
     * 设置商户请求的唯一标志
     *
     * @param  string  $value
     *
     * @return $this
     */
    public function setTransactionIdParam ($value)
    {
        if (strlen($value) > 32) {
            throw new \InvalidArgumentException('The value of transaction_id param can not exceed 32 characters');
        }

        $this->params['transaction_id'] = $value;

        return $this;
    }

    /**
     * 设置认证场景码
     *
     * @param  string  $code
     *
     * @return $this
     */
    public function setBizCodeParam ($code)
    {
        $code = strtoupper($code);
        $codes = ['FACE', 'CERT_PHOTO', 'CERT_PHOTO_FACE', 'SMART_FACE'];

        if (! in_array($code, $codes)) {
            throw new \InvalidArgumentException('The value of biz_code param can only be one of '.implode(', ', $codes));
        }

        $this->params['biz_code'] = $code;

        return $this;
    }

    /**
     * 设置认证信息
     *
     * @param  Enterprise  $enterprise 企业认证信息
     *
     * @return $this
     */
    public function setIdentityParam (Enterprise $enterprise)
    {
        $this->params['identity_param'] = $enterprise->toArray();

        return $this;
    }

    /**
     * 设置商户可选的一些设置
     *
     * @param  string|array  $key
     * @param  mixed         $value
     *
     * @return $this
     */
    public function setMerchantConfigParam ($key, $value = null)
    {
        if (is_array($key)) {
            $this->params['merchant_config'] = $key;
        } else {
            $this->params['merchant_config'][$key] = $value;
        }

        return $this;
    }

    /**
     * 扩展业务参数
     *
     * @param  string|array  $key
     * @param  mixed         $value
     *
     * @return $this
     */
    public function setExtBizParam ($key, $value = null)
    {
        if (is_array($key)) {
            $this->params['ext_biz_param'] = $key;
        } else {
            $this->params['ext_biz_param'][$key] = $value;
        }

        return $this;
    }
}