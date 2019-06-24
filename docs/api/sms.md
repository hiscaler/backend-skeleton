短信发送
=======

## 发送短信
POST /api/sms/send

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| type | string | 是 |  | 短信类型 |
| mobile_phone | string | 是 |  | 手机号码 |
| content | string | 否 |  | 发送内容 |

### 说明
在提供短息发送接口前，您需要设置 config/sms.php 配置文件中的相关项
```php
'private' => [
    'business' => [
        // 验证码发送处理
        'captcha' => \app\business\SmsCaptchaBusiness::class
        // 订单通知短信发送
        'orderNotice' => \app\business\SmsOrderNoticeBusiness::class
    ],
],
...
```
如上设置，比如您当前需要发送验证码短信，您需要在发送参数的时候，将 `type` 参数设置为 `captcha`，这样后端方可正确的调用您要处理的业务逻辑类。