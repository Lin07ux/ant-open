<?php
/**
 * 商品详情
 *
 * Author: Lin07ux
 * Created_at: 2020-04-01 17:13:21
 */

namespace AntOpen\Common;

class GoodDetail
{
    /**
     * @var array 商品详情
     */
    private $detail = [];

    /**
     * GoodDetail constructor.
     *
     * @param  string       $id
     * @param  string       $name
     * @param  integer      $quantity
     * @param  float        $price
     * @param  string|null  $body
     * @param  string|null  $url
     * @param  string|null  $category
     * @param  array        $tree
     * @param  string|null  $alipayId
     */
    public function __construct ($id, $name, $quantity, $price, $body = null, $url = null, $category = null, $tree = [], $alipayId = null)
    {
        $this->setId($id)->setName($name)->setQuantity($quantity)->setPrice($price)->setBody($body)
            ->setShowUrl($url)->setCategory($category)->setCategoriesTree($tree)->setAlipayGoodId($alipayId);
    }

    /**
     * 设置商品编号
     *
     * @param  string  $id
     * @return $this
     */
    public function setId ($id)
    {
        if (empty($id) || mb_strlen($id) > 32) {
            throw new \InvalidArgumentException('商品的编号不得为空，且不能超过 32 个字符');
        }

        $this->detail['id'] = $id;

        return $this;
    }

    /**
     * 设置商品名称
     *
     * @param  string  $name
     * @return $this
     */
    public function setName ($name)
    {
        if (empty($name) || mb_strlen($name) > 256) {
            throw new \InvalidArgumentException('商品名称不得为空，且不能超过 256 个字符');
        }

        $this->detail['name'] = $name;

        return $this;
    }

    /**
     * 设置商品数量
     *
     * @param  integer  $quantity
     * @return $this
     */
    public function setQuantity ($quantity)
    {
        if ($quantity < 1 || $quantity > 1000000000) {
            throw new \InvalidArgumentException('商品数量不得小于 1 且不得大于 1000000000');
        }

        $this->detail['quantity'] = $quantity;

        return $this;
    }

    /**
     * 设置商品单价
     *
     * @param  float  $price  单价(元)
     * @return $this
     */
    public function setPrice ($price)
    {
        $price = (int)(number_format($price, 2) * 100);

        if ($price < 1 || $price > 100000000 * 100) {
            throw new \InvalidArgumentException('商品单价应不小于 0.01 元，且不大于 1 亿元');
        }

        $this->detail['price'] = (float)($price / 100);

        return $this;
    }

    /**
     * 设置商品的支付宝统一编号
     *
     * @param  string  $id
     * @return $this
     */
    public function setAlipayGoodId ($id = null)
    {
        if (! empty($id) && mb_strlen($id) > 32) {
            throw new \InvalidArgumentException('商品的支付宝统一编号不能超过 32 个字符');
        }

        $this->detail['alipay_goods_id'] = $id;

        return $this;
    }

    /**
     * 设置商品描述信息
     *
     * @param  string|null  $body
     * @return $this
     */
    public function setBody ($body = null)
    {
        if (! empty($body) || mb_strlen($body) > 1000) {
            throw new \InvalidArgumentException('商品描述信息不得超过 1000 个字符');
        }

        $this->detail['body'] = $body;

        return $this;
    }

    /**
     * 设置商品的展示地址
     *
     * @param string|null  $url
     * @return $this
     */
    public function setShowUrl ($url = null)
    {
        $this->detail['show_url'] = $url;

        return $this;
    }

    /**
     * 设置商品类目
     *
     * @param  string|null  $category
     * @return $this
     */
    public function setCategory ($category = null)
    {
        $this->detail['goods_category'] = $category;

        return $this;
    }

    /**
     * 设置商品类目树
     *
     * 从商品类目根节点到叶子节点的类目 id 组成，类目 id 值使用 | 分割
     *
     * @param  array|string  $tree
     * @return $this
     */
    public function setCategoriesTree ($tree = [])
    {
        $this->detail['categories_tree'] = implode('|', (array)$tree);

        return $this;
    }

    /**
     * 获取商品详情数组
     *
     * @return array
     */
    public function toArray ()
    {
        return array_filter($this->detail);
    }
}