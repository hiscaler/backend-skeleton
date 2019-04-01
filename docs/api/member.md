会员
====

## 列表
GET /api/member/index

### 查询参数
键名称 | 值类型 | 备注 |
| --- | :---: | ---|
| username | string | 姓名 |
| real_name | string | 真实姓名 |
| mobile_phone | string | 手机号码 |
| type | int | 类型 1: 系统管理员 |

## 添加
POST /api/member/create

## 更新
PUT|PATCH /api/member/update?id=:id

## 详情
GET /api/member/view?id=:id

## 删除
DELETE /api/member/delete?id=:id