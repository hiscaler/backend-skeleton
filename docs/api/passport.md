用户认证
=======
## 注册
GET /api/passport/register

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| username | string | 是 | null | 用户名 |
| password | string | 是 | null | 密码 |
| confirm_password | string | 是 | null | 密码 |
| type | int | 是 | null | 会员类型 |
| mobile_phone | string | 是 | null | 手机号码 |

## 登录
GET /api/passport/login

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