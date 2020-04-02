<?php
/**
 * 结算收款方信息
 *
 * Author: Lin07ux
 * Created_at: 2020-04-01 12:29:47
 */

namespace AntOpen\Common;


class Settle
{
    /**
     * 二级商户
     */
    const ENTITY_SECOND = 'SecondMerchantID';

    /**
     * 商户或者直连商户门店
     */
    const ENTITY_STORE = 'Store';

    /**
     * @var array 结算信息
     */
    private $info = [];

    /**
     * Settle constructor.
     *
     * @param  Account      $account    结算收款方账户
     * @param  float        $amount     结算金额(元)
     * @param  string|null  $dimension  结算汇总维度
     * @param  string|null  $entityType 结算主体类型
     * @param  string|null  $entityId   结算主体标识
     */
    public function __construct (Account $account, $amount, $dimension = null, $entityType = null, $entityId = null)
    {
        $this->setAccount($account);
        $this->setAmount($amount);
        $this->setSummaryDimension($dimension);
        $this->setEntity($entityType, $entityId);
    }

    /**
     * 设置结算收款方账户信息
     *
     * @param  Account  $account
     * @return $this
     */
    public function setAccount (Account $account)
    {
        $this->info['trans_in_type'] = $account->getType();
        $this->info['trans_in'] = $account->getAccount();

        return $this;
    }

    /**
     * 设置结算的金额
     *
     * @param  float  $amount  金额(元)
     * @return $this
     */
    public function setAmount ($amount)
    {
        $amount = (int)(number_format($amount, 2) * 100);

        if ($amount < 1 || $amount > 100000000 * 100) {
            throw new \InvalidArgumentException('结算的金额应在 0.01 元 ~ 1 亿元直接');
        }

        $this->info['amount'] = (float)$amount / 100;

        return $this;
    }

    /**
     * 设置结算汇总维度
     *
     * @param  string  $dimension
     * @return $this
     */
    public function setSummaryDimension ($dimension)
    {
        if (! empty($dimension) && mb_strlen($dimension) > 64) {
            throw new \InvalidArgumentException('结算汇总维度不得超过 64 个字符');
        }

        $this->info['summary_dimension'] = $dimension;

        return $this;
    }

    /**
     * 设置结算主体信息
     *
     * @param  string  $type  结算主体类型
     * @param  string  $id    结算主体标识
     * @return $this
     */
    public function setEntity ($type, $id)
    {
        if (! empty($type) && ! in_array($type, [self::ENTITY_SECOND, self::ENTITY_STORE])) {
            throw new \InvalidArgumentException('结算主体类型不合法');
        }

        if (! empty($id) && mb_strlen($id) > 64) {
            throw new \InvalidArgumentException('结算主体标识不得超过 64 个字符');
        }

        $this->info['settle_entity_type'] = $type;
        $this->info['settle_entity_id'] = $id;

        return $this;
    }

    /**
     * 转换成数组结构
     *
     * @return array
     */
    public function toArray ()
    {
        return $this->info;
    }
}