www_member
==========
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                   | integer  | 11  |   |   | 自增主键   | 
|  2 | category_id          | integer  | 11  |   | 0 | 分类         | 
|  3 | type                 | smallint | 6   |   | 0 | 会员类型   | 
|  4 | group                | string   | 20  | Y |   | 分组         | 
|  5 | invitation_code      | string   | 16  |   |   | 邀请码      | 
|  6 | parent_id            | integer  | 11  |   | 0 | 上级         | 
|  7 | username             | string   | 20  |   |   | 帐号         | 
|  8 | nickname             | string   | 60  |   |   | 昵称         | 
|  9 | real_name            | string   | 20  | Y |   | 姓名         | 
| 10 | avatar               | string   | 200 | Y |   | 头像         | 
| 11 | auth_key             | string   | 32  |   |   | 认证 key     | 
| 12 | password_hash        | string   | 255 |   |   | 密码         | 
| 13 | password_reset_token | string   | 255 | Y |   | 密码重置 token | 
| 14 | access_token         | string   | 255 | Y |   | 访问 Token   | 
| 15 | email                | string   | 50  | Y |   | 邮箱         | 
| 16 | mobile_phone         | string   | 35  | Y |   | 手机号码   | 
| 17 | register_ip          | string   | 39  |   |   | 注册 IP      | 
| 18 | login_count          | integer  | 11  |   | 0 | 登录次数   | 
| 19 | total_credits        | integer  | 11  |   | 0 | 总积分      | 
| 20 | available_credits    | integer  | 11  |   | 0 | 可用积分   | 
| 21 | last_login_ip        | string   | 39  | Y |   | 最后登录 IP | 
| 22 | last_login_time      | integer  | 11  | Y |   | 最后登录时间 | 
| 23 | last_login_session   | string   | 128 | Y |   | 最后登录 session 值 | 
| 24 | expired_datetime     | integer  | 11  | Y |   | 有效期      | 
| 25 | status               | smallint | 6   |   | 0 | 状态         | 
| 26 | remark               | text     |     | Y |   | 备注         | 
| 27 | created_at           | integer  | 11  |   |   | 添加时间   | 
| 28 | created_by           | integer  | 11  |   |   | 添加人      | 
| 29 | updated_at           | integer  | 11  |   |   | 更新时间   | 
| 30 | updated_by           | integer  | 11  |   |   | 更新人      | 
