www_meta
========
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id                    | integer  | 11  |   |   | 自增主键 | 
|  2 | table_name            | string   | 60  |   |   | 表名称 | 
|  3 | key                   | string   | 60  |   |   | 键名 | 
|  4 | label                 | string   | 255 |   |   | 显示名称 | 
|  5 | description           | string   | 255 |   |   | 描述 | 
|  6 | input_type            | string   | 16  |   |   | 输入类型 | 
|  7 | input_candidate_value | text     |     | Y |   | 输入候选值 | 
|  8 | return_value_type     | smallint | 6   |   | 0 | 返回值类型 | 
|  9 | default_value         | string   | 16  | Y |   | 默认值 | 
| 10 | enabled               | tinyint  | 1   |   | 1 | 激活 | 
| 11 | created_by            | integer  | 11  |   |   | 添加人 | 
| 12 | created_at            | integer  | 11  |   |   | 添加时间 | 
| 13 | updated_by            | integer  | 11  |   |   | 更新人 | 
| 14 | updated_at            | integer  | 11  |   |   | 更新时间 | 
| 15 | deleted_by            | integer  | 11  | Y |   | 删除人 | 
| 16 | deleted_at            | integer  | 11  | Y |   | 删除时间 | 
