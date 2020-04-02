<?php
/**
 * 账户类型
 *
 * Author: Lin07ux
 * Created_at: 2020-04-02 09:44:18
 */

namespace AntOpen\Common;

class Account
{
    /**
     * 银行卡编号
     */
    const TYPE_CARD = 'cardAliasNo';

    /**
     * 支付宝登录号
     */
    const TYPE_LOGIN_NAME = 'loginName';

    /**
     * 支付宝唯一用户号
     */
    const TYPE_USER_ID = 'userId';

    /**
     * 默认结算账号
     */
    const TYPE_DEFAULT = 'defaultSettle';

    /**
     * @var string 账户类别
     */
    private $type;

    /**
     * @var string 账户号
     */
    private $account;

    /**
     * Account constructor.
     *
     * @param string $type     账户类别
     * @param string $account  账户号
     */
    public function __construct ($type, $account = '')
    {
        $this->setAccount($type, $account);
    }

    /**
     * 获取账户类别
     *
     * @return string
     */
    public function getType ()
    {
        return $this->type;
    }

    /**
     * 获取账户号
     *
     * @return string
     */
    public function getAccount ()
    {
        return $this->account;
    }

    /**
     * 设置账户
     *
     * @param  string  $type
     * @param  string  $account
     * @return void
     */
    private function setAccount ($type, $account = '')
    {
        switch ($type) {
            case self::TYPE_CARD:
            case self::TYPE_LOGIN_NAME:
                if (empty($account)) {
                    throw new \InvalidArgumentException('账户不得为空');
                } elseif (mb_strlen($account) > 64) {
                    throw new \InvalidArgumentException('结算收款方账户不得超过 64 个字符');
                }
                break;
            case self::TYPE_USER_ID:
                if (! preg_match('/^2088\d{12}$/', $account)) {
                    throw new \InvalidArgumentException('账户应为以 2088 开头的 16 位数字支付宝账号');
                }
                break;
            case self::TYPE_DEFAULT:
                $account = '';
                break;
            default:
                throw new \InvalidArgumentException('不受支持的账户类别');
        }

        $this->type = $type;
        $this->account = $account;
    }
}