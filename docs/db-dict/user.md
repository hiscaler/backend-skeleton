**www_user**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                   | integer  | 11  | N |   |            | 
|  2 | username             | string   | 20  | N |   | 用户名  | 
|  3 | nickname             | string   | 20  | N |   | 昵称     | 
|  4 | avatar               | string   | 100 | Y |   | 头像     | 
|  5 | auth_key             | string   | 32  | N |   | 认证 key | 
|  6 | password_hash        | string   | 255 | N |   | 密码     | 
|  7 | password_reset_token | string   | 255 | Y |   | 密码重置 token | 
|  8 | email                | string   | 50  | Y |   | 邮箱     | 
|  9 | role                 | string   | 64  | Y |   | 角色     | 
| 10 | credits_count        | integer  | 11  | N | 0 | 积分     | 
| 11 | user_group           | string   | 20  | Y |   | 用户组  | 
| 12 | system_group         | string   | 20  | Y |   | 系统组  | 
| 13 | register_ip          | integer  | 11  | N |   | 注册 IP  | 
| 14 | login_count          | integer  | 11  | N | 0 | 登录次数 | 
| 15 | last_login_ip        | integer  | 11  | Y |   | 最后登录 IP | 
| 16 | last_login_time      | integer  | 11  | Y |   | 最后登录时间 | 
| 17 | status               | smallint | 6   | N | 1 | 状态     | 
| 18 | created_at           | integer  | 11  | N |   | 添加时间 | 
| 19 | created_by           | integer  | 11  | N |   | 添加人  | 
| 20 | updated_at           | integer  | 11  | N |   | 更新时间 | 
| 21 | updated_by           | integer  | 11  | N |   | 更新人  | 
