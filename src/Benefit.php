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
     * 是否应用了本权益
     * @var bool
     */
    private $isApplied = false;

    /**
     * 如果应用了，本权益让价格改变了多少。 一般来说是负数，负数越小说明消费者越实惠。
     * @var float
     */
    private $benefit = 0.0;


    public function __construct($parameters = [])
    {
        foreach ($parameters as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    public function getGroup()
    {
        if ($this->group === null) {
            return static::class;
        }
        return $this->group;
    }

    /**
     * @return bool
     */
    public function isApplied(): bool
    {
        return $this->isApplied;
    }

    /**
     * @param bool $isApplied
     */
    public function setIsApplied(bool $isApplied)
    {
        $this->isApplied = $isApplied;
    }

    /**
     * @return int
     */
    public function getBenefit(): float
    {
        return $this->benefit;
    }

    /**
     * @param float $benefit
     */
    public function setBenefit(float $benefit)
    {
        $this->benefit = $benefit;
    }

    /**
     * 判断一个商品是否符合本权益条件。 如 SKU， Category， 商品单价是否符合等。
     * 如果符合，则该商品被加入本权益的 scope，进行下一步 isScopeQualified 更详细的判断
     * 请在子类重写本函数
     * @param $product
     * @return bool
     */
    protected function scope($product)
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
    protected function isScopeQualified($scopeProducts, $scopeTotalPrice)
    {
        return true;
    }

    /**
     * 返回改变过的 scope 商品集合。 用于某些会增加、删除、修改商品的权益。
     * @param $scopeProductions
     * @param $scopeTotalPrice
     * @return mixed
     */
    protected function newScopeProductions($scopeProductions, $scopeTotalPrice)
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
    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice;
    }

    /**
     * 当本权益真的被应用时调用。
     * 请在子类重写本函数
     * @param $scopeProducts
     * @param $newScopeTotalPrice
     */
    protected function onApplied($scopeProducts, $newScopeTotalPrice)
    {

    }

    /**
     * 当前 Benefit 比传入的 Benefit 更优先
     * @param Benefit $benefit
     * @return bool
     */
    public function isPriorThan(Benefit $benefit)
    {
        return $this->getPriority() > $benefit->getPriority();
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * TODO 尝试将本权益应用在商品集合 $products 上
     * @param ProductCollection $products
     * @return bool true为应用了这个权益
     */
    public function attempt(ProductCollection $products)
    {
        // 找出范围内的商品集合
        $scopeProducts = array_filter($products->getArrayCopy(), [$this, 'scope']);
        if (sizeof($scopeProducts) === 0) {
            return false;
        }
        
        $scopeTotalPrice = $this->sumTotalPrice($scopeProducts);

        // 判断该商品集合是否符合条件
        if (!$this->isScopeQualified($scopeProducts, $scopeTotalPrice)) {
            return false;
        }
        $this->setIsApplied(true);

        // 返回改变过的 scope 商品集合。 用于某些会增加、删除、修改商品的权益。
        $scopeProducts = $this->newScopeProductions($scopeProducts, $scopeTotalPrice);

        // 返回 scope 应用本权益后的新总价
        $newScopeTotalPrice = $this->newScopePrice($scopeProducts, $scopeTotalPrice);

        $this->setBenefit($newScopeTotalPrice - $scopeTotalPrice);

        $this->onApplied($scopeProducts, $newScopeTotalPrice);

        return true;
    }

    /**
     * 计算产品总价
     * @param $products
     * @return mixed
     */
    private function sumTotalPrice(array $products)
    {
        $sum = array_reduce($products, function ($pre, Product $product) {
            return $pre + $product->getPrice();
        }, 0);
        return $sum;
    }
}