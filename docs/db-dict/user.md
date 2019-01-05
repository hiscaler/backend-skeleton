www_user
========
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                   | integer  | 11  |   |   | 自增主键   | 
|  2 | username             | string   | 20  |   |   | 用户名      | 
|  3 | nickname             | string   | 20  |   |   | 昵称         | 
|  4 | avatar               | string   | 100 | Y |   | 头像         | 
|  5 | auth_key             | string   | 32  |   |   | 认证 key     | 
|  6 | password_hash        | string   | 255 |   |   | 密码         | 
|  7 | password_reset_token | string   | 255 | Y |   | 密码重置 token | 
|  8 | access_token         | string   | 255 |   |   | 访问 Token   | 
|  9 | email                | string   | 50  | Y |   | 邮箱         | 
| 10 | role                 | string   | 64  | Y |   | 角色         | 
| 11 | register_ip          | string   | 39  |   |   | 注册 IP      | 
| 12 | login_count          | integer  | 11  |   | 0 | 登录次数   | 
| 13 | last_login_ip        | string   | 39  | Y |   | 最后登录 IP | 
| 14 | last_login_time      | integer  | 11  | Y |   | 最后登录时间 | 
| 15 | last_login_session   | string   | 128 | Y |   | 最后登录 session 值 | 
| 16 | status               | smallint | 6   |   | 1 | 状态         | 
| 17 | created_at           | integer  | 11  |   |   | 添加时间   | 
| 18 | created_by           | integer  | 11  |   |   | 添加人      | 
| 19 | updated_at           | integer  | 11  |   |   | 更新时间   | 
| 20 | updated_by           | integer  | 11  |   |   | 更新人      | 
