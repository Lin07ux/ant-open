<?php
/**
 * 响应类
 *
 * Author: Lin07ux
 * Created_at: 2018-12-16 23:21:53
 */

namespace AntOpen;

class Response
{
    /**
     * 响应后缀
     */
    const RESPONSE_SUFFIX = '_response';

    /**
     * 错误响应名称
     */
    const ERROR_RESPONSE = 'error_response';

    /**
     * 签名节点名称
     */
    const SIGN_NODE_NAME = 'sign';

    /**
     * XML 加密节点
     */
    const ENCRYPT_XML_NODE_NAME = 'response_encrypted';

    /**
     * @var array 响应数据
     */
    protected $response = [];

    /**
     * @var  string|null 响应数据签名
     */
    protected $sign = null;

    /**
     * SignData constructor.
     *
     * @param  string  $apiName
     * @param  string  $content
     * @param  string  $format
     */
    public function __construct ($apiName, $content, $format = 'json')
    {
        $responseKeyName = $this->getResponseKeyName($apiName);
        $format = strtolower($format);

        if ($format === 'json') {
            $this->parseJson($responseKeyName, $content);
        } else if ($format === 'xml') {
            $this->parseXML($responseKeyName, $content);
        } else {
            throw new \InvalidArgumentException('Unsupported format type! Only supports json or xml');
        }
    }

    /**
     * 获取数据
     *
     * @return array|null
     */
    public function getResponse ()
    {
        return $this->response;
    }

    /**
     * 获取签名
     *
     * @return null|string
     */
    public function getSign ()
    {
        return $this->sign;
    }

    /**
     * 验证数据和签名
     *
     * @param  string  $publicKey
     * @param  string  $signType
     * @return bool
     */
    public function verifySignature ($publicKey, $signType = 'RSA2')
    {
        if (! empty($this->sign) && ! empty($this->response)) {
            $data = json_encode($this->response, JSON_UNESCAPED_UNICODE);
            $alg = 'RSA' === strtolower($signType) ? OPENSSL_ALGO_SHA1 : OPENSSL_ALGO_SHA256;

            return openssl_verify($data, base64_decode($this->sign), $publicKey, $alg) === 1;
        }

        return false;
    }

    /**
     * 请求是否成功
     *
     * @return bool
     */
    public function isSuccess ()
    {
        return ! empty($this->response) && empty($this->response['sub_code']);
    }

    /**
     * 获取响应代码
     *
     * @return string|null
     */
    public function getCode ()
    {
        return isset($this->response['code']) ? $this->response['code'] : null;
    }

    /**
     * 获取响应信息
     *
     * @return string|null
     */
    public function getMessage ()
    {
        return isset($this->response['msg']) ? $this->response['msg'] : null;
    }

    /**
     * 获取语义化响应代码
     *
     * @return string|null
     */
    public function getSubCode ()
    {
        return isset($this->response['sub_code']) ? $this->response['sub_code'] : null;
    }

    /**
     *  获取语义化响应信息
     *
     * @return string|null
     */
    public function getSubMessage ()
    {
        return isset($this->response['sub_msg']) ? $this->response['sub_msg'] : null;
    }

    /**
     * 解析 json 数据
     *
     * @param  string  $responseKeyName
     * @param  string  $content
     */
    protected function parseJson ($responseKeyName, $content)
    {
        $response = json_decode($content, true);

        if (! empty($response)) {
            $this->sign = empty($response[self::SIGN_NODE_NAME]) ? null : $response[self::SIGN_NODE_NAME];
            $this->response = empty($response[$responseKeyName]) ? [] : $response[$responseKeyName];
        }
    }

    /**
     * 解析 xml 数据
     *
     * @param  string  $responseKeyName
     * @param  string  $content
     */
    protected function parseXML ($responseKeyName, $content)
    {
        $disableLibxmlEntityLoader = libxml_disable_entity_loader(true);

        if ($response = @simplexml_load_string($content)) {
            $response = (array)$response;

            $this->sign = $response[self::SIGN_NODE_NAME];

            unset($response[self::SIGN_NODE_NAME]);

            $this->response = empty($response) ? $this->getResponseFromXML($responseKeyName, $content) : $response;
        }

        libxml_disable_entity_loader($disableLibxmlEntityLoader);
    }

    /**
     * 从原始 xml 数据中获取响应数据
     *
     * @param  string  $responseKeyName
     * @param  string  $content
     * @return array|null
     */
    private function getResponseFromXML($responseKeyName, $content)
    {
        $nodeName = "<{$responseKeyName}>";
        $startIndex = strpos($content, $nodeName);
        $length = 0;

        if ($startIndex < 0) {
            $nodeName = '<'.self::ERROR_RESPONSE.'>';
            $startIndex = strpos($content, $nodeName);
        }

        if ($startIndex >= 0) {
            $startIndex += strlen($nodeName);
            $length = strrpos($content, '<'.self::SIGN_NODE_NAME.'>') - $startIndex;
        }

        return $length > 0 ? json_decode(substr($content, $startIndex, $length), true) : null;
    }

    /**
     * 获取响应数据的键名
     *
     * @param  string  $requestMethod
     * @return string
     */
    private function getResponseKeyName ($requestMethod)
    {
        return str_replace(".", "_", $requestMethod) . self::RESPONSE_SUFFIX;
    }
}