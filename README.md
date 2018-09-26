# zgldh/discount-and-coupon
优惠活动、折扣计算器

[![Build Status](https://travis-ci.com/zgldh/discount-and-coupon.svg?branch=master)](https://travis-ci.com/zgldh/discount-and-coupon)

## 定义
1. discount 折扣活动，通常用于店铺对所有消费者的活动。
2. coupon   优惠券，通常属于一个特定的消费者的特权。
3. 以上两者在下文统称“benefit 权益”
4. `zgldh\DiscountAndCoupon\Discounts\Discount::class` 是折扣活动的基类，用于定义一系列折扣活动的类型。注意是折扣活动类型而不是实例。
5. `zgldh\DiscountAndCoupon\Coupons\Coupon::class` 是优惠券的基类，同上。
6. `priority` 表示该权益的判断优先级，数值越高越优先判断。

## 功能
1. 输入“折扣活动(discount)”，“优惠券(coupon)”和一些货品，即可计算出最终价格。
2. TODO 可以得出每一个货品参与了哪些 权益。
3. TODO 提供各个权益的 before 和 after 回调。before 可以返回 boolean 值来决定是否应用该权益，after 处理应用了权益的后续操作。

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
                'name'      => 'Coke Cola'  // 货物名           可选
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
// TODO
//    foreach($result->products as $product)    // 所有享受到权益的货物数组，每个元素对应一个货物
//    {
//        $product->sku;                        // 该货物 SKU
//        $product->price;                      // 货物原价
//        $product->category;                   // 货物分类 ID
//        $product->name;                       // 货物名字
//        $product->foo;                        // 其他参数
//        $product->discounts;                  // 该货物应用的 discounts
//        $product->coupons;                    // 该货物应用的 coupons
//    }

```

### 如何定义店铺折扣活动 Discount

#### 1. 定义店铺的“满减活动”：
```
use zgldh\DiscountAndCoupon\Discounts\Discount;

class FlatDiscountWhenPurchaseExceed extends Discount{
    private $above = null;      // 满多少钱
    private $deduction = null;  // 减多少钱

    protected $priority = 100;    // 默认优先级

    protected function isScopeQualified($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice >= $this->above;
    }

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice - $this->deduction;
    }
}

// 满20减2
$d2a20 = new FlatDiscountWhenPurchaseExceed(['above'=>20,'deduction'=>2,'priority'=>101]);
// 满50减10
$d10a50 = new FlatDiscountWhenPurchaseExceed(['above'=>50,'deduction'=>10,'priority'=>102]);
// 满100减30
$d30a100 = new FlatDiscountWhenPurchaseExceed(['above'=>100,'deduction'=>30,'priority'=>103]);
```

上面我们定义了 `FlatDiscountWhenPurchaseExceed` 类，用于描述“满减”这一类活动。

然后我们新建了 3 个该类的对象，分别代表满20减2, 满50减10, 满100减30 这三种活动。

注意 `priority` 属性， 它决定了 `Calculator` 在尝试应用折扣时的判断顺序。由于默认以上三种活动同属于“满减”活动，且只能应用其中一种(请参考 `Benefit` 类的源码中对 `$group` 的说明)。所以我们让优惠额度最大的优先级最高，以便总是能帮客户享用最大的优惠力度。

#### 2. 定义店铺的“迎中秋早餐8折”活动，不与其他活动同享：

```
use zgldh\DiscountAndCoupon\Discounts\Discount;

const CATEGORY_BREAKFAST = 'breakfast';

class MidAutumnDayBreakfast80Discount extends Discount{
    protected $priority = 200;    // 优先级

    protected $exclusive = true;  // 不与其他活动同享

    protected function scope(Product $product){
        return $product->category === CATEGORY_BREAKFAST;
    }

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice * 0.8;
    }
}

$midAutumnDayDiscountEvent = new MidAutumnDayBreakfast80Discount();
```

#### 3. 定义“新品促销，XX酸奶买一送一” 活动，不与其他活动同享。

下列代码是以减价实现本促销活动，会影响本次订单最终价格。

```
use zgldh\DiscountAndCoupon\Discounts\Discount;

const SKU_YOGURT = 'yogurt';

class YogurtBuyOneGetOne extends Discount{
    protected $priority = 500;    // 优先级

    protected $exclusive = true;  // 不与其他活动同享

    protected function scope(Product $product){
        return $product->sku === SKU_YOGURT;
    }

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        $productsCount = count($scopeProducts);
        $fairCount = ceil($productsCount / 2);
        $fairProductPrice = $scopeTotalPrice / $productsCount;
        return $fairProductPrice * $fairCount;
    }
}

$yogurtPromotion = new YogurtBuyOneGetOne();
```

下列代码是以赠送货品实现促销活动，不会影响本次订单最终价格，但会增加商品。

```
use zgldh\DiscountAndCoupon\Discounts\Discount;
use zgldh\DiscountAndCoupon\Product;

const SKU_YOGURT = 'yogurt';

class YogurtBuyOneGetOne extends Discount{
    protected $priority = 500;    // 优先级

    protected $exclusive = true;  // 不与其他活动同享

    protected function scope(Product $product){
        return $product->sku === SKU_YOGURT;
    }

    protected function newScopeProducts($scopeProducts, $scopeTotalPrice)
    {
        $count = count($scopeProducts);
        for($i = 0; $i<$count; $i++)
        {
            array_push($scopeProducts, new Product([
                'sku'=> SKU_YOGURT,
                'price'=>0
            ]));
        }
        return $scopeProducts;
    }
}

$yogurtPromotion = new YogurtBuyOneGetOne();
```

### 如何定义优惠券 Coupon

#### 1. 定义代金券

```
use zgldh\DiscountAndCoupon\Coupons\Coupon;

class FlatDeduction extends Coupon{
    protected $priority = 0;    // 优先级

    private $deduction = 0;
    private $couponId = null;

    protected function newScopePrice($scopeProducts, $scopeTotalPrice)
    {
        return $scopeTotalPrice - $this->deduction;
    }

    protected function onApplied($scopeProducts)
    {
        // 如果这个 coupon 只能用一次，则删除这个 coupon 记录
        // 如 DB::table('coupons')->where('id',$this->couponId)->delete();
        // 或者修改 coupon 记录的剩余使用次数等等
    }
}

$flatDeduction10= new FlatDeduction([ 'deduction'=>10, 'coupon_id'=>123 ]); // 10元代金券
$flatDeduction20= new FlatDeduction([ 'deduction'=>20, 'coupon_id'=>124 ]); // 20元代金券
$flatDeduction50= new FlatDeduction([ 'deduction'=>50, 'coupon_id'=>125 ]); // 50元代金券
```

#### 2. 定义 “饮料升级优惠券”

能把订单内的某种饮料的一件商品替换成更贵的饮料。 本质是替换商品，所以你可以任意定义规则。

```
use zgldh\DiscountAndCoupon\Coupons\Coupon;

const SMALL_COKE = 'small-coke';
const BIG_COKE = 'big-coke';

class UpgradeBeverage extends Coupon{
    protected $priority = 500;    // 优先级

    protected function scope(Product $product){
        return $product->sku === SMALL_COKE;
    }

    protected function newScopeProducts($scopeProducts, $scopeTotalPrice)
    {
        $firstCoke = $scopeProducts[0];
        if($firstCoke)
        {
            $firstCoke['sku'] = BIG_COKE;
            $firstCoke['name'] = 'Big Coke';
        }
        return $scopeProducts;
    }

    protected function onApplied($scopeProducts)
    {
        // 如果这个 coupon 只能用一次，则删除这个 coupon 记录
        // 如 DB::table('coupons')->where('id',$this->couponId)->delete();
        // 或者修改 coupon 记录的剩余使用次数等等
    }
}
```

