微信
===

## 微信公众号认证
GET /api/wx/auth

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| redirectUri | string | 是 | null | 回调地址 |
### 返回值
无

## 微信 JSSDK 数据
GET /api/wx/jssdk

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