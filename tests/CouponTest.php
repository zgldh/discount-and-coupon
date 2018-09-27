<?php

namespace zgldh\DiscountAndCoupon\Test;

use zgldh\DiscountAndCoupon\Calculator;
use zgldh\DiscountAndCoupon\Discounts\Discount;
use zgldh\DiscountAndCoupon\Discounts\FlatDiscountWhenPurchaseExceed;
use zgldh\DiscountAndCoupon\Product;

class CouponTest extends BasicTestCase
{
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