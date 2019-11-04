<?php
/**
 * 芝麻认证查询接口
 * ALIPAY API: zhima.customer.certification.query request
 * @since 1.0, 2018-08-29 14:41:48
 */

namespace AntOpen\Requests;

class ZhimaCustomerCertificationQueryRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'zhima.customer.certification.query';

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
