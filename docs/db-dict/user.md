www_user
========
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                   | integer  | 11  |   |   | 自增主键 | 
|  2 | username             | string   | 20  |   |   | 用户名  | 
|  3 | nickname             | string   | 20  |   |   | 昵称     | 
|  4 | avatar               | string   | 100 | Y |   | 头像     | 
|  5 | auth_key             | string   | 32  |   |   | 认证 key | 
|  6 | password_hash        | string   | 255 |   |   | 密码     | 
|  7 | password_reset_token | string   | 255 | Y |   | 密码重置 token | 
|  8 | email                | string   | 50  | Y |   | 邮箱     | 
|  9 | role                 | string   | 64  | Y |   | 角色     | 
| 10 | register_ip          | integer  | 11  |   |   | 注册 IP  | 
| 11 | login_count          | integer  | 11  |   | 0 | 登录次数 | 
| 12 | last_login_ip        | integer  | 11  | Y |   | 最后登录 IP | 
| 13 | last_login_time      | integer  | 11  | Y |   | 最后登录时间 | 
| 14 | status               | smallint | 6   |   | 1 | 状态     | 
| 15 | created_at           | integer  | 11  |   |   | 添加时间 | 
| 16 | created_by           | integer  | 11  |   |   | 添加人  | 
| 17 | updated_at           | integer  | 11  |   |   | 更新时间 | 
| 18 | updated_by           | integer  | 11  |   |   | 更新人  | 
