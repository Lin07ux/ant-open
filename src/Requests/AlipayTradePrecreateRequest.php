<?php
/**
 * 统一收单线下交易预创建
 *
 * 收银员通过收银台或商户后台调用支付宝接口，生成二维码后，展示给用户，由用户扫描二维码完成订单支付。
 *
 * [官方文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.precreate)
 *
 * Author: Lin07ux
 * Created_at: 2020-03-31 11:19:18
 */

namespace AntOpen\Requests;

use AntOpen\Common\GoodDetail;
use AntOpen\Common\PayChannel;
use AntOpen\Common\Settle;

class AlipayTradePrecreateRequest extends Request
{
    /**
     * @var string 接口签名
     */
    protected $method = 'alipay.trade.precreate';

    /**
     * 设置商户订单号
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
     * 设置订单标题
     *
     * @param  string  $subject
     * @return $this
     */
    public function setSubjectParam ($subject)
    {
        if (empty($subject) || mb_strlen($subject) > 256) {
            throw new \InvalidArgumentException('订单标题不得为空，且不得超过 256 个字符');
        }

        $this->params['subject'] = $subject;

        return $this;
    }

    /**
     * 对交易或商品的描述
     *
     * @param  string  $body
     * @return $this
     */
    public function setBodyParam ($body)
    {
        if (mb_strlen($body) > 128) {
            throw new \InvalidArgumentException('订单描述信息不得超过 128 个字符');
        }

        $this->params['body'] = $body;

        return $this;
    }

    /**
     * 设置订单总金额
     *
     * @param  float  $amount 金额(单位：元)
     * @return $this
     */
    public function setTotalAmountParam ($amount)
    {
        $amount = (int)(number_format($amount, 2) * 100);

        if ($amount < 1 || $amount > 100000000 * 100) {
            throw new \InvalidArgumentException('订单总金额应不小于 0.01 元，且不大于 1 亿元');
        }

        $this->params['total_amount'] = (float)($amount / 100);

        return $this;
    }

    /**
     * 设置订单可打折金额
     *
     * @param  float  $amount 打折金额(元)
     * @return $this
     */
    public function setDiscountableAmount ($amount)
    {
        $amount = (int)(number_format($amount, 2) * 100);

        if ($amount < 1 || $amount > 100000000 * 100) {
            throw new \InvalidArgumentException('订单可打折金额的应不小于 0.01 元，且不大于 1 亿元');
        }

        $this->params['discountable_amount'] = (float)($amount / 100);

        return $this;
    }

    /**
     * 设置销售产品码
     *
     * @param  bool  $offline  是否是当面付快捷版
     * @return $this
     */
    public function setProductCodeParam ($offline = false)
    {
        $this->params['product_code'] = $offline ? 'OFFLINE_PAYMENT' : 'FACE_TO_FACE_PAYMENT';

        return $this;
    }

    /**
     * 设置卖家支付宝用户 ID
     *
     * @param  string  $id
     * @return $this
     */
    public function setSellerIdParam ($id)
    {
        if (mb_strlen($id) > 28) {
            throw new \InvalidArgumentException('卖家支付宝用户 ID 不得超过 28 个字符');
        }

        $this->params['seller_id'] = $id;

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
     * 设置商户原始订单号
     *
     * @param  string  $orderNo
     * @return $this
     */
    public function setMerchantOrderNoParam ($orderNo)
    {
        if (mb_strlen($orderNo) > 32) {
            throw new \InvalidArgumentException('商户原始订单号不得超过 32 个字符');
        }

        $this->params['merchant_order_no'] = $orderNo;

        return $this;
    }

    /**
     * 设置可用支付渠道
     *
     * @param  string|array  $channels
     * @return $this
     */
    public function setEnablePayChannelsParam ($channels)
    {
        $validChannels = PayChannel::channels();

        foreach ((array)$channels as $channel) {
            if (! in_array($channel, $validChannels)) {
                throw new \InvalidArgumentException("{$channel} 不是有效的支付渠道");
            }
        }

        $this->params['enable_pay_channels'] = implode(',', $channels);

        return $this;
    }

    /**
     * 设置禁用支付渠道
     *
     * @param  string|array  $channels
     * @return $this
     */
    public function setDisablePayChannelsParam ($channels)
    {
        $validChannels = PayChannel::channels();

        foreach ((array)$channels as $channel) {
            if (! in_array($channel, $validChannels)) {
                throw new \InvalidArgumentException("{$channel} 不是有效的支付渠道");
            }
        }

        $this->params['disable_pay_channels'] = implode(',', $channels);

        return $this;
    }

    /**
     * 设置订单最晚支付时间
     *
     * @param  integer  $minutes      支付时间(单位：分)
     * @param  bool     $afterQrCode  是否从生成二维码开始计时
     * @return $this
     */
    public function setTimeoutExpressParam ($minutes, $afterQrCode = true)
    {
        $minutes = (integer)$minutes;

        if ($minutes < 1 || $minutes > 15 * 24 * 60) {
            throw new \InvalidArgumentException('支付有效时间应在 1 分钟到 15 天内');
        }

        $minutes = "{$minutes}m";

        if ($afterQrCode) {
            $this->params['qr_code_timeout_express'] = $minutes;
        } else {
            $this->params['timeout_express'] = $minutes;
        }

        return $this;
    }

    /**
     * 设置业务扩展参数
     *
     * @param  string|null  $providerId  系统商编号(系统商签约协议的PID)
     * @param  string|null  $cardType    卡类型
     * @return $this
     */
    public function setExtendParam ($providerId = null, $cardType = null)
    {
        $extend = [];

        if (! empty($providerId)) {
            if (mb_strlen($providerId) > 64) {
                throw new \InvalidArgumentException('系统商编号不得超过 64 个字符');
            }

            $extend['sys_service_provider_id'] = $providerId;
        }

        if (! empty($cardType)) {
            if (mb_strlen($cardType) > 32) {
                throw new \InvalidArgumentException('卡类型不得超过 32 个字符');
            }

            $extend['card_type'] = $cardType;
        }

        $this->params['extend_params'] = $extend;

        return $this;
    }

    /**
     * 设置业务信息
     *
     * 订单时间在乘车码场景，传入的是用户刷码乘车时间，如：2019-05-14 09:18:55
     *
     * @param  string|null  $campusCard       校园卡编号
     * @param  string|null  $cardType         虚拟卡卡类型
     * @param  string|null  $actualOrderTime  实际订单时间
     * @return $this
     */
    public function setBusinessParam ($campusCard = null, $cardType = null, $actualOrderTime = null)
    {
        $business = [];

        if (! empty($campusCard)) {
            if (mb_strlen($campusCard) > 64) {
                throw new \InvalidArgumentException('校园卡编号不得超过 64 个字符');
            }

            $business['campus_card'] = $campusCard;
        }

        if (! empty($cardType)) {
            if (mb_strlen($cardType) > 128) {
                throw new \InvalidArgumentException('虚拟卡卡类型不得超过 128 个字符');
            }

            $business['card_type'] = $cardType;
        }

        if (! empty($actualOrderTime)) {
            if ($actualOrderTime === true) {
                $time = date('Y-m-d H:i:s');
            } else {
                $time = date('Y-m-d H:i:s', strtotime($actualOrderTime));

                if ($actualOrderTime !== $time) {
                    throw new \InvalidArgumentException('实际订单时间格式错误或不是有效值，应为 yyyy-mm-dd hh:mm:ss 格式');
                }
            }

            $business['actual_order_time'] = $time;
        }

        $this->params['business_params'] = $business;

        return $this;
    }

    /**
     * 设置结算信息
     *
     * @param  Settle   $settle      结算收款信息
     * @param  integer  $periodDays  超期结算天数
     * @return $this
     */
    public function setSettleInfoParam (Settle $settle, $periodDays = 7)
    {
        if ($periodDays < 1 || $periodDays > 365) {
            throw new \InvalidArgumentException('超期自动确认结算时间不在有效范围内(1 天 ~ 365 天)');
        }

        $this->params['settle_info'] = [
            'settle_detail_infos' => $settle->toArray(),
            'settle_period_time' => $periodDays.'d',
        ];

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
}