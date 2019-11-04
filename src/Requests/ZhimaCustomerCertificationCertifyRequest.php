<?php
/**
 * 芝麻认证开始认证接口
 * ALIPAY API: zhima.customer.certification.certify request
 * @since 1.0, 2018-08-29 14:42:03
 */

namespace AntOpen\Requests;

class ZhimaCustomerCertificationCertifyRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'zhima.customer.certification.certify';

    /**
     * 设置认证的唯一标识
     *
     * @param  string  $value 初始化接口获取到的认证标识
     *
     * @return $this
     */
    public function setBizNoParam ($value)
    {
        if (strlen($value) > 32) {
            throw new \InvalidArgumentException('The value of biz_no param can not exceed 32 characters');
        }

        $this->params['biz_no'] = $value;

        return $this;
    }
}
