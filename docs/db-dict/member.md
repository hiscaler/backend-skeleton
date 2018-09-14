www_member
==========
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                   | integer  | 11  |   |   | 自增主键 | 
|  2 | category_id          | integer  | 11  |   | 0 | 分类     | 
|  3 | type                 | smallint | 6   |   | 0 | 会员类型 | 
|  4 | group                | string   | 20  | Y |   | 分组     | 
|  5 | username             | string   | 20  |   |   | 帐号     | 
|  6 | nickname             | string   | 60  |   |   | 昵称     | 
|  7 | real_name            | string   | 20  | Y |   | 姓名     | 
|  8 | avatar               | string   | 200 | Y |   | 头像     | 
|  9 | auth_key             | string   | 32  |   |   | 认证 key | 
| 10 | password_hash        | string   | 255 |   |   | 密码     | 
| 11 | password_reset_token | string   | 255 | Y |   | 密码重置 token | 
| 12 | access_token         | string   | 255 | Y |   | 访问 Token | 
| 13 | email                | string   | 50  | Y |   | 邮箱     | 
| 14 | tel                  | string   | 30  | Y |   | 电话号码 | 
| 15 | mobile_phone         | string   | 35  | Y |   | 手机号码 | 
| 16 | address              | string   | 100 | Y |   | 地址     | 
| 17 | register_ip          | integer  | 11  |   |   | 注册 IP  | 
| 18 | login_count          | integer  | 11  |   | 0 | 登录次数 | 
| 19 | total_credits        | integer  | 11  |   | 0 | 总积分  | 
| 20 | available_credits    | integer  | 11  |   | 0 | 可用积分 | 
| 21 | last_login_ip        | integer  | 11  | Y |   | 最后登录 IP | 
| 22 | last_login_time      | integer  | 11  | Y |   | 最后登录时间 | 
| 23 | status               | smallint | 6   |   | 0 | 状态     | 
| 24 | remark               | text     |     | Y |   | 备注     | 
| 25 | created_at           | integer  | 11  |   |   | 添加时间 | 
| 26 | created_by           | integer  | 11  |   |   | 添加人  | 
| 27 | updated_at           | integer  | 11  |   |   | 更新时间 | 
| 28 | updated_by           | integer  | 11  |   |   | 更新人  | 
