内置接口说明
===========
> 接口返回统一格式为
> ```json
> {
>     success: true,
>     data: {
>         ...
>     }
> }
> ```
>
> 或者
>
> ```json
> {
>     success: false,
>     error: {
>         message: "ERROR MESSAGE."
>     }
> }
> ```
>
> 在客户端，我们总可以判断返回数据中的 success 来作为数据是否成功返回的依据。以便进行下一步的操作。
>
> 需要认证的接口控制器请继承于 app\modules\api\extensions\AuthController 控制器。

## GET /api/category
### 说明
分类数据获取接口
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| sign | null/string | 否 | 无 | 后台设置的标记字符 |
| level | integer | 否 | 0 | 返回分类数据的层级（0表示所有层级） |
| flat | boolean | 否 | true | 为 false 返回平级数据，true 则返回 children 为子项目键的数据 |
### 返回格式
```json
{
    success: true,
    data: {
        items: [
            {
                "id": 1,
                "alias": "apple",
                "name": "苹果",
                "icon": null,
                "parent": 0,
                "level": 0
            },
            ...
        ]
    }
}
```

## GET /api/lookup?name=:name
### 说明
常规设定项目取值
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| name | string | 是 | null | 常规设定的键名 |

## GET /api/wechat/auth
### 说明
微信公众号认证
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| redirectUri | string | 是 | null | 回调地址 |
### 返回值
无

## GET /api/wechat/jssdk
### 说明
微信 JSSDK 数据
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| url | string | 否 | null | 当前页面地址 |
| apis | string | 否 | null | 激活的 api 接口，比如 checkJsApi, onMenuShareTimeline, onMenuShareAppMessage, onMenuShareQQ, onMenuShareWeibo, onMenuShareQZone 等 |
### 返回值
```javascript
{
    "appId": "公众号的唯一标识",
    "timestamp": "生成签名的时间戳",
    "nonceStr": "生成签名的随机串",
    "signature": "签名",
    jsApiList: ['需要使用的JS接口列表']
}
```

## GET /api/wxapp/login
### 说明
微信小程序认证
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| code | string | 是 | null | 小程序 code 值 |
| info | json | 是 | null | 获取的微信用户信息 |
### 参考资料
https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxloginobject
### 返回值
```json
{
    "sessionKey": "session value",
    "openid": 'wechat openid'
}
```

## GET /api/wxapp/check-session
### 说明
微信小程序 session 检测
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| session | string | 是 | null | login 接口获得的 session 值 |
### 参考资料
https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxchecksessionobject
### 返回值
```json
{
    "valid": [true|false]
}
```

## GET /api/passport/login
### 说明
会员登录，登录成功后其他需要认证的请求 url 带上 accessToken 参数
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| username | string | 是 | null | 用户名 |
| password | string | 是 | null | 密码 |
### 返回值
```json
{
    "id": 1,
    "username": 'username',
    "accessToken": 'accessToken',
    ...
}
```

## GET /api/passport/logout
### 说明
注销登录

### 参数说明
无

### 返回值
```json
{
    "data": [true|false]
}
```