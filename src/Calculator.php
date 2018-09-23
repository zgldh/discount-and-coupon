<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 08/30/2018
 * Time: 17:35
 */

namespace zgldh\DiscountAndCoupon;

use zgldh\DiscountAndCoupon\Coupons\Coupon;
use zgldh\DiscountAndCoupon\Coupons\CouponCollection;
use zgldh\DiscountAndCoupon\Discounts\Discount;
use zgldh\DiscountAndCoupon\Discounts\DiscountCollection;

class Calculator
{
    /**
     * @var DiscountCollection
     */
    private $discounts = null;
    /**
     * @var CouponCollection
     */
    private $coupons = null;
    /**
     * Other parameters
     * @var array
     */
    private $parameters = [];

    /**
     * @var ProductCollection
     */
    private $products = null;

    public function __construct()
    {
        $this->discounts = new DiscountCollection();
        $this->coupons = new CouponCollection();
    }

    /**
     * @param Coupon $coupon
     * @return Calculator
     */
    public function setCoupon(Coupon $coupon): Calculator
    {
        $this->coupons = new CouponCollection();
        $this->coupons->appendCoupon($coupon);
        return $this;
    }

    /**
     * @param Discount $discount
     * @return Calculator
     */
    public function setDiscount(Discount $discount): Calculator
    {
        $this->discounts = new DiscountCollection();
        $this->discounts->appendDiscount($discount);
        return $this;
    }

    /**
     * @param CouponCollection|array $couponCollection
     * @return Calculator
     */
    public function setCoupons($couponCollection): Calculator
    {
        $this->coupons = is_a($couponCollection,
            CouponCollection::class) ? $couponCollection : new CouponCollection($couponCollection);
        return $this;
    }

    /**
     * @param DiscountCollection|array $discountCollection
     * @return Calculator
     */
    public function setDiscounts($discountCollection): Calculator
    {
        $this->discounts = is_a($discountCollection,
            DiscountCollection::class) ? $discountCollection : new DiscountCollection($discountCollection);
        return $this;
    }

    /**
     * @param Discount $discount
     * @return Calculator
     */
    public function appendDiscount(Discount $discount): Calculator
    {
        $this->discounts->appendDiscount($discount);
        return $this;
    }

    /**
     * @param Coupon $coupon
     * @return $this
     */
    public function appendCoupon(Coupon $coupon): Calculator
    {
        $this->coupons->appendCoupon($coupon);
        return $this;
    }

    /**
     * @return DiscountCollection
     */
    public function getDiscounts(): DiscountCollection
    {
        return $this->discounts;
    }

    /**
     * @return CouponCollection
     */
    public function getCoupons(): CouponCollection
    {
        return $this->coupons;
    }

    public function setProducts($products): Calculator
    {
        $this->products = new ProductCollection($products);
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return Calculator
     */
    public function setParameter($name, $value): Calculator
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getParameter($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * Calculate and get Result
     * @return Result
     */
    public function calculate(): Result
    {
        $benefits = $this->getBenefits();
        /** @var Benefit $benefit */
        foreach ($benefits as $benefit) {
            $benefit->attempt($this->products);
        }
        return $this->getResult();
    }

    private function getResult()
    {
        $result = new Result();
        $result->setPrice($this->products->getPrice());
        $result->setFinalPrice($this->products->getFinalPrice());
        $result->setDiscounts($this->discounts->getApplied());
        $result->setCoupons($this->coupons->getApplied());
        return $result;
    }

    /**
     * 得到 Discounts 和 Coupon 的合集
     * 并且 priority 大的，排在最前面
     * @return array
     */
    private function getBenefits()
    {
        $benefits = [];
        foreach ($this->discounts as $discount) {
            $benefits[] = $discount;
        }
        foreach ($this->coupons as $coupon) {
            $benefits[] = $coupon;
        }
        usort($benefits, function (Benefit $a, Benefit $b) {
            $priorityA = $a->getPriority();
            $priorityB = $b->getPriority();
            return $priorityB - $priorityA;
        });
        return $benefits;
    }
}