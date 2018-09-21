<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 08/30/2018
 * Time: 17:36
 */

namespace zgldh\DiscountAndCoupon;


class ProductCollection extends \ArrayObject
{
    private $originalTotalPrice = 0.0;
    private $currentTotalPrice = 0.0;

    public function __construct($input = array(), int $flags = 0, string $iterator_class = "ArrayIterator")
    {
        $input = array_map(function ($item) {
            return new Product($item);
        }, $input);
        parent::__construct($input, $flags, $iterator_class);
    }

    public function getPrice()
    {
        return array_reduce((array)$this, function ($carry, Product $item) {
            return $carry + $item->getPrice();
        }, 0);
    }

    /**
     * 得到最终总价
     */
    public function getFinalPrice()
    {

    }
}