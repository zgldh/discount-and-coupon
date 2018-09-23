<?php

namespace zgldh\DiscountAndCoupon\Test;

use zgldh\DiscountAndCoupon\Calculator;
use zgldh\DiscountAndCoupon\Discounts\FlatDiscountWhenPurchaseExceed;

class DiscountTest extends BasicTestCase
{
    public function testFlatDiscount()
    {

        // 满20减2
        $d2a20 = new FlatDiscountWhenPurchaseExceed(['above' => 20, 'deduction' => 2, 'priority' => 101]);
        // 满50减10
        $d10a50 = new FlatDiscountWhenPurchaseExceed(['above' => 50, 'deduction' => 10, 'priority' => 102]);
        // 满100减30
        $d30a100 = new FlatDiscountWhenPurchaseExceed(['above' => 100, 'deduction' => 30, 'priority' => 103]);

        $calculator = new Calculator();         // 初始化计算器
        $result = $calculator
            ->setDiscounts([
                $d2a20,
                $d10a50,
                $d30a100
            ])
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 2.8]),
                $this->product(['price' => 20]),
                $this->product(['price' => 31])
            ])
            ->calculate();                      // 开始计算

        $this->assertEquals(2.8 + 20 + 31, $result->getPrice());
    }
}
