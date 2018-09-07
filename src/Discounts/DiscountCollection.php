<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 08/30/2018
 * Time: 17:36
 */

namespace zgldh\DiscountAndCoupon\Discounts;


class DiscountCollection extends \ArrayObject
{

    public function appendDiscount(Discount $discount)
    {
    }

    /**
     * @return array
     */
    public function getApplied()
    {
        return (array)$this;
    }
}