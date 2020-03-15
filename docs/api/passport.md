用户认证
=======

## 注册
POST /api/passport/register

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| scene | string | 是 | account | 注册方式（account: 帐号注册，mobile-phone: 手机注册，sms: 手机短信注册） |
| username | string | 否 | null | 用户名（注册方式为 account 时必填） |
| password | string | 否 | null | 密码（注册方式为 account 时必填） |
| confirm_password | string | 否 | null | 密码（注册方式为 account 时必填） |
| mobile_phone | string | 否 | null | 手机号码（注册方式为 mobile-phone 时必填） |
| captcha | string | 否 | null | 验证码（注册方式为 sms 时必填） |
| type | int | 是 | null | 会员类型 |

## 登录
POST /api/passport/login

### 说明
登录成功后其他需要认证的请求 url 带上 access_token 参数

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| scene | string | 是 | account | 类型(account: 帐号登录，mobile-phone: 手机登录，sms: 手机短信登录，access_token: 令牌登录) |
| username | string | 否 | null | 用户名（scene 为 account 时必填） |
| password | string | 否 | null | 密码（scene 为 account、mobile_phone 时必填） |
| mobile_phone | string | 否 | null | 手机号码（scene 为 mobile_phone、captcha 时必填） |
| captcha | string | 否 | null | 验证码（scene 为 sms 时必填） |
| access_token | string | 否 | null | 令牌（scene 为 access_token 时必填） |

### 返回值
```json
{
    "id": 1,
    "username": "username",
    "access_token": "accessToken",
    ...
}
```

## 注销登录
GET /api/passport/logout

### 参数说明
无

### 返回值
```json
{
    "data": [true|false]
}
```

##修改密码
POST /api/passport/change-password

### 说明
修改密码需要认证的请求 url 带上 access_token 参数

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| username | string | 是 | null | 用户名 |
| old_password | string | 是 | null | 旧密码 |
| password | string | 是 | null | 新密码 |
| confirm_password | string | 是 | null | 确认密码 |