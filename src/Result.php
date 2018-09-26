<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/03/2018
 * Time: 21:35
 */

namespace zgldh\DiscountAndCoupon;


class Result
{
    private $final_price = 0.0;
    private $price = 0.0;
    private $benefit = 0.0;
    private $discounts = [];
    private $coupons = [];
    private $products = null;

    /**
     * @param mixed $final_price
     * @return Result
     */
    public function setFinalPrice($final_price)
    {
        $this->final_price = $final_price;
        $this->benefit = $this->final_price - $this->price;
        return $this;
    }

    /**
     * @param mixed $price
     * @return Result
     */
    public function setPrice($price)
    {
        $this->price = $price;
        $this->benefit = $this->final_price - $this->price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinalPrice()
    {
        return $this->final_price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $benefit
     * @return Result
     */
    public function setBenefit($benefit)
    {
        $this->benefit = $benefit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBenefit()
    {
        return $this->benefit;
    }

    /**
     * @param array $discounts
     * @return Result
     */
    public function setDiscounts(array $discounts): Result
    {
        $this->discounts = $discounts;
        return $this;
    }

    /**
     * @return array
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    /**
     * @param array $coupons
     * @return Result
     */
    public function setCoupons(array $coupons): Result
    {
        $this->coupons = $coupons;
        return $this;
    }

    /**
     * @return array
     */
    public function getCoupons(): array
    {
        return $this->coupons;
    }

    /**
     * @return ProductCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param null $products
     */
    public function setProducts($products)
    {
        $this->products = is_a($products,
            ProductCollection::class) ? $products : new ProductCollection($products);
    }
}