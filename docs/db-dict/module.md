**www_module**
---
| 序号 | 字段名称 | 类型 | 长度 | 允许 NULL | 默认值 | 备注 | 
| :---: | --- | --- | :---: | :---: | :---: | --- | 
|  1 | id          | integer | 11  | N |  |      | 
|  2 | alias       | string  | 20  | N |  | 别名 | 
|  3 | name        | string  | 30  | N |  | 模块名称 | 
|  4 | author      | string  | 20  | N |  | 作者 | 
|  5 | version     | string  | 10  | N |  | 版本 | 
|  6 | icon        | string  | 100 | Y |  | 图标 | 
|  7 | url         | string  | 100 | Y |  | URL  | 
|  8 | description | string  | 255 | Y |  | 描述 | 
|  9 | menus       | text    |     | Y |  | 菜单配置 | 
| 10 | created_at  | integer | 11  | N |  | 添加时间 | 
| 11 | created_by  | integer | 11  | N |  | 添加人 | 
| 12 | updated_at  | integer | 11  | N |  | 更新时间 | 
| 13 | updated_by  | integer | 11  | N |  | 更新人 | 
