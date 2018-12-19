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
     * 成功响应时的 code 值
     */
    const SUCCESS_CODE = '10000';

    /**
     * @var array 响应数据
     */
    protected $response = [];

    /**
     * @var  string|null 响应数据签名
     */
    protected $sign = null;

    /**
     * @var string 原始响应数据
     */
    protected $content;

    /**
     * SignData constructor.
     *
     * @param  string  $content
     * @param  string  $apiName
     * @param  string  $format
     */
    public function __construct ($content, $apiName, $format = 'json')
    {
        switch (strtolower($format)) {
            case 'json':
                $this->parseJson($content, $apiName);
                break;
            case 'query':
                $this->parseQuery($content, $apiName);
                break;
            case 'xml':
                $this->parseXML($content, $apiName);
                break;
            default:
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
        if (! empty($this->sign) && ! empty($this->content)) {
            $alg = 'RSA' === strtolower($signType) ? OPENSSL_ALGO_SHA1 : OPENSSL_ALGO_SHA256;

            return openssl_verify($this->content, base64_decode($this->sign), $publicKey, $alg) === 1;
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
        if (! empty($this->response)) {
            if (empty($this->response['code'])) {
                return empty($this->response['sub_code']);
            }

            return $this->response['code'] === self::SUCCESS_CODE;
        }

        return false;
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
     * @param  string  $content
     * @param  string  $apiName
     * @return void
     */
    protected function parseJson ($content, $apiName)
    {
        $response = json_decode($content, true);

        if (! empty($response)) {
            $this->sign = empty($response[self::SIGN_NODE_NAME]) ? null : $response[self::SIGN_NODE_NAME];
            $responseKeyName = $this->getResponseKeyName($apiName);

            if (! empty($response[$responseKeyName])) {
                $this->content = json_encode($response[$responseKeyName], JSON_UNESCAPED_UNICODE);
                $this->response = json_decode($this->formatJsonString($this->content), true);
            }
        }
    }

    /**
     * 解析查询字符串数据
     *
     * @param  string  $content 响应数据
     * @param  string  $sign    响应签名
     * @return void
     */
    protected function parseQuery ($content, $sign)
    {
        $this->sign = $sign ?: null;
        $this->response = json_decode($this->formatJsonString($content), true) ?: [];
        $this->content = $content;
    }

    /**
     * 解析 xml 数据
     *
     * @param  string  $content
     * @param  string  $apiName
     * @return void
     */
    protected function parseXML ($content, $apiName)
    {
        $disableLibxmlEntityLoader = libxml_disable_entity_loader(true);

        if ($response = @simplexml_load_string($content)) {
            $response = (array)$response;

            $this->sign = empty($response[self::SIGN_NODE_NAME]) ? null : $response[self::SIGN_NODE_NAME];

            unset($response[self::SIGN_NODE_NAME]);

            if (! empty($response)) {
                $this->parseResponseFromXML($content, $apiName);
            } else {
                $this->response = $response;
                $this->content = json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }

        libxml_disable_entity_loader($disableLibxmlEntityLoader);
    }

    /**
     * 从原始 xml 数据中获取响应数据
     *
     * @param  string  $content
     * @param  string  $apiName
     * @return void
     */
    private function parseResponseFromXML ($content, $apiName)
    {
        $nodeName = "<{$this->getResponseKeyName($apiName)}>";
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

        if ($length > 0) {
            $this->content = substr($content, $startIndex, $length);
            $this->response = json_decode($this->formatJsonString($content), true);
        }
    }

    /**
     * 处理 json 字符串，替换不必要的符号
     *
     * @param  string  $string
     * @return string
     */
    private function formatJsonString ($string)
    {
        $search = ['"[', ']"', '"{', '}"', '\"'];
        $replace = ['[', ']', '{', '}', '"'];

        return str_replace($search, $replace, $string);
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