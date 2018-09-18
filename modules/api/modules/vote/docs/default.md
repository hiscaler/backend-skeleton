投票接口
=======

## GET /api/vote/default
### 接口说明
列表（带翻页）
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | 无 | 可选择的字段（id,categoryId,categoryName,title,description,beginDatetime,endDatetime,totalVotesCount,allowAnonymous,allowViewResults,allowMultipleChoice,intervalSeconds,items,ordering,createdAt,updatedAt） |
| category | string | 否 | 无 | 分类 id，多个分类以小写的逗号分隔（1,2,3） |
| orderBy | string | 否 | ordering.asc | 排序方式（id,ordering,votesCount,createdAt,updatedAt） |
| page | int | 否 | 1 | 当前页 |
| pageSize | int | 否 | 20 | 每页显示的数据量 |


## GET /api/vote/default/list
### 接口说明
列表（不带翻页）
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | 无 | 可选择的字段（id,categoryId,categoryName,title,description,beginDatetime,endDatetime,totalVotesCount,allowAnonymous,allowViewResults,allowMultipleChoice,intervalSeconds,items,ordering,createdAt,updatedAt） |
| category | string | 否 | 无 | 分类 id，多个分类以小写的逗号分隔（1,2,3） |
| orderBy | string | 否 | ordering.asc | 排序方式（id,ordering,votesCount,createdAt,updatedAt） |
| offset | int | 否 | 0 | 从第几条数据开始 |
| limit | int | 否 | 10 | 每次拉取几条 |

## GET /api/vote/default/view?id=:id
### 接口说明
投票详情
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| id | int | 是 | 无 | 投票数据 id |

## POST /api/vote/default/voting?id=:id
### 接口说明
投票
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| id | int | 是 | 无 | 投票数据 id |
| optionId | int | 是 | 无 | 投票选项值 |