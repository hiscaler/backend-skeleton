www_meta_validator
==================
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
| 1 | id      | integer | 11 |   |  | 自增主键 | 
| 2 | meta_id | integer | 11 |   |  | Meta id | 
| 3 | name    | string  | 30 |   |  | 验证器名称 | 
| 4 | options | text    |    | Y |  | 验证器配置属性 | 
