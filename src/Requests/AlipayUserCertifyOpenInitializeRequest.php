<?php
/**
 * 支付宝身份认证初始化服务
 *
 * Author: Lin07ux
 * Created_at: 2019-10-31 15:06:22
 */

namespace AntOpen\Requests;

class AlipayUserCertifyOpenInitializeRequest extends Request
{
    /**
     * 设置商户请求的唯一标志
     *
     * @param  string  $orderNo
     *
     * @return $this
     */
    public function setOuterOrderNoParam ($orderNo)
    {
        if (strlen($orderNo) > 32) {
            throw new \InvalidArgumentException('The value of outer_order_no param can not exceed 32 characters');
        }

        $this->params['outer_order_no'] = $orderNo;

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
     * 设置认证场景及身份信息
     *
     * @param  string  $name 真实姓名
     * @param  string  $cert 身份证号
     *
     * @return $this
     */
    public function setIdentityParam ($name, $cert)
    {
        if (! preg_match('/^\d{15}(\d{2}[\dxX])?$/', $cert)) {
            throw new \InvalidArgumentException('The value of identity_param.cert should be the correct ID number of mainland China');
        }

        $this->params['identity_param'] = [
            'identity_type' => 'CERT_INFO',
            'cert_type' => 'IDENTITY_CARD',
            'cert_name' => $name,
            'cert_no' => $cert,
        ];

        return $this;
    }

    /**
     * 设置认证后的返回地址
     *
     * @param  string  $url          认证后回跳的目标地址
     * @param  bool    $openInAlipay 是否需要在 Alipay 中打开回调地址
     *
     * @return $this
     */
    public function setMerchantUrlParam ($url, $openInAlipay = true)
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('The value of merchant_config.return_url should be a legal url');
        }

        if ($openInAlipay) {
            $url = 'alipays://platformapi/startapp?appId=20000067&url='.urlencode($url);
        }

        $this->params['merchant_config'] = ['return_url' => $url];

        return $this;
    }

    /**
     * 设置自定义人脸比对图片
     *
     * @param  string  $content 人脸比对图片的 base64 编码格式的字符串
     *
     * @return $this
     */
    public function setFacePictureParam ($content)
    {
        $this->params['face_contrast_picture'] = $content;

        return $this;
    }
}