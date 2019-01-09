微信小程序
========

## 微信小程序认证
GET /api/wxapp/login

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

## 微信小程序 session 检测
GET /api/wxapp/check-session

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