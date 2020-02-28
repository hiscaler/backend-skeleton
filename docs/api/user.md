系统用户
=======

## 列表
GET /api/user/index

## 注册
POST /api/user/create

### <span id="params">参数</span>
| 参数 | 键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | --- | :---: | :---: | :---: | :---: | --- |
| 用户名 | username | string | | | Y ||
| 密码 | password | string | | | Y ||
| 确认密码 | confirm_password | string | | | Y ||
| 昵称 | nickname | string | | |||
| 头像 | avatar | file | | |||
| EMail | email | string | | | ||

## 登录
POST /api/user/login

### 参数
| 参数 | 键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | --- | :---: | :---: | :---: | :---: | --- |
| 用户名 | username | string | | | Y ||
| 密码 | password | string | | | Y ||

## 修改密码
POST /api/user/change-password?access_token=:tokenValue

### 参数
| 参数 | 键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | --- | :---: | :---: | :---: | :---: | --- |
| 旧密码 | old_password | string | | | Y ||
| 新密码 | password | string | | | Y ||
| 确认密码 | confirm_password | string | | | Y ||

## 更新
PUT|PATCH /api/user/update?id=:id

## 删除
DELETE /api/user/delete?id=:id

## 详情
GET /api/user/view?id=:id