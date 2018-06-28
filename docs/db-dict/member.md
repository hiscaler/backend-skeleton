**www_member**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                   | integer  | 11  | N |   |            | 
|  2 | category_id          | integer  | 11  | N | 0 | 分类     | 
|  3 | type                 | smallint | 6   | N | 0 | 会员类型 | 
|  4 | username             | string   | 20  | N |   | 帐号     | 
|  5 | nickname             | string   | 60  | N |   | 昵称     | 
|  6 | real_name            | string   | 20  | Y |   | 姓名     | 
|  7 | avatar               | string   | 200 | Y |   | 头像     | 
|  8 | auth_key             | string   | 32  | N |   | 认证 key | 
|  9 | password_hash        | string   | 255 | N |   | 密码     | 
| 10 | password_reset_token | string   | 255 | Y |   | 密码重置 token | 
| 11 | access_token         | string   | 255 | Y |   | 访问 Token | 
| 12 | email                | string   | 50  | Y |   | 邮箱     | 
| 13 | tel                  | string   | 30  | Y |   | 电话号码 | 
| 14 | mobile_phone         | string   | 35  | Y |   | 手机号码 | 
| 15 | address              | string   | 100 | Y |   | 地址     | 
| 16 | register_ip          | integer  | 11  | N |   | 注册 IP  | 
| 17 | login_count          | integer  | 11  | N | 0 | 登录次数 | 
| 18 | total_credits        | integer  | 11  | N | 0 | 总积分  | 
| 19 | available_credits    | integer  | 11  | N | 0 | 可用积分 | 
| 20 | last_login_ip        | integer  | 11  | Y |   | 最后登录 IP | 
| 21 | last_login_time      | integer  | 11  | Y |   | 最后登录时间 | 
| 22 | status               | smallint | 6   | N | 0 | 状态     | 
| 23 | remark               | text     |     | Y |   | 备注     | 
| 24 | created_at           | integer  | 11  | N |   | 添加时间 | 
| 25 | created_by           | integer  | 11  | N |   | 添加人  | 
| 26 | updated_at           | integer  | 11  | N |   | 更新时间 | 
| 27 | updated_by           | integer  | 11  | N |   | 更新人  | 
