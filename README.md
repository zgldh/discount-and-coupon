# zgldh/discount-and-coupon
优惠活动、折扣计算器

## 定义
1. discount 折扣活动，通常用于店铺对所有消费者的活动。
2. coupon   优惠券，通常属于一个特定的消费者的特权。
3. 以上两者在下文统称“benefit 权益”
4. `zgldh\DiscountAndCoupon\Discounts\Discount::class` 是折扣活动的基类，用于定义一系列折扣活动的类型。注意是折扣活动类型而不是实例。
5. `zgldh\DiscountAndCoupon\Coupons\Coupon::class` 是优惠券的基类，同上。
6. `priority` 表示该权益的判断优先级，数值越高越优先判断。

## 功能
1. 输入“折扣活动(discount)”，“优惠券(coupon)”和一些货品，即可计算出最终价格。
2. 可以得出每一个货品参与了哪些 权益。
3. 提供各个权益的 before 和 after 回调。before 可以返回 boolean 值来决定是否应用该权益，after 处理应用了权益的后续操作。

## 用法

先给个感性认识。
```php
    use zgldh\DiscountAndCoupon\Calculator;

    $calculator = new Calculator();         // 初始化计算器
    $result = $calculator
        ->setDiscounts($discountCollection) // 设置打算应用的 discounts  可选 Optional
        ->setCoupons($couponCollection)     // 设置打算使用的 coupons    可选 Optional
        ->setProducts([                      // 设置要买的货物            必填
            [
                'sku'       => 123,         // 货物 SKU 或 ID   必填
                'price'     => 2.80         // 货物原价         必填
                'category'  => 456,         // 货物分类 ID      可选。如不填写，无法参与针对分类的活动。
                'name'      => 'Coca Cola'  // 货物名           可选
                'foo'       => 'bar'        // 其他参数         可选
            ],
            ... // more products
         ])
        ->calculate();                      // 开始计算

    $result->price;                         // 原始总价， 两位小数
    $result->final_price;                   // 最终总价， 两位小数
    $result->benefit;                       // final_price 减去 price
    $result->discounts;                     // 实际应用的 discounts， 内含每个 discount 提供了多少 benefit。
    $result->coupons;                       // 实际应用的 coupons， 内含每个 coupon 提供了多少 benefit。
    foreach($result->products as $product)     // 所有享受到权益的货物数组，每个元素对应一个货物
    {
        $product->sku;                        // 该货物 SKU
        $product->price;                      // 货物原价
        $product->category;                   // 货物分类 ID
        $product->name;                       // 货物名字
        $product->foo;                        // 其他参数
        $product->discounts;                  // 该货物应用的 discounts
        $product->coupons;                    // 该货物应用的 coupons
    }

```

### 如何定义 discount

比如我们想定义店铺的“满减活动”：
```
use zgldh\DiscountAndCoupon\Discounts\Discount;

class FlatDiscountWhenPurchaseAbove extends Discount{
    private $above = null;      // 满多少钱
    private $deduction = null;  // 减多少钱

    private $priority = 100;    // 默认优先级
    private $group = self::class;

    public function beforeApply($product, $products, $fullPrice, $currentFullPrice){
        return $currentFullPrice >= $this->above;
    }

    public function apply($product, $products, $fullPrice, $currentFullPrice){
        return $this->updatePrice($currentFullPrice - $this->deduction);
    }

    public function afterApplied($product, $products, $fullPrice, $currentFullPrice){
    }
}

// 满20减2
$d2a20 = new FlatDiscountWhenPurchaseAbove(['above'=>20,'deduction'=>2,'priority'=>101]);
// 满50减10
$d10a50 = new FlatDiscountWhenPurchaseAbove(['above'=>50,'deduction'=>10,'priority'=>102]);
// 满100减30
$d30a100 = new FlatDiscountWhenPurchaseAbove(['above'=>100,'deduction'=>30,'priority'=>103]);

```