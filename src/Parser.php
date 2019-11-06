<?php
/**
 * Description
 *
 * Author: Lin07ux
 * Created_at: 2019-11-05 17:19:50
 */

namespace AntOpen;

// TODO: 加密响应的解析、XML 数据的解析规则
class Parser
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
     * @var string 原始响应内容
     */
    protected $original;

    /**
     * @var array 格式化后的响应内容
     */
    protected $response;

    /**
     * @var string 响应签名
     */
    protected $signature;

    /**
     * Parser constructor.
     *
     * @param        $content
     * @param        $requestMethod
     * @param string $format
     */
    public function __construct ($content, $requestMethod, $format = 'json')
    {
        $parser = 'parse'.ucfirst(strtolower($format));

        if (! method_exists($this, $parser)) {
            throw new \InvalidArgumentException('Unsupported format type!');
        }

        $this->$parser($content, $requestMethod);
    }

    /**
     * 获取响应的签名字符串
     *
     * @return string
     */
    public function getSignature ()
    {
        return $this->signature;
    }

    /**
     * 获取格式化后的响应
     *
     * @return array
     */
    public function getResponse ()
    {
        return $this->response;
    }

    /**
     * 获取原始响应数据字符串
     *
     * @return string
     */
    public function getOriginal ()
    {
        return $this->original;
    }

    /**
     * 解析 json 数据
     *
     * @param  string $content
     * @param  string $requestMethod
     *
     * @return void
     */
    protected function parseJson ($content, $requestMethod)
    {
        $response = json_decode($content, true);

        if (! empty($response)) {
            $responseKeyName = $this->getResponseKeyName($requestMethod);

            if (! empty($response[$responseKeyName])) {
                $this->original = json_encode($response[$responseKeyName], JSON_UNESCAPED_UNICODE);
                $this->response = $response[$responseKeyName];
            }

            $this->signature = empty($response[self::SIGN_NODE_NAME]) ? null : $response[self::SIGN_NODE_NAME];
        }
    }

    /**
     * 解析 xml 数据
     *
     * @param  string $content
     * @param  string $requestMethod
     *
     * @return void
     */
    protected function parseXml ($content, $requestMethod)
    {
        $disableLibxmlEntityLoader = libxml_disable_entity_loader(true);

        if ($response = @simplexml_load_string($content)) {
            $response = (array)$response;

            $this->signature = empty($response[self::SIGN_NODE_NAME]) ? null : $response[self::SIGN_NODE_NAME];

            $this->parseResponseFromXML($content, $requestMethod);
        }

        libxml_disable_entity_loader($disableLibxmlEntityLoader);
    }

    /**
     * 从原始 xml 数据中获取响应数据
     *
     * @param  string $content
     * @param  string $requestMethod
     *
     * @return void
     */
    private function parseResponseFromXML ($content, $requestMethod)
    {
        $nodeName = "<{$this->getResponseKeyName($requestMethod)}>";
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
            $this->original = substr($content, $startIndex, $length);
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