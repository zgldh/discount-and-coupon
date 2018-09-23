<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 08/30/2018
 * Time: 17:36
 */

namespace zgldh\DiscountAndCoupon\Coupons;

use zgldh\DiscountAndCoupon\BenefitCollection;

class CouponCollection extends BenefitCollection
{

    public function appendCoupon(Coupon $coupon)
    {
        return parent::appendBenefit($coupon);
    }
}