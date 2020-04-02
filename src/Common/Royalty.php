<?php
/**
 * 分账信息
 *
 * Author: Lin07ux
 * Created_at: 2020-04-01 21:48:20
 */

namespace AntOpen\Common;

class Royalty
{
    /**
     * 普通分账
     */
    const TYPE_TRANSFER = 'transfer';

    /**
     * 补差分账
     */
    const TYPE_REPLENISH = 'replenish';

    /**
     * @var array 分账信息
     */
    private $data = [];

    /**
     * Royalty constructor.
     *
     * @param  Account       $inAccount    分账收入账户
     * @param  Account|null  $outAccount   分账支出账户
     * @param  bool          $isReplenish  是否是补差分账
     * @param  float         $amount       分账金额
     * @param  integer       $percent      分账百分比
     * @param  string|null   $desc         描述信息
     */
    public function __construct (Account $inAccount, Account $outAccount = null, $isReplenish = false, $amount = 0.0, $percent = 50, $desc = null)
    {
        $this->setInAccount($inAccount);
        $this->setOutAccount($outAccount);
        $this->setRoyaltyType($isReplenish);
        $this->setAmount($amount, $percent);
        $this->setDescription($desc);
    }

    /**
     * 设置分账收入方账户
     *
     * @param  Account  $account
     * @return $this
     */
    public function setInAccount (Account $account)
    {
        if ($account->getType() === Account::TYPE_DEFAULT) {
            throw new \InvalidArgumentException('分账收入方账户类别不被支持');
        }

        $this->data['trans_in'] = $account->getAccount();
        $this->data['trans_in_type'] = $account->getType();

        return $this;
    }

    /**
     * 设置分账支出账户
     *
     * @param  Account  $account
     * @return $this
     */
    public function setOutAccount (Account $account)
    {
        $type = $account->getType();

        if ($type !== Account::TYPE_LOGIN_NAME && $type !== Account::TYPE_USER_ID) {
            throw new \InvalidArgumentException('分账支出方账户只支持支付宝登录账号和支付宝唯一用户号两种');
        }

        $this->data['trans_out'] = $account->getAccount();
        $this->data['trans_out_type'] = $type;

        return $this;
    }

    /**
     * 设置分账类型
     *
     * @param  bool  $isReplenish  是否是补差分账
     * @return $this
     */
    public function setRoyaltyType ($isReplenish = false)
    {
        $this->data['royalty_type'] = $isReplenish ? self::TYPE_REPLENISH : self::TYPE_TRANSFER;

        return $this;
    }

    /**
     * 分账的金额
     *
     * @param  float    $amount      金额(元)
     * @param  integer  $percentage  分账百分比(大于 0、少于或等于 100 的整数）
     * @return $this
     */
    public function setAmount ($amount = 0.0, $percentage = 50)
    {
        $amount = (int)(number_format($amount, 2) * 100);

        if ($amount < 1 || $amount > 100000000 * 100) {
            throw new \InvalidArgumentException('分账金额不得小于 0.01 元且不得大于 1 亿元');
        }

        $percentage = (int)$percentage;

        if ($percentage < 1 || $percentage > 100) {
            throw new \InvalidArgumentException('分账比例需为不小于 1 且不大于 100 的整数');
        }

        $this->data['amount'] = (float)$amount / 100;
        $this->data['amount_percentage'] = $percentage;

        return $this;
    }

    /**
     * 设置分账描述信息
     *
     * @param  string|null  $description
     * @return $this
     */
    public function setDescription ($description = null)
    {
        $this->data['desc'] = $description;

        return $this;
    }

    /**
     * 获取数组信息
     *
     * @return array
     */
    public function toArray ()
    {
        return array_filter($this->data);
    }
}