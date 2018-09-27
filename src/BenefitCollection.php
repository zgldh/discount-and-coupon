<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/23/2018
 * Time: 17:36
 */

namespace zgldh\DiscountAndCoupon;

class BenefitCollection extends \ArrayObject
{

    public function appendBenefit(Benefit $benefit)
    {
        $this->append($benefit);
    }

    /**
     * @return array
     */
    public function getApplied()
    {
        return array_filter((array)$this, function (Benefit $benefit) {
            return $benefit->isApplied();
        });
    }
}