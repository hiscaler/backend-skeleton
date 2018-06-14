**www_user_group**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id          | integer  | 11  | N |   |      | 
|  2 | type        | smallint | 6   | N |   | 分组类型 | 
|  3 | alias       | string   | 20  | N |   | 别名 | 
|  4 | name        | string   | 30  | N |   | 组头衔 | 
|  5 | icon_path   | string   | 100 | Y |   | 组图标 | 
|  6 | min_credits | integer  | 11  | N | 0 | 最小积分 | 
|  7 | max_credits | integer  | 11  | N | 0 | 最大积分 | 
|  8 | created_at  | integer  | 11  | N |   | 添加时间 | 
|  9 | created_by  | integer  | 11  | N |   | 添加人 | 
| 10 | updated_at  | integer  | 11  | N |   | 更新时间 | 
| 11 | updated_by  | integer  | 11  | N |   | 更新人 | 
