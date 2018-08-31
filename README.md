# zgldh/discount-and-coupon
优惠活动、折扣计算器

## 定义
1. discount 折扣活动
2. coupon   优惠券
3. 以上两者在下文统称“benefit 权益”

## 功能
1. 输入“优惠活动(discount)”，“折扣权益(coupon)”和一些货品，即可计算出最终价格。
2. 可以得出每一个货品参与了哪些 权益。
3. 提供各个权益的 before 和 after 回调。可以决定是否应用该权益，以及应用了权益的后续操作。

## 用法

先给个感性认识。
```php
    use zgldh\DiscountAndCoupon\Calculator;

    $calculator = new Calculator();         // 初始化计算器
    $result = $calculator
        ->setDiscounts($discountCollection) // 设置打算应用的 discounts  可选 Optional
        ->setCoupons($couponCollection)     // 设置打算使用的 coupons    可选 Optional
        ->setGoodses()                      // 设置要买的货物            必填
        ->calculate();                      // 开始计算

    $result->original_price;                // 原始总价， 两位小数
    $result->price;                         // 最终价格， 两位小数
    $result->discounts;                     // 实际应用的 discounts
    $result->coupons;                       // 实际应用的 coupons
    foreach($result->goodses as $goods)     // 所有享受到权益的货物数组，每个元素对应一个货物
    {
        $goods->name;                       // 货物名字
        $goods->category;                   // 货物分类
        $goods->original_price;             // 该货物原价
        $goods->price;                      // 该货物最终价格
        $goods->discounts;                  // 该货物应用的 discounts
        $goods->coupons;                    // 该货物应用的 coupons
    }

```

如何定义 discount

```
class
```