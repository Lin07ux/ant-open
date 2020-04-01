<?php
/**
 * 有效的支付渠道
 *
 * Author: Lin07ux
 * Created_at: 2020-04-01 11:04:18
 */

namespace AntOpen\Common;


class PayChannel
{
    /**
     * 余额
     */
    const BALANCE = 'balance';

    /**
     * 余额宝
     */
    const MONEY_FUND = 'moneyFund';

    /**
     * 网银
     */
    const BANK_PAY = 'bankPay';

    /**
     * 借记卡快捷
     */
    const DEBIT_CARD_EXPRESS = 'debitCardExpress';

    /**
     * 信用卡快捷
     */
    const CREDIT_CARD_EXPRESS = 'creditCardExpress';

    /**
     * 信用卡卡通
     */
    const CREDIT_CARD_CARTOON = 'creditCardCartoon';

    /**
     * 信用卡
     */
    const CREDIT_CARD = 'creditCard';

    /**
     * 卡通
     */
    const CARTOON = 'cartoon';

    /**
     * 花呗
     */
    const P_CREDIT = 'pcredit';

    /**
     * 花呗分期
     */
    const P_CREDIT_PAY_INSTALLMENT = 'pcreditpayInstallment';

    /**
     * 信用支付类型（包含 信用卡卡通，信用卡快捷,花呗，花呗分期）
     */
    const CREDIT_GROUP = 'credit_group';

    /**
     * 红包
     */
    const COUPON = 'coupon';

    /**
     * 积分
     */
    const POINT = 'point';

    /**
     * 优惠（包含实时优惠+商户优惠）
     */
    const PROMOTION = 'promotion';

    /**
     * 营销券
     */
    const VOUCHER = 'voucher';

    /**
     * 商户优惠
     */
    const MERCHANT_DISCOUNT = 'mdiscount';

    /**
     * 亲密付
     */
    const HONEY_PAY = 'honeyPay';

    /**
     * 商户预存卡
     */
    const MERCHANT_CARD = 'mcard';

    /**
     * 个人预存卡
     */
    const PERSONAL_CARD = 'pcard';

    /**
     * 获取全部渠道
     *
     * @return array
     */
    public static function channels ()
    {
        try {
            $reflect = new \ReflectionClass(__CLASS__);

            return array_values($reflect->getConstants());
        } catch (\ReflectionException $exception) {
            return [];
        }
    }
}