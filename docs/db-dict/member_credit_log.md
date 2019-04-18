www_member_credit_log
=====================
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
| 1 | id          | integer  | 11 |   |   | 自增主键 | 
| 2 | member_id   | integer  | 11 |   |   | 会员 id | 
| 3 | operation   | string   | 40 |   |   | 积分类型 | 
| 4 | related_key | string   | 60 | Y |   | 外部关联数据 | 
| 5 | credits     | smallint | 6  |   |   | 积分 | 
| 6 | remark      | text     |    | Y |   | 备注 | 
| 7 | created_at  | integer  | 11 |   |   | 操作时间 | 
| 8 | created_by  | integer  | 11 |   | 0 | 操作人 | 
