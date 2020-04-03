<?php
/**
 * 统一收单交易关闭接口
 *
 * 用于交易创建后，用户在一定时间内未进行支付，可调用该接口直接将未付款的交易进行关闭。
 *
 * [官方文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.close)
 *
 * Author: Lin07ux
 * Created_at: 2020-04-02 10:12:02
 */

namespace AntOpen\Requests;

class AlipayTradeCloseRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'alipay.trade.close';

    /**
     * 设置商户订单号
     *
     * 和支付宝交易号不能同时为空，如果两者同时存在，以支付宝交易号为准
     *
     * @param  string  $no
     * @return $this
     */
    public function setOutTradeNoParam ($no)
    {
        if (empty($no) || strlen($no) > 64 || ! preg_match('/^[a-zA-Z\d_]{1,64}$/', $no)) {
            throw new \InvalidArgumentException('商户订单号只能包含字母、数字、下划线，且不得超过 64 个字符');
        }

        $this->params['out_trade_no'] = $no;

        return $this;
    }

    /**
     * 设置支付宝交易号
     *
     * 和商户订单号不能同时为空，如果两者同时存在，则以此为准
     *
     * @param  string  $no
     * @return $this
     */
    public function setTradeNoParam ($no)
    {
        if (empty($no) || strlen($no) > 64 || ! preg_match('/^[a-zA-Z\d_]{1,64}$/', $no)) {
            throw new \InvalidArgumentException('支付宝交易号只能包含字母、数字、下划线，且不得超过 64 个字符');
        }

        $this->params['trade_no'] = $no;

        return $this;
    }

    /**
     * 设置商户操作员 ID
     *
     * @param  string  $id
     * @return $this
     */
    public function setOperatorIdParam ($id)
    {
        if (mb_strlen($id) > 28) {
            throw new \InvalidArgumentException('商户操作员编号不得超过 28 个字符');
        }

        $this->params['operator_id'] = $id;

        return $this;
    }
}