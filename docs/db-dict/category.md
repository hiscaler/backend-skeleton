**www_category**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id          | integer  | 11  | N |   |         | 
|  2 | sign        | string   | 40  | Y |   | 标记  | 
|  3 | alias       | string   | 120 | N |   | 分类别名 | 
|  4 | name        | string   | 30  | N |   | 分类名称 | 
|  5 | short_name  | string   | 30  | N |   | 简称  | 
|  6 | parent_id   | integer  | 11  | N | 0 | 父级  | 
|  7 | level       | smallint | 6   | N | 0 | 层级  | 
|  8 | id_path     | string   | 100 | Y |   | id 层级路径 | 
|  9 | name_path   | string   | 255 | Y |   | 名称层级路径 | 
| 10 | icon        | string   | 100 | Y |   | 分类图标 | 
| 11 | description | text     |     | Y |   | 描述  | 
| 12 | ordering    | smallint | 6   | N | 0 | 排序  | 
| 13 | quantity    | integer  | 11  | N | 0 | 数量  | 
| 14 | enabled     | tinyint  | 1   | N | 1 | 激活  | 
| 15 | created_at  | integer  | 11  | N |   | 添加时间 | 
| 16 | created_by  | integer  | 11  | N |   | 添加人 | 
| 17 | updated_at  | integer  | 11  | N |   | 更新时间 | 
| 18 | updated_by  | integer  | 11  | N |   | 更新人 | 
