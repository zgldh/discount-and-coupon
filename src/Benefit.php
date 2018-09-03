<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/03/2018
 * Time: 21:35
 */

namespace zgldh\DiscountAndCoupon;


class Benefit
{
    /**
     * 权益判断优先级。数字越大越优先判断。
     * @var int
     */
    protected $priority = 100;

    /**
     * 权益的组名
     * @var string
     */
    protected $group = null;
    /**
     * 最多允许同一个权益组内的权益一共被应用几次。比如一系列满减活动可以属于同一个权益组，且最多被应用一次。
     * @var int
     */
    protected $groupMaxApplyTime = 1;

    public function getGroup()
    {
        if ($this->group === null) {
            return static::class;
        }
        return $this->group;
    }

    public function updatePrice($newTotalPrice)
    {

    }

    public function updateProductPrice($product, $newPrice)
    {

    }
}