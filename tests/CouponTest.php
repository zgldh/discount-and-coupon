<?php

namespace zgldh\DiscountAndCoupon\Test;

use zgldh\DiscountAndCoupon\Benefit;
use zgldh\DiscountAndCoupon\Calculator;
use zgldh\DiscountAndCoupon\Product;

class CouponTest extends BasicTestCase
{
    public function testFlatDeduction()
    {
        $flatDeduction10 = new FlatDeduction(['deduction' => 10, 'coupon_id' => 123, 'priority' => 8]); // 10元代金券
        $flatDeduction20 = new FlatDeduction(['deduction' => 20, 'coupon_id' => 124, 'priority' => 9]); // 20元代金券
        $flatDeduction50 = new FlatDeduction(['deduction' => 50, 'coupon_id' => 125, 'priority' => 10]); // 50元代金券

        $calculator = new Calculator();         // 初始化计算器
        $result = $calculator
            ->setBenefits([
                $flatDeduction10,
                $flatDeduction20,
                $flatDeduction50,
            ])
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 2.8]),
                $this->product(['price' => 20]),
                $this->product(['price' => 81])
            ])
            ->calculate();                      // 开始计算
        $this->assertEquals(2.8 + 20 + 81 - 50 - 20, $result->getFinalPrice());

        $result = $calculator
            ->setBenefits([
                $flatDeduction10,
            ])
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 2.8]),
                $this->product(['price' => 20]),
                $this->product(['price' => 81])
            ])
            ->calculate();                      // 开始计算
        $this->assertEquals(2.8 + 20 + 81 - 10, $result->getFinalPrice());

        $result = $calculator
            ->setBenefits([
                $flatDeduction50,
            ])
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 2.8]),
                $this->product(['price' => 20]),
                $this->product(['price' => 81])
            ])
            ->calculate();                      // 开始计算
        $this->assertEquals(2.8 + 20 + 81 - 50, $result->getFinalPrice());
    }

    public function testUpgradeBeverage()
    {
        $upgradeBeverage = new UpgradeBeverage();

        $calculator = new Calculator();         // 初始化计算器
        $result = $calculator
            ->setBenefit($upgradeBeverage)
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 5, 'sku' => SMALL_COKE]),
                $this->product(['price' => 7, 'sku' => BIG_COKE]),
                $this->product(['price' => 8, 'sku' => 'bread'])
            ])
            ->calculate();                      // 开始计算

        $products = array_filter($result->getProducts()->getArrayCopy(), function (Product $product) {
            return $product->sku === BIG_COKE;
        });
        $this->assertEquals(5 + 7 + 8, $result->getFinalPrice());
        $this->assertEquals(2, sizeof($products));
    }
}

class FlatDeduction extends Benefit
{
    protected $priority = 0;    // 优先级
    protected $deduction = 0;
    protected $couponId = null;
    protected $groupMaxApplyTime = 2; // 最多可以同时用两张代金券

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return max(0, $scopeTotalPrice - $this->deduction);
    }
}

const SMALL_COKE = 'small-coke';
const BIG_COKE = 'big-coke';

class UpgradeBeverage extends Benefit
{
    protected $priority = 500;    // 优先级

    protected function scope(Product $product)
    {
        return $product->sku === SMALL_COKE;
    }

    protected function newScopeProducts($scopeProducts, $scopeTotalPrice)
    {
        $firstCoke = $scopeProducts[0];
        if ($firstCoke) {
            $firstCoke->sku = BIG_COKE;
            $firstCoke->name = 'Big Coke';
        }
        return $scopeProducts;
    }
}