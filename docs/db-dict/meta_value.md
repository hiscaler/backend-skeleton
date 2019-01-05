www_meta_value
==============
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
| 1 | meta_id       | integer | 11  |   |  | Meta id | 
| 2 | object_id     | integer | 11  |   |  | 数据 id | 
| 3 | string_value  | string  | 255 | Y |  | 字符值 | 
| 4 | integer_value | integer | 11  | Y |  | 整型数字 | 
| 5 | decimal_value | decimal | 10  | Y |  | 浮点数 | 
| 6 | text_value    | text    |     | Y |  | 大段字符值 | 
