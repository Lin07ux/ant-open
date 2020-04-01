<?php
/**
 * 支付订单状态
 *
 * Author: Lin07ux
 * Created_at: 2020-04-01 16:40:14
 */

namespace AntOpen\Common;


class TradeStatus
{
    /**
     * 交易已创建，等待买家付款
     */
    const WAIT = 'WAIT_BUYER_PAY';

    /**
     * 未付款交易超时关闭，或支付完成后全额退款
     */
    const CLOSED = 'TRADE_CLOSED';

    /**
     * 交易支付成功
     */
    const SUCCESS = 'TRADE_SUCCESS';

    /**
     * 交易结束，不可退款
     */
    const FINISHED = 'TRADE_FINISHED';
}