<?php
/**
 * 蚂蚁金服开放平台 API 接口请求客户端类
 *
 * Author: Lin07ux
 * Created_at: 2018-12-15 23:59:11
 */

namespace AntOpen;

use AntOpen\Exception\BadRequestException;
use AntOpen\Exception\BadResponseException;
use AntOpen\Exception\InvalidRequestContentException;
use AntOpen\Requests\Request;

class AlipayClient
{
    /**
     * API 版本号
     */
    const VERSION = '1.0';

    /**
     * 请求数据格式
     */
    const FORMAT = 'JSON';

    /**
     * 字符集编码
     */
    const CHARSET = 'utf-8';

    /**
     * 加密类型
     */
    const ENCRYPT_TYPE = 'AES';

    /**
     * @var string 网关
     */
    protected $gateway = 'https://openapi.alipay.com/gateway.do';

    /**
     * @var string 签名类型
     */
    protected $signType = "RSA2";

    /**
     * @var string 应用 APP ID
     */
    protected $appId;

    /**
     * @var string 支付宝公钥字符串
     */
    protected $alipayRsaPublicKey;

    /**
     * @var string 商户私钥字符串
     */
    protected $customerRsaPrivateKey;

    /**
     * @var string 加密密钥
     */
    protected $encryptKey;

    /**
     * AntClient constructor.
     *
     * @param  string       $appId 应用 ID
     * @param  string|null  $customerRsaPrivateKey 商户私钥
     * @param  string|null  $alipayRsaPublicKey 支付宝公钥
     * @param  string|null  $encryptKey 加密密钥
     */
    public function __construct ($appId, $customerRsaPrivateKey = null, $alipayRsaPublicKey = null, $encryptKey = null)
    {
        if ($this->isEmpty($appId) || !$appId) {
            throw new \InvalidArgumentException('App id is invalid!');
        }

        $this->appId = $appId;

        if ($customerRsaPrivateKey) {
            $this->setCustomerRsaPrivateKey($customerRsaPrivateKey);
        }

        if ($alipayRsaPublicKey) {
            $this->setAlipayRsaPublicKey($alipayRsaPublicKey);
        }

        if ($encryptKey) {
            $this->setEncryptKey($encryptKey);
        }
    }

    /**
     * 获取服务器网关
     *
     * @return string
     */
    public function getGateway ()
    {
        return $this->gateway;
    }

    /**
     * 设置服务器网关
     *
     * @param  string  $gateway
     * @return $this
     */
    public function setGateway ($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * 获取 app id
     *
     * @return string
     */
    public function getAppId ()
    {
        return $this->appId;
    }

    /**
     * 设置 app id
     *
     * @param  string  $appId
     * @return $this
     */
    public function setAppId ($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * 获取支付宝 RSA 公钥
     *
     * @return string
     */
    public function getAlipayRsaPublicKey ()
    {
        return $this->alipayRsaPublicKey;
    }

    /**
     * 设置支付宝 RSA 公钥
     *
     * @param  string  $alipayRsaPublicKey
     * @param  bool    $isFile
     * @return $this
     */
    public function setAlipayRsaPublicKey ($alipayRsaPublicKey, $isFile = false)
    {
        $key = !empty($alipayRsaPublicKey) && $isFile ? file_get_contents($alipayRsaPublicKey) : $alipayRsaPublicKey;

        if ($this->isEmpty($key)) {
            throw new \InvalidArgumentException('The Alipay RSA public key or file is invalid.');
        }

        $this->alipayRsaPublicKey = $key;

        return $this;
    }

    /**
     * 获取商户 RSA 私钥
     *
     * @return string
     */
    public function getCustomerRsaPrivateKey ()
    {
        return $this->customerRsaPrivateKey;
    }

    /**
     * 设置商户 RSA 私钥
     *
     * @param  string  $customerRsaPrivateKey
     * @param  bool    $isFile
     * @return $this
     */
    public function setCustomerRsaPrivateKey ($customerRsaPrivateKey, $isFile = false)
    {
        $key = !empty($customerRsaPrivateKey) && $isFile ? file_get_contents($customerRsaPrivateKey) : $customerRsaPrivateKey;

        if ($this->isEmpty($key)) {
            throw new \InvalidArgumentException('The customer RSA public key or file is invalid.');
        }

        $this->customerRsaPrivateKey = $key;

        return $this;
    }

    /**
     * 获取加密密钥
     *
     * @return string
     */
    public function getEncryptKey ()
    {
        return $this->encryptKey;
    }

    /**
     * 设置加密密钥
     *
     * @param  string  $encryptKey
     * @return $this
     */
    public function setEncryptKey ($encryptKey)
    {
        if ($this->isEmpty($encryptKey)) {
            throw new \InvalidArgumentException('The AES encrypt key is invalid.');
        }

        $this->encryptKey = $encryptKey;

        return $this;
    }

    /**
     * 获取签名类别
     *
     * @return string
     */
    public function getSignType ()
    {
        return $this->signType;
    }

    /**
     * 设置签名类别
     *
     * @param  string  $type
     * @return $this
     */
    public function setSignType ($type)
    {
        $type = strtoupper($type);

        if (! in_array($type, ['RSA', 'RSA2'])) {
            throw new \InvalidArgumentException('The sign type can only be RSA or RSA2');
        }

        $this->signType = $type;

        return $this;
    }

    /**
     * 执行请求
     *
     * @param  Request     $request
     * @param  string|null $appAuthToken
     *
     * @return Response
     */
    public function request (Request $request, $appAuthToken = null)
    {
        // 生成 业务参数 和 系统参数
        $apiParams = $this->getApiParams($request);
        $sysParams = $this->getSystemParams($request, $appAuthToken);
        $sysParams["sign"] = $this->sign($this->buildParamsString(array_merge($apiParams, $sysParams)));

        // 发送请求，生成响应
        $content = $this->post($this->buildRequestUrl($sysParams), $apiParams);

        // 解析数据，并验证签名
        $parser = new Parser($content, $request->getMethod(), static::FORMAT);
        if (! $this->verifySignature($parser->getOriginal(), $parser->getSignature())) {
            throw new BadResponseException('Failure to verify data signature');
        }

        // 构造响应数据
        $response = new Response($parser->getResponse());
        if (! $response->isSuccess()) {
            throw new BadResponseException(
                $response->getErrorMessage() ?: 'Server returns an exception response!',
                $response->getCode()
            );
        }

        return $response;
    }

    /**
     * 页面提交执行方法
     *
     * @param  Request     $request    跳转类接口的 request
     * @param  string      $httpMethod 提交方式(post、get)
     * @param  string|null $appAuthToken
     *
     * @return string 跳转 URL(GET) 或 HTML 表单(POST)
     */
    public function requestFromPage (Request $request, $httpMethod = "GET", $appAuthToken = null)
    {
        $params = array_merge($this->getApiParams($request), $this->getSystemParams($request, $appAuthToken));
        $params['sign'] = $this->sign($this->buildParamsString($params));

        return "GET" === strtoupper($httpMethod) ? $this->buildRequestUrl($params) : $this->buildRequestForm($params);
    }

    /**
     * 获取业务请求参数
     *
     * @param  Request  $request
     *
     * @return array
     * @throws InvalidRequestContentException
     * @throws \RuntimeException
     */
    private function getApiParams (Request $request)
    {
        $apiParams['biz_content'] = $request->getParams(true);

        if ($request->getNeedEncrypt()) {
            if ($this->isEmpty($apiParams['biz_content'])) {
                throw new InvalidRequestContentException("The biz_content of request can not be empty");
            }

            if ($this->isEmpty($encryptKey = $this->getEncryptKey())) {
                throw new \RuntimeException("AES encrypt key can not be empty");
            }

            $apiParams['encrypt_type'] = self::ENCRYPT_TYPE;
            $apiParams['biz_content'] = Encrypt::encrypt($apiParams['biz_content'], $encryptKey);
        }

        return $apiParams;
    }

    /**
     * 获取请求的公共参数
     *
     * @param  Request      $request
     * @param  string|null  $appAuthToken
     *
     * @return array
     */
    private function getSystemParams (Request $request, $appAuthToken = null)
    {
        return array_filter([
            'app_id' => $this->appId,
            'format' => static::FORMAT,
            'charset' => static::CHARSET,
            'sign_type' => $this->signType,
            'method' => $request->getMethod(),
            'return_url' => $request->getReturnUrl(),
            'version' => $request->getVersion() ?: static::VERSION,
            'timestamp' => date("Y-m-d H:i:s"),
            'app_auth_token' => $appAuthToken,
        ]);
    }

    /**
     * 获取待签名字符串
     *
     * @param  array  $params    签名数据
     * @param  bool   $urlEncode 是否需要对每个值做 url encode 处理
     * @return string
     */
    private function buildParamsString ($params, $urlEncode = false)
    {
        ksort($params);

        $paramsToBeSigned = [];

        foreach ($params as $k => $v) {
            if (! $this->isEmpty($v) && "@" != substr($v, 0, 1)) {
                $paramsToBeSigned[] = $k."=".($urlEncode ? urlencode($v) : $v);
            }
        }

        return implode('&', $paramsToBeSigned);
    }

    /**
     * 获取请求网址
     *
     * @param  array  $params
     * @return string
     */
    private function buildRequestUrl (array $params = [])
    {
        return $this->getGateway()."?".$this->buildParamsString($params, true);
    }

    /**
     * 以 HTML 表单形式构造请求
     *
     * @param  array  $params 请求参数数组
     * @return string
     */
    private function buildRequestForm ($params)
    {
        $action = $this->getGateway().'?charset='.self::CHARSET;
        $form = '<form id="alipaysubmit" name="alipaysubmit" action="'.$action.'" method="POST">';

        foreach ($params as $key => $val) {
            if (! $this->isEmpty($val)) {
                $val = str_replace("'", "&apos;", $val);
                $form .= "<input type='hidden' name='{$key}' value='{$val}'/>";
            }
        }

        // submit 按钮控件请不要含有 name 属性
        $form .= '<input type="submit" value="ok" style="display:none;"></form>';
        $form .= '<script>document.forms["alipaysubmit"].submit();</script>';

        return $form;
    }

    /**
     * 对数据进行签名
     *
     * @param  string  $content 签名内容
     * @return string
     */
    private function sign ($content)
    {
        $alg = "RSA" === $this->getSignType() ? OPENSSL_ALGO_SHA1 : OPENSSL_ALGO_SHA256;

        openssl_sign($content, $sign, $this->getCustomerRsaPrivateKey(), $alg);

        return base64_encode($sign);
    }

    /**
     * 验证签名
     *
     * @param  string  $content   待签名内容
     * @param  string  $signature 签名字符串
     *
     * @return bool
     */
    private function verifySignature ($content, $signature)
    {
        $alg = 'RSA' === strtolower($this->getSignType()) ? OPENSSL_ALGO_SHA1 : OPENSSL_ALGO_SHA256;

        return openssl_verify($content, base64_decode($signature), $this->getAlipayRsaPublicKey(), $alg) === 1;
    }

    /**
     * 发送 POST 请求
     *
     * @param  string  $url
     * @param  array   $postFields
     * @return string
     */
    private function post ($url, array $postFields = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (is_array($postFields) && count($postFields)) {
            foreach ($postFields as $k => $v) {
                if ("@" === substr($v, 0, 1)) {
                    $postFields[$k] = new \CURLFile(substr($v, 1));
                }
            }

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

            $headers = ['content-type: multipart/form-data;charset='.self::CHARSET.';boundary='.$this->getMillisecond()];
        } else {
            $headers = ['content-type: application/x-www-form-urlencoded;charset='.self::CHARSET];
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new BadRequestException(curl_error($ch));
        }

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (200 !== $httpStatusCode) {
            throw new BadResponseException($response ?: 'The request failed, Please try again later', $httpStatusCode);
        }

        return $response;
    }

    /**
     * 校验值是否为空
     *
     * @param  mixed  $value
     * @return bool
     **/
    private function isEmpty ($value)
    {
        return $value === false || $value === null || trim($value) === "";
    }

    /**
     * 获取毫秒
     *
     * @return float
     */
    private function getMillisecond ()
    {
        list($s1, $s2) = explode(' ', microtime());

        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }
}