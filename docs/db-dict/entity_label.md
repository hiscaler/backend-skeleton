**www_entity_label**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id         | integer | 11 | N |   |        | 
|  2 | entity_id  | integer | 11 | N |   | 数据 id | 
|  3 | model_name | string  | 60 | N |   | 模型名称 | 
|  4 | label_id   | integer | 11 | N |   | 推送位 id | 
|  5 | ordering   | integer | 11 | N | 0 | 排序 | 
|  6 | enabled    | tinyint | 1  | N | 1 | 激活 | 
|  7 | created_at | integer | 11 | N |   | 添加时间 | 
|  8 | created_by | integer | 11 | N |   | 添加人 | 
|  9 | updated_at | integer | 11 | N |   | 更新时间 | 
| 10 | updated_by | integer | 11 | N |   | 更新人 | 
