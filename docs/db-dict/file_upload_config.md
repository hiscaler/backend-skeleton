**www_file_upload_config**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id           | integer  | 11 | N |     |         | 
|  2 | type         | smallint | 6  | N | 0   | 类型  | 
|  3 | model_name   | string   | 60 | N |     | 模型名称 | 
|  4 | attribute    | string   | 60 | N |     | 表字段名 | 
|  5 | extensions   | string   | 60 | N |     | 允许的文件后缀 | 
|  6 | min_size     | integer  | 11 | N | 1   | 最小尺寸 | 
|  7 | max_size     | integer  | 11 | N | 200 | 最大尺寸 | 
|  8 | thumb_width  | smallint | 6  | Y |     | 缩略图宽度 | 
|  9 | thumb_height | smallint | 6  | Y |     | 缩略图高度 | 
| 10 | created_by   | integer  | 11 | N |     | 添加人 | 
| 11 | created_at   | integer  | 11 | N |     | 添加时间 | 
| 12 | updated_by   | integer  | 11 | N |     | 更新人 | 
| 13 | updated_at   | integer  | 11 | N |     | 更新时间 | 
