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

    public function __construct($input = [], int $flags = 0, string $iterator_class = "ArrayIterator")
    {
        $input = array_map(function ($item) {
            return is_a($item, Product::class) ? $item : new Product($item);
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
        return array_reduce((array)$this, function ($carry, Product $item) {
            return $carry + $item->getFinalPrice();
        }, 0);
    }

    public function appendProducts($products)
    {
        if (is_array($products)) {
            array_walk($products, function (Product $product) {
                $this->append($product);
            });
        }
    }

    public function normalize()
    {
        $arr = array_values($this->getArrayCopy());
        $this->exchangeArray($arr);
    }
}