推送位
=====

## 列表
GET /api/label/index

### 查询参数
键名称 | 值类型 | 备注 |
| --- | :---: | ---|
| name | string | 名称 |
| alias | string | 别名 |
| enabled | int | 是否激活 |

## 添加
POST /api/label/create

## 更新
PUT|PATCH /api/label/update?id=:id

## 删除
DELETE /api/label/delete?id=:id