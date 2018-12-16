<?php
/**
 * 芝麻认证初始化接口
 * ALIPAY API: zhima.customer.certification.initialize request
 * @since 1.0, 2018-08-29 14:41:25
 */

namespace AntOpen\Request;

/**
 * Class ZhimaCustomerCertificationInitializeRequest
 *
 * Docs: https://docs.open.alipay.com/api_8/zhima.customer.certification.initialize
 *
 * 请求成功时
 *
 * JSON 原始数据类似如下：
 *
 * "{\"zhima_customer_certification_initialize_response\":{\"code\":\"10000\",\"biz_no\":\"044eb82564529b890aeb1a1f5ed42f33\",\"msg\":\"Success\"},\"sign\":\"xEBaZLNLZ/dIlx8ITzqQEf+Uq/WQsGZoPRTi0ZMSOidGhflCw9zHwTTHppk1MWGyDmGCxMzCu722spZMoN0Txu8c6BRv1OUj6+zTAScjFIr6NIAhx7Hh7yRKL/GsYbk3NvmYDeC37UAPOn348ihGIE6WZ7VcILwruCKgtspC93Lni/wy5UEALB9nuNOULWg2n8KOWC/5I8DFEzhf4Oez3tb3OxiRUNZQCp2GtrCWDjjrdBcis1XPPWGmy/MTbGQ/Xb7ZPW1rJUchljmdm6b7zXLDYIMnUioxGmnOe24wFBA9HO6ZgTiUJYEC93aLCCj4dsUHkGFyeG+MChLCYABBcw==\"}"
 *
 * XML 原始数据类似如下：(除签名数据外，其他数据以 json 字符串的方式放在根节点中)
 *
 * "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><zhima_customer_certification_initialize_response>{\"code\":\"10000\",\"biz_no\":\"3485348f319cae7d96f671ea8fb558c2\",\"msg\":\"Success\"}<sign>MswOSvfzxmEnbnA4bFRjG+l3kQEBavvcNrFjwjUxSm2SnbVjC0DQHnL16TTvr2m/tnC5EFoxo8/krgS6ftjlivDbfjak7K1ss8wxOj40ys1gPiL3gtAJNvxIObG/MlQW8x3UhUWo2kzQE7NELd1N2KOkzhuDdckrvcYQDxYz5DiOtEBDRVval8dObG4zGJW04TXQAQ2EerGyWflFWOsozntK5WH16CvkZcctsiCZnCY+yAv+rmDHvmyN6VcieRLYkoGwW7Dta85VQXwKU/Jj7GvbiPgxtFMCf9rmkgxtsrxtz5D7o/zR5Rcp48dspnkNc+9ngRE8KvCTx50annUMog==</sign></zhima_customer_certification_initialize_response>"
 *
 * 请求失败时
 *
 * JSON 原始数据类似是如下：
 *
 * "{\"zhima_customer_certification_initialize_response\":{\"code\":\"40004\",\"msg\":\"Business Failed\",\"sub_code\":\"INVALID_PARAMETER\",\"sub_msg\":\"参数有误。参数[transaction_id]不是有效的入参\"},\"sign\":\"oZYkxXYewhwA5klzOJx/JjaChfqfzn6u1JangC9Yidq+P9Q/zlWA9wIbULaTGqnGTXK++DAJZbWPPoFth+WN11P6A+YESHPwtkKv1ci1lLD2MNvJVhuyrMZOFwHKoQn4unCPZ96tIeadZgGlxNSiIy+OncszTrdpl304xpF8NBXcYbvdJiSAOd3gpFToXK9PCDBG0puhOFspkaW8KnD4SE/5XmHGOAX28tq0ywv69RcYN6plBDJW4XcSoTOPXzR1VLkUhrltJpIYGGiCCSM/PP06v0Xqi6bqanYd09/HyzYaOKT1el/DhINx0XuuzVQeP7OBzCMdzQ/VYcYX9OvsdA==\"}"
 *
 * XML 原始数据类似如下：
 *
 * "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><zhima_customer_certification_initialize_response><code>40004</code><msg>Business Failed</msg><sub_code>INVALID_PARAMETER</sub_code><sub_msg>参数有误。参数[transaction_id]不是有效的入参</sub_msg><sign>bO47ya+mzymeh4xQl44T8vPwN3GrfHfhNCPK0FHY2zHdff9AkqjWBfKszARy6I1liqj0XZDkc7wCmjKjuAwWkBoKKyt/UR6bB6MDEUmVXJR8qMiq9c97If4TcnnygVT1POubCZ/z2SUTTyb2asmCsZ/aSBx5jAbTBt0tyyzQbv7eUC3pdQ1cy+de1ZNrbFv+r3bshXAECPVp3KC2T7D5bYIyaHBbMD3qRr5hpnfH7Lem1p4nPby0KZK/0NKznPXZeFfp9NXWLg4SWBg3WFR59MzOhoeLBzrjMxoYMn1gkyjTUEiOjZ7WvCD4UfVTQGadtJL2fXF9F46/ZXsqUO+QvQ==</sign></zhima_customer_certification_initialize_response>"
 */
class ZhimaCustomerCertificationInitializeRequest extends AbstractRequest
{
    /**
     * 获取 API 签名
     *
     * @return string
     */
	public function getApiMethodName()
	{
		return "zhima.customer.certification.initialize";
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
        if (empty($content['transaction_id'])) {
            throw new \InvalidArgumentException('The transaction_id of Biz Content can not be empty');
        }

        if (empty($content['product_code'])) {
            throw new \InvalidArgumentException('The product_code of Biz Content can not be empty');
        }

        if (empty($content['biz_code'])) {
            throw new \InvalidArgumentException('The biz_code of Biz Content can not be empty');
        }

        if (empty($content['identity_param'])) {
            throw new \InvalidArgumentException('The identity_param of Biz Content can not be empty');
        }

        $this->apiParams["biz_content"] = json_encode($content, JSON_UNESCAPED_UNICODE);

        return $this;
	}
}
