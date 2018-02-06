链接接口
=======

## GET api/link/default
###接口说明
获取链接数据
###参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | null | 可返回的列表字段（title,categoryId,categoryName,type,description,url,logo,urlOpenTarget） |
| category | string | 否 | null | 分类 id |
| page | int | 否 | 1 | 当前页 |
| pageSize | int | 否 | 20 | 每页返回的数据量 |
