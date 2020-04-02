<?php
/**
 * 统一收单交易退款接口
 *
 * 当交易发生之后一段时间内，由于买家或者卖家的原因需要退款时，卖家可以通过退款接口将支付款退还给买家，
 * 支付宝将在收到退款请求并且验证成功之后，按照退款规则将支付款按原路退到买家帐号上。
 *
 * 退款有如下约束条件：
 *
 * 1. 交易超过约定时间（签约时设置的可退款时间）的订单无法进行退款；
 * 2. 支持单笔交易分多次退款，多次退款需要提交原支付订单的商户订单号和设置不同的退款单号；
 * 3. 一笔退款失败后重新提交，要采用原来的退款单号；
 * 4. 总退款金额不能超过用户实际支付金额。
 *
 * [官方文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.refund)
 *
 * Author: Lin07ux
 * Created_at: 2020-04-01 16:44:51
 */

namespace AntOpen\Requests;

use AntOpen\Common\GoodDetail;
use AntOpen\Common\Royalty;

class AlipayTradeRefundRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'alipay.trade.refund';

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
     * 设置退款请求标识号
     *
     * 同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
     *
     * @param  string  $no
     * @return $this
     */
    public function setOutRequestNoParam ($no)
    {
        if (strlen($no) > 64 || ! preg_match('/^[a-zA-Z\d_]{1,64}$/', $no)) {
            throw new \InvalidArgumentException('退款请求号只能包含字母、数字、下划线，且不得超过 64 个字符');
        }

        $this->params['out_request_no'] = $no;

        return $this;
    }

    /**
     * 设置退款金额
     *
     * @param  float  $amount  退款金额(元)
     * @return $this
     */
    public function setRefundAmountParam ($amount)
    {
        $amount = (int)(number_format($amount, 2) * 100);

        if ($amount < 1 || $amount > 100000000 * 100) {
            throw new \InvalidArgumentException('退款的金额应不小于 0.01 元，且不大于 1 亿元');
        }

        $this->params['refund_amount'] = (float)($amount / 100);

        return $this;
    }

    /**
     * 设置退款币种
     *
     * @param  string  $currency
     * @return $this
     */
    public function setRefundCurrencyParam ($currency)
    {
        $this->params['refund_currency'] = $currency;

        return $this;
    }

    /**
     * 设置退款原因
     *
     * @param  string  $reason
     * @return $this
     */
    public function setRefundReasonParam ($reason)
    {
        if (! empty($reason) || mb_strlen($reason) > 256) {
            throw new \InvalidArgumentException('退款原因不得超过 256 个字符');
        }

        $this->params['refund_reason'] = $reason;

        return $this;
    }

    /**
     * 设置操作员编号
     *
     * @param  string  $id
     * @return $this
     */
    public function setOperatorIdParam ($id)
    {
        if (! empty($id) || mb_strlen($id) > 30) {
            throw new \InvalidArgumentException('商户的操作员编号不得超过 30 个字符');
        }

        $this->params['operator_id'] = $id;

        return $this;
    }

    /**
     * 设置商户店铺 ID
     *
     * @param  string  $id
     * @return $this
     */
    public function setStoreIdParam ($id)
    {
        if (mb_strlen($id) > 32) {
            throw new \InvalidArgumentException('商户门店编号不得超过 32 个字符');
        }

        $this->params['store_id'] = $id;

        return $this;
    }

    /**
     * 设置商户机具终端编号
     *
     * @param  string  $id
     * @return $this
     */
    public function setTerminalIdParam ($id)
    {
        if (mb_strlen($id) > 32) {
            throw new \InvalidArgumentException('商户机具终端编号不得超过 32 个字符');
        }

        $this->params['terminal_id'] = $id;

        return $this;
    }

    /**
     * 设置订单包含的商品列表信息
     *
     * @param  GoodDetail[]|array[]  $goods
     * @return $this
     */
    public function setGoodsDetailParam (array $goods = [])
    {
        $detail = [];

        foreach ($goods as $good) {
            $detail[] = ($good instanceof GoodDetail) ? $good->toArray() : $good;
        }

        $this->params['goods_detail'] = $detail;

        return $this;
    }

    /**
     * 设置退分账明细信息
     *
     * @param  Royalty  $royalty
     * @return $this
     */
    public function setRefundRoyaltyParam (Royalty $royalty)
    {
        $this->params['refund_royalty_parameters'] = $royalty->toArray();

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
}