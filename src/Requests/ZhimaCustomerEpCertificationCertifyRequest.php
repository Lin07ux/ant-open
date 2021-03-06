<?php
/**
 * 芝麻信用企业认证 URL 生成接口
 *
 * Author: Lin07ux
 * Created_at: 2020-03-13 16:34:33
 */

namespace AntOpen\Requests;

class ZhimaCustomerEpCertificationCertifyRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'zhima.customer.ep.certification.certify';

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