<?php
/**
 * 统一收单线下交易查询
 *
 * 该接口提供所有支付宝支付订单的查询，商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑。
 *
 * 需要调用查询接口的情况：
 * * 当商户后台、网络、服务器等出现异常，商户系统最终未接收到支付通知；
 * * 调用支付接口后，返回系统错误或未知交易状态情况；
 * * 调用`alipay.trade.pay`，返回`INPROCESS`的状态；
 * * 调用`alipay.trade.cancel`之前，需确认支付状态。
 *
 * [官方文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.query)
 *
 * Author: Lin07ux
 * Created_at: 2020-04-01 16:15:03
 */

namespace AntOpen\Requests;

class AlipayTradeQueryRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'alipay.trade.query';

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
     * 设置交易所属收单机构的 pid
     *
     * 银行间联模式下有用，其它场景请不要使用
     *
     * @param  string  $pid
     * @return $this
     */
    public function setOrgPidPram ($pid)
    {
        if (mb_strlen($pid) > 16) {
            throw new \InvalidArgumentException('交易所属收单机构的 pid 不得超过 16 个字符');
        }

        $this->params['org_pid'] = $pid;

        return $this;
    }

    /**
     * 设置查询选项
     *
     * 商户通过上送该字段来定制查询返回信息，比如：设置 TRADE_SETTLE_INFO 来查询交易的收款账户信息
     *
     * @param  array  $options
     * @return $this
     */
    public function setQueryOptionsParam (array $options)
    {
        $this->params['query_options'] = $options;

        return $this;
    }
}