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
     * 成功响应时的 code 值
     */
    const SUCCESS_CODE = 10000;

    /**
     * @var array 响应数据
     */
    protected $data = [];

    /**
     * @var integer 响应代码
     */
    protected $code;

    /**
     * @var string 响应信息
     */
    protected $message;

    /**
     * Response constructor.
     *
     * @param  array  $response
     */
    public function __construct (array $response)
    {
        if (! isset($response['code']) || ! isset($response['msg'])) {
            throw new \InvalidArgumentException('Invalid response data');
        }

        $this->code = (int)$response['code'];
        $this->message = $response['msg'];
        $this->data = $response;
    }

    /**
     * 请求是否成功
     *
     * @return bool
     */
    public function isSuccess ()
    {
        return $this->code === self::SUCCESS_CODE;
    }

    /**
     * 获取响应代码
     *
     * @return string|null
     */
    public function getCode ()
    {
        return $this->code;
    }

    /**
     * 获取响应信息
     *
     * @return string|null
     */
    public function getMessage ()
    {
        return $this->message;
    }

    /**
     * 获取错误提示信息
     *
     * @return string|null
     */
    public function getErrorMessage ()
    {
        $message = $this->getResponseData('sub_msg');

        return $message ? "{$message}({$this->getResponseData('sub_code')})" : null;
    }

    /**
     * 获取响应数据
     *
     * @param  string  $name     数据名称
     * @param  mixed   $default  默认值
     *
     * @return mixed
     */
    public function getResponseData ($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->data;
        }

        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }
}