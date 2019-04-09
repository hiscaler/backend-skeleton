分类
====

## 获取分类数据 
GET /api/category

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| sign | null/string | 否 | 无 | 后台设置的标记字符 |
| level | integer | 否 | 0 | 返回分类数据的层级（0表示所有层级） |
| flat | boolean | 否 | true | 为 false 返回平级数据，true 则返回 children 为子项目键的数据 |

### 返回格式
```json
{
    success: true,
    data: {
        items: [
            {
                "id": 1,
                "alias": "apple",
                "name": "苹果",
                "icon": null,
                "parent": 0,
                "level": 0
            },
            ...
        ]
    }
}
```