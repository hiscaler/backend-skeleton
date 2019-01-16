幻灯片接口
========

## GET /api/slide/default?fields=:fields&category=:category&page=:page&pageSize=:pageSize
### 接口说明
列表（带翻页）
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | null | 返回的字段（id,title,categoryId,categoryName,url,urlOpenTarget,picturePath） |
| category | string | 否 | null | 分类 id |
| page | int | 否 | 1 | 当前页 |
| pageSize | int | 否 | 20 | 每页返回的数据量 |

## GET /api/slide/default/list?fields=:fields&category=:category&offset=:offset&limit=:limit
### 接口说明
列表（不带翻页）
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | null | 返回的字段（id,title,categoryId,categoryName,url,urlOpenTarget,picturePath） |
| category | string | 否 | null | 分类 id |
| offset | int | 否 | 1 | 起始位置 |
| limit | int | 否 | 20 | 返回的数据量 |
