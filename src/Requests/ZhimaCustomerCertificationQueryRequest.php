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
     * 设置认证内容
     *
     * @param  array|string  $content
     *
     * @return $this
     */
    public function setBizContent (array $content)
    {
        if (empty($content['biz_no'])) {
            throw new \InvalidArgumentException('The biz_no of Biz Content can not be empty');
        }

        $this->params["biz_content"] = json_encode($content, JSON_UNESCAPED_UNICODE);

        return $this;
	}
}
