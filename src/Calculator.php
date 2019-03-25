<?php
/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 08/30/2018
 * Time: 17:35
 */

namespace zgldh\DiscountAndCoupon;

class Calculator
{
    /**
     * @var BenefitCollection
     */
    private $benefits = null;

    /**
     * Other parameters
     * @var array
     */
    private $parameters = [];

    /**
     * @var ProductCollection
     */
    private $products = null;

    /*
     * Handler functions before attempt
     * @var array
     * */
    private $beforeAttemptHandlers = [];

    /*
     * Handler functions after attempt
     * @var array
     * */
    private $afterAttemptHandlers = [];

    public function __construct()
    {
        $this->benefits = new BenefitCollection();
    }

    /**
     * @param Benefit $benefit
     * @return Calculator
     */
    public function setBenefit(Benefit $benefit): Calculator
    {
        $this->benefits = new BenefitCollection();
        $this->benefits->appendBenefit($benefit);
        return $this;
    }

    /**
     * @param BenefitCollection|array $benefitCollection
     * @return Calculator
     */
    public function setBenefits($benefitCollection): Calculator
    {
        $this->benefits = is_a($benefitCollection,
            BenefitCollection::class) ? $benefitCollection : new BenefitCollection($benefitCollection);
        return $this;
    }

    /**
     * @param Benefit $benefit
     * @return Calculator
     */
    public function appendBenefit(Benefit $benefit): Calculator
    {
        $this->benefits->appendBenefit($benefit);
        return $this;
    }

    /**
     * @return BenefitCollection
     */
    public function getBenefits(): BenefitCollection
    {
        return $this->benefits;
    }

    /**
     * @param ProductCollection
     * @return Calculator
     */
    public function setProducts($products): Calculator
    {
        if (is_a($products, ProductCollection::class)) {
            $this->products = $products;
        } else {
            $this->products = new ProductCollection($products);
        }
        return $this;
    }

    /**
     * @return ProductCollection
     */
    public function getProducts(): ProductCollection
    {
        return $this->products;
    }

    /**
     * @param $name
     * @param $value
     * @return Calculator
     */
    public function setParameter($name, $value): Calculator
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getParameter($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * Calculate and get Result
     * @return Result
     */
    public function calculate(): Result
    {
        $benefits = $this->getOrderedBenefits();
        /** @var Benefit $benefit */
        foreach ($benefits as $benefitIndex=>$benefit) {
            $this->beforeAttempt($benefitIndex, $benefit);
            $attemptResult = $benefit->attempt($this->products);
            $this->afterAttempt($benefitIndex, $benefit, $attemptResult);
            $this->products->normalize();
        }
        return $this->getResult();
    }

    private function getResult()
    {
        $result = new Result();
        $result->setPrice($this->products->getPrice());
        $result->setFinalPrice($this->products->getFinalPrice());
        $result->setBenefits($this->benefits->getApplied());
        $result->setProducts($this->products->getArrayCopy());
        return $result;
    }

    /**
     * 得到 Discounts 和 Coupon 的合集
     * 并且 priority 大的，排在最前面
     * @return array
     */
    private function getOrderedBenefits()
    {
        $benefits = $this->getBenefits()->getArrayCopy();
        usort($benefits, function (Benefit $a, Benefit $b) {
            $priorityA = $a->getPriority();
            $priorityB = $b->getPriority();
            return $priorityB - $priorityA;
        });
        return $benefits;
    }

    private function beforeAttempt($benefitIndex, $benefit)
    {
        foreach($this->beforeAttemptHandlers as $handle)
        {
            if(is_callable($handle))
            {
                $handle($this, $benefitIndex, $benefit);
            }
        }
    }

    private function afterAttempt($benefitIndex, $benefit, $attemptResult)
    {
        foreach($this->afterAttemptHandlers as $handle)
        {
            if(is_callable($handle))
            {
                $handle($this, $benefitIndex, $benefit,$attemptResult);
            }
        }
    }

    /**
     * @return array
     */
    public function getBeforeAttemptHandlers(): array
    {
        return $this->beforeAttemptHandlers;
    }

    /**
     * @param $beforeAttemptHandler
     */
    public function addBeforeAttemptHandler($beforeAttemptHandler)
    {
        $this->beforeAttemptHandlers[] = $beforeAttemptHandler;
    }

    /**
     * @return void
     */
    public function cleanBeforeAttemptHandlers()
    {
        $this->beforeAttemptHandlers = [];
    }

    /**
     * @return array
     */
    public function getAfterAttemptHandlers(): array
    {
        return $this->afterAttemptHandlers;
    }

    /**
     * @param $afterAttemptHandler
     */
    public function addAfterAttemptHandler($afterAttemptHandler)
    {
        $this->afterAttemptHandlers[] = $afterAttemptHandler;
    }
    
    /**
     * @return void
     */
    public function cleanAfterAttemptHandlers()
    {
        $this->beforeAttemptHandlers = [];
    }
}
