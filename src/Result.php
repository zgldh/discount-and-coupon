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
    private $profit = 0.0;
    private $benefits = [];
    private $products = null;

    /**
     * @param mixed $final_price
     * @return Result
     */
    public function setFinalPrice($final_price)
    {
        $this->final_price = $final_price;
        $this->profit = $this->final_price - $this->price;
        return $this;
    }

    /**
     * @param mixed $price
     * @return Result
     */
    public function setPrice($price)
    {
        $this->price = $price;
        $this->profit = $this->final_price - $this->price;
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
     * @param mixed $profit
     * @return Result
     */
    public function setProfit($profit)
    {
        $this->profit = $profit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * @param array $benefits
     * @return Result
     */
    public function setBenefits(array $benefits): Result
    {
        $this->benefits = $benefits;
        return $this;
    }

    /**
     * @return array
     */
    public function getBenefits(): array
    {
        return $this->benefits;
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