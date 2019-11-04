<?php
/**
 * 支付宝身份认证执行认证
 *
 * Author: Lin07ux
 * Created_at: 2019-10-31 15:56:31
 */

namespace AntOpen\Requests;

class AlipayUserCertifyOpenQueryRequest extends Request
{
    /**
     * 设置认证的唯一标识
     *
     * @param  string  $value 初始化接口获取到的认证标识
     *
     * @return $this
     */
    public function setCertifyIdParam ($value)
    {
        if (strlen($value) > 32) {
            throw new \InvalidArgumentException('The value of certify_id param can not exceed 32 characters');
        }

        $this->params['certify_id'] = $value;

        return $this;
    }
}