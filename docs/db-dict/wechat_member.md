**www_wechat_member**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id             | integer  | 11  | N |   |         | 
|  2 | member_id      | integer  | 11  | Y |   | 会员 id | 
|  3 | subscribe      | tinyint  | 1   | N | 1 | 是否关注 | 
|  4 | openid         | string   | 28  | N |   | openid  | 
|  5 | nickname       | string   | 60  | N |   | 昵称  | 
|  6 | sex            | smallint | 6   | N | 0 | 性别  | 
|  7 | country        | string   | 50  | Y |   | 国家  | 
|  8 | province       | string   | 50  | Y |   | 省份  | 
|  9 | city           | string   | 50  | Y |   | 城市  | 
| 10 | language       | string   | 50  | Y |   | 语言  | 
| 11 | headimgurl     | string   | 200 | Y |   | 头像  | 
| 12 | subscribe_time | integer  | 11  | Y |   | 关注时间 | 
| 13 | unionid        | string   | 29  | Y |   | unionid | 
