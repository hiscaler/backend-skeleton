资讯接口
=======

## GET api/news/default
###接口说明
获取资讯数据（带翻页数据）
###参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | null | 返回的字段（id,categoryId,categoryName,title,shortTitle,keywords,description,isPictureNews,picturePath,enabledComment,commentsCount,publishedAt,createdAt,updatedAt） |
| category | string | 否 | null | 分类 id（多个以逗号分隔，以“!”开头的表示排除该分类） |
| children | string | 否 | null | 是否查询子分类（y,n） |
| label | string | 否 | null | 推送位（多个推送位中间以逗号分隔） |
| picture | string | 否 | null | 是否为图片资讯（y,n） |
| author | string | 否 | null | 资讯作者 |
| keywords | string | 否 | null | 资讯关键词 |
| reject | string | 否 | null | 需要排除的数据（格式：id:1,2,3） |
| page | int | 否 | 1 | 当前页 |
| pageSize | int | 否 | 20 | 每页返回的数据量 |
| expand | string | 否 | null | 附加数据，多个数据间使用逗号分隔，当前可使用的数据为：content |


## GET api/news/default/list
###接口说明
获取资讯数据（不带翻页数据）
###参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| fields | string | 否 | null | 返回的字段（id,categoryId,categoryName,title,shortTitle,keywords,description,isPictureNews,picturePath,enabledComment,commentsCount,publishedAt,createdAt,updatedAt） |
| category | string | 否 | null | 分类 id（多个以逗号分隔，以“!”开头的表示排除该分类） |
| children | string | 否 | null | 是否查询子分类（y,n） |
| label | string | 否 | null | 推送位（多个推送位中间以逗号分隔） |
| picture | string | 否 | null | 是否为图片资讯（y,n） |
| author | string | 否 | null | 资讯作者 |
| keywords | string | 否 | null | 资讯关键词 |
| reject | string | 否 | null | 需要排除的数据（格式：id:1,2,3） |
| offset | int | 否 | null | 起始位置 |
| limit | int | 否 | 10 | 返回的数据量 |
| expand | string | 否 | null | 附加数据，多个数据间使用逗号分隔，当前可使用的数据为：content |

## GET api/news/default/view?id=:id
###接口说明
获取资讯详情
###参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| id | int | 是 | null | 数据 id |
| fields | string | 否 | null | 返回的字段（id,categoryId,categoryName,title,shortTitle,keywords,description,isPictureNews,picturePath,enabledComment,commentsCount,publishedAt,createdAt,updatedAt） |
| expand | string | 否 | null | 附加数据，多个数据间使用逗号分隔，当前可使用的数据为：content |