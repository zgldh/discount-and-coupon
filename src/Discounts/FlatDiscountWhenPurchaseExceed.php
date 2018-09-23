<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 08/30/2018
 * Time: 17:20
 */

namespace zgldh\DiscountAndCoupon\Discounts;


class FlatDiscountWhenPurchaseExceed extends Discount
{
    protected $above = null;      // 满多少钱
    protected $deduction = null;  // 减多少钱

    protected $priority = 100;    // 默认优先级

    protected $exclusive = true;

    protected function isScopeQualified($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice >= $this->above;
    }

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice - $this->deduction;
    }
}