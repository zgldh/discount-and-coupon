<?php

namespace zgldh\DiscountAndCoupon\Test;

use PHPUnit\Framework\TestCase;
use zgldh\DiscountAndCoupon\Calculator;
use zgldh\DiscountAndCoupon\Discounts\FlatDiscountWhenPurchaseExceed;

class BasicTestCase extends TestCase
{
    protected function production($params = [])
    {
        $sku = isset($params['sku']) ? $params['sku'] : uniqid('sku-');
        $params['sku'] = $sku;

        $price = isset($params['price']) ? $params['price'] : rand(0.1, 100);
        $params['price'] = $price;

        if (!isset($params['name'])) {
            $params['name'] = 'pn: ' . $sku;
        }

        $category = isset($params['category']) ? $params['category'] : uniqid('category-');
        $params['category'] = $category;
        return $params;
    }
}
