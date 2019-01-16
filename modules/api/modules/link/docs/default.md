链接接口
=======

## 列表
GET /api/link/default

### 查询参数
| 参数 | 类型 | 说明 |
|---|:---:|---|
| category_id | int | 分类 id |
| type | int | 类型（0：文本、1: 图片） |
| title | title | 标题 |

## 添加
POST /api/link/create

### <span id="params">参数</span>
| 参数 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | :---: | :---: | :---: | :---: | --- |
| category_id | int | | 0 |  | 所属分类 |
| type | int | | 0 | Y | 类型（0：文本、1: 图片） |
| title | string | 1 ~ 60 | | Y | 标题 |
| description | string | 1 ~ 60 | | Y | 描叙 |
| url | string | 1 ~ 100| | Y | URL |
| url_open_target | string | 6 | _blank | Y | 链接打开方式（_self, _blank） |
| logo | file | | | | Logo（类型为“图片”的时候需要提供） |
| ordering | int | | 0 | | 排序 |

## 详情
GET /api/link/view?id=:id

## 更新
PUT|PATCH /api/link/update?id=:id

提交参数详见 [添加提交参数说明](#params)

## 删除
DELETE /api/link/delete?id=:id