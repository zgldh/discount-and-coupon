<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/03/2018
 * Time: 21:35
 */

namespace zgldh\DiscountAndCoupon;


class Benefit
{
    /**
     * 权益判断优先级。数字越大越优先判断。
     * @var int
     */
    protected $priority = 100;

    /**
     * true： 只能应用在“干净”的商品上，也就是如果一个商品已经应用了比本权益优先级更高的权益，则本权益自动跳过。
     * false： 默认无上述限制
     * @var bool
     */
    protected $exclusive = false;

    /**
     * 权益的组名
     * @var string
     */
    protected $group = null;

    /**
     * 最多允许同一个权益组内的权益一共被应用几次。比如一系列满减活动可以属于同一个权益组，且最多被应用一次。
     * @var int
     */
    protected $groupMaxApplyTime = 1;

    /**
     * @var Calculator
     */
    private $calculator = null;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getGroup()
    {
        if ($this->group === null) {
            return static::class;
        }
        return $this->group;
    }

    /**
     * 判断一个商品是否符合本权益条件。 如 SKU， Category， 商品单价是否符合等。
     * 如果符合，则该商品被加入本权益的 scope，进行下一步 isScopeQualified 更详细的判断
     * 请在子类重写本函数
     * @param $product
     * @return bool
     */
    public function scope($product)
    {
        return true;
    }

    /**
     * 判断 scope 是否符合本权益条件。 如总价是否符合，是否有包含的商品组合等等。
     * 请在子类重写本函数
     * @param $scopeProducts
     * @param $scopeTotalPrice
     * @return bool
     */
    public function isScopeQualified($scopeProducts, $scopeTotalPrice)
    {
        return true;
    }

    /**
     * 返回改变过的 scope 商品集合。 用于某些会增加、删除、修改商品的权益。
     * @param $scopeProductions
     * @param $scopeTotalPrice
     * @return mixed
     */
    public function newScopeProductions($scopeProductions, $scopeTotalPrice)
    {
        return $scopeProductions;
    }

    /**
     * 返回 scope 应用本权益后的新总价
     * 请在子类重写本函数
     * @param $scopeProducts
     * @param $scopeTotalPrice
     * @return mixed
     */
    public function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice;
    }

    /**
     * 当本权益真的被应用时调用。
     * 请在子类重写本函数
     * @param $scopeProducts
     */
    public function onApplied($scopeProducts)
    {

    }
}