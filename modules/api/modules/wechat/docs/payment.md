微信支付
=======

## 统一下单
POST /api/wechat/payment/order

### 接口说明
统一下单

### 参数说明
| 参数 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | :---: | :---: | :---: | :---: | --- |
| trade_type | string | 16 | | Y | JSAPI: JSAPI支付</br> NATIVE: Native支付</br> APP: APP支付 |
| body | string | 128 | | | 商品简单描述 |
| detail | string | 6000 | | | 商品详情 |
| attach | string | 127 | | | 附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用。 |
| out_trade_no | string | 32 | | | 商户系统内部订单号，要求32个字符内，只能是数字、大小写字母\_\-\|\* 且在同一个商户号下唯一。</br> *未填写的情况下，将会自动生成一个不重复的订单号* |
| total_fee | int | | | Y  | 订单总金额，单位为分 |
| product_id | string | 32 | | | trade_type=NATIVE时，此参数必传。此参数为二维码中包含的商品ID，商户自行定义。 |
| openid | string | 128 | | | trade_type=JSAPI时（即JSAPI支付），此参数必传，此参数为微信用户在商户对应appid下的唯一标识。 |

## 授权回调
GET /api/wechat/payment/notify

### 接口说明
付款回调业务处理接口

### 参数说明
由微信发起