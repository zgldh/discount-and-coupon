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
        parent::__construct($input, $flags, $iterator_class);
    }
}