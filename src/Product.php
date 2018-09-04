<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 09/03/2018
 * Time: 21:35
 */

namespace zgldh\DiscountAndCoupon;


/**
 * Class Product
 * @package zgldh\DiscountAndCoupon
 */
class Product
{
    /**
     * 货物 SKU 或 ID   必填
     * @var mixed|null
     */
    private $sku = null;
    /**
     * 货物原价         必填
     * @var float
     */
    private $price = 0.0;
    /**
     * 货物分类 ID      可选。如不填写，无法参与针对分类的活动。
     * @var mixed|null
     */
    private $category = null;
    /**
     * 货物名           可选
     * @var mixed|null
     */
    private $name = null;

    /**
     * 其他参数
     * @var array
     */
    private $properties = [];

    public function __construct($data = [])
    {
        $this->sku = $data['sku'];
        $this->price = floatval($data['price']);
        $this->category = isset($data['category']) ? $data['category'] : null;
        $this->name = isset($data['name']) ? $data['name'] : null;

        unset($data['sku'], $data['price'], $data['category'], $data['name']);

        $this->properties = $data;
    }

    public function __get($name)
    {
        return $this->properties[$name];
    }

    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }
}