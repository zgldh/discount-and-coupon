<?php

namespace zgldh\DiscountAndCoupon\Test;

use zgldh\DiscountAndCoupon\Calculator;
use zgldh\DiscountAndCoupon\Discounts\Discount;
use zgldh\DiscountAndCoupon\Discounts\FlatDiscountWhenPurchaseExceed;
use zgldh\DiscountAndCoupon\Product;

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
        $this->assertEquals(2.8 + 20 + 31 - 10, $result->getFinalPrice());
    }

    public function test80Discount()
    {
        $discount = new Breakfast80Discount();

        $calculator = new Calculator();         // 初始化计算器
        $result = $calculator
            ->setDiscount($discount)
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 2.8, 'category' => 'breakfast']),
                $this->product(['price' => 20, 'category' => 'breakfast']),
                $this->product(['price' => 31, 'category' => 'drink'])
            ])
            ->calculate();                      // 开始计算

        $this->assertEquals((2.8 + 20) * 0.8 + 31, $result->getFinalPrice());
    }

    public function testBuyOneFreeOne()
    {
        $discount = new BuyOneFreeOne();

        $calculator = new Calculator();         // 初始化计算器
        $result = $calculator
            ->setDiscount($discount)
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 8, 'sku' => 'bread'])
            ])
            ->calculate();                      // 开始计算

        $this->assertEquals(5 + 8, $result->getFinalPrice());

        $result = $calculator
            ->setDiscount($discount)
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 8, 'sku' => 'bread'])
            ])
            ->calculate();                      // 开始计算

        $this->assertEquals(5 + 0 + 8, $result->getFinalPrice());

        $result = $calculator
            ->setDiscount($discount)
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 8, 'sku' => 'bread'])
            ])
            ->calculate();                      // 开始计算

        $this->assertEquals(5 + 5 + 0 + 8, $result->getFinalPrice());
    }

    public function testBuyOneGetOne()
    {
        $discount = new BuyOneGetOne();

        $calculator = new Calculator();         // 初始化计算器
        $result = $calculator
            ->setDiscount($discount)
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 8, 'sku' => 'bread'])
            ])
            ->calculate();                      // 开始计算
        $products = array_filter($result->getProducts()->getArrayCopy(), function (Product $product) {
            return $product->sku === 'yogurt';
        });
        $this->assertEquals(5 + 8, $result->getFinalPrice());
        $this->assertEquals(2, sizeof($products));

        $result = $calculator
            ->setDiscount($discount)
            ->setProducts([                     // 设置要买的货物            必填
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 5, 'sku' => 'yogurt']),
                $this->product(['price' => 8, 'sku' => 'bread'])
            ])
            ->calculate();                      // 开始计算
        $products = array_filter($result->getProducts()->getArrayCopy(), function (Product $product) {
            return $product->sku === 'yogurt';
        });
        $this->assertEquals(5 + 5 + 8, $result->getFinalPrice());
        $this->assertEquals(4, sizeof($products));
    }
}


class Breakfast80Discount extends Discount
{
    protected $priority = 200;    // 优先级

    protected $exclusive = true;  // 不与其他活动同享

    protected function scope(Product $product)
    {
        return $product->category === 'breakfast';
    }

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice * 0.8;
    }
}

class BuyOneFreeOne extends Discount
{
    protected $priority = 500;    // 优先级

    protected $exclusive = true;  // 不与其他活动同享

    protected function scope(Product $product)
    {
        return $product->sku === 'yogurt';
    }

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        $productsCount = count($scopeProducts);
        $fairCount = ceil($productsCount / 2);
        $fairProductPrice = $scopeTotalPrice / $productsCount;
        return $fairProductPrice * $fairCount;
    }
}

class BuyOneGetOne extends Discount
{
    protected $priority = 500;    // 优先级

    protected $exclusive = true;  // 不与其他活动同享

    protected function scope(Product $product)
    {
        return $product->sku === 'yogurt';
    }

    protected function newScopeProducts($scopeProducts, $scopeTotalPrice)
    {
        $count = count($scopeProducts);
        for ($i = 0; $i < $count; $i++) {
            array_push($scopeProducts, new Product([
                'sku'   => 'yogurt',
                'price' => 0
            ]));
        }
        return $scopeProducts;
    }
}