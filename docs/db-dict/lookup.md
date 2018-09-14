www_lookup
==========
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id           | integer  | 11 |   |        | 自增主键            | 
|  2 | type         | tinyint  | 1  |   | 1      | 类型（0:私有 1: 公有） | 
|  3 | group        | smallint | 6  |   | 0      | 分组（0: 自定义　1: 系统 2: SEO） | 
|  4 | key          | string   | 60 |   |        | 键名                  | 
|  5 | label        | string   | 60 |   |        | 标签                  | 
|  6 | description  | text     |    | Y |        | 描述                  | 
|  7 | value        | text     |    |   |        | 值                     | 
|  8 | return_type  | smallint | 6  |   | 0      | 返回值类型         | 
|  9 | input_method | string   | 12 |   | string | 输入方式            | 
| 10 | input_value  | text     |    | Y |        | 输入值               | 
| 11 | enabled      | tinyint  | 1  |   | 1      | 激活                  | 
| 12 | created_by   | integer  | 11 |   | 0      | 添加人               | 
| 13 | created_at   | integer  | 11 |   |        | 添加时间            | 
| 14 | updated_by   | integer  | 11 |   | 0      | 更新人               | 
| 15 | updated_at   | integer  | 11 |   |        | 更新时间            | 
