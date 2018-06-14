**www_member**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                   | integer  | 11  | N |   |            | 
|  2 | type                 | smallint | 6   | N | 0 | 会员类型 | 
|  3 | username             | string   | 20  | N |   | 用户名  | 
|  4 | nickname             | string   | 20  | N |   | 昵称     | 
|  5 | avatar               | string   | 200 | Y |   | 头像     | 
|  6 | auth_key             | string   | 32  | N |   | 认证 key | 
|  7 | password_hash        | string   | 255 | N |   | 密码     | 
|  8 | password_reset_token | string   | 255 | Y |   | 密码重置 token | 
|  9 | access_token         | string   | 255 | Y |   | 访问 Token | 
| 10 | email                | string   | 50  | Y |   | 邮箱     | 
| 11 | tel                  | string   | 30  | Y |   | 电话号码 | 
| 12 | mobile_phone         | string   | 35  | Y |   | 手机号码 | 
| 13 | register_ip          | integer  | 11  | N |   | 注册 IP  | 
| 14 | login_count          | integer  | 11  | N | 0 | 登录次数 | 
| 15 | last_login_ip        | integer  | 11  | Y |   | 最后登录 IP | 
| 16 | last_login_time      | integer  | 11  | Y |   | 最后登录时间 | 
| 17 | status               | smallint | 6   | N | 0 | 状态     | 
| 18 | remark               | text     |     | Y |   | 备注     | 
| 19 | created_at           | integer  | 11  | N |   | 添加时间 | 
| 20 | created_by           | integer  | 11  | N |   | 添加人  | 
| 21 | updated_at           | integer  | 11  | N |   | 更新时间 | 
| 22 | updated_by           | integer  | 11  | N |   | 更新人  | 
