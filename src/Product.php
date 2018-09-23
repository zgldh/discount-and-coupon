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

    /**
     * 货物最终价格
     * @var float
     */
    private $final_price = 0.0;

    /**
     * 在本产品上应用过的 Benefit
     * @var array
     */
    private $appliedBenefits = [];

    public function __construct($data = [])
    {
        $data = json_decode(json_encode($data), true);
        $this->sku = $data['sku'];
        $this->price = floatval($data['price']);
        $this->final_price = $this->price;
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

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed|null
     */
    public function getSku(): mixed
    {
        return $this->sku;
    }

    /**
     * @param mixed|null $sku
     */
    public function setSku(mixed $sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return mixed|null
     */
    public function getCategory(): mixed
    {
        return $this->category;
    }

    /**
     * @param mixed|null $category
     */
    public function setCategory(mixed $category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed|null
     */
    public function getName(): mixed
    {
        return $this->name;
    }

    /**
     * @param mixed|null $name
     */
    public function setName(mixed $name)
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getFinalPrice(): float
    {
        return $this->final_price;
    }

    /**
     * @param float $final_price
     */
    public function setFinalPrice(float $final_price): void
    {
        $this->final_price = $final_price;
    }

    /**
     * @return array
     */
    public function getAppliedBenefits(): array
    {
        return $this->appliedBenefits;
    }

    /**
     * @param array $appliedBenefits
     */
    public function setAppliedBenefits(array $appliedBenefits): void
    {
        $this->appliedBenefits = $appliedBenefits;
    }

    /**
     * 追加一个应用到本产品的 Benefit
     * @param Benefit $benefit
     */
    public function appendAppliedBenefit(Benefit $benefit)
    {
        array_push($this->appliedBenefits, $benefit);
    }

    /**
     * 是否本产品应用过任何 Benefit
     * @return bool
     */
    public function isAppliedBenefit()
    {
        return sizeof($this->getAppliedBenefits()) > 0;
    }
}