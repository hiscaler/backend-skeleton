留言反馈接口
===========

## GET /api/feedback/default?fields=:fields&category=:category&page=:page&pageSize=:pageSize
### 接口说明
列表（带翻页）
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | null | 返回的字段（title,categoryId,username,tel,mobilePhone,email,message,createdAt,createdBy,updatedAt,updatedBy） |
| category | string | 否 | null | 分类 id |

## GET /api/feedback/default/list?fields=:fields&category=:category&offset=:offset&limit=:limit
### 接口说明
列表（不带翻页）
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | null | 返回的字段（title,categoryId,username,tel,mobilePhone,email,message,createdAt,createdBy,updatedAt,updatedBy） |
| category | string | 否 | null | 分类 id |

## POST /api/feedback/default/submit
### 接口说明
提交留言反馈
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| categoryId | int | 否 | 0 | 分类 id |
| title | string | 否 | null | 标题 |
| username | string | 否 | null | 姓名 |
| tel | string | 否 | null | 电话号码 |
| mobilePhone | string | 否 | null | 手机号码 |
| email | string | 否 | null | 邮箱 |
| message | string | 是 | 无 | 内容 |