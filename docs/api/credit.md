积分
====

## 获取当前登录会员积分数据 
GET /api/credit/index

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| operation | null/string | 否 |  | 操作类型 |
| related_key | null/string | 否 | | 外部关联数据 |

### 返回格式
```json
{
    "success": true,
    "data": {
        "items": [
            {
                "id": 13,
                "member_id": 1,
                "operation": "task",
                "operation_formatted": "任务消费",
                "related_key": 20190415.1,
                "credits": -775,
                "remark": "2019年04月15日 00点任务积分消费",
                "created_at": 1555408059,
                "created_by": 0
            },
            {
                "id": 12,
                "member_id": 1,
                "operation": "finance",
                "operation_formatted": "财务",
                "related_key": 28,
                "credits": 400,
                "remark": null,
                "created_at": 1555403008,
                "created_by": 1
            }
        ],
        "_links": {
            "self": {
                "href": "http://192.168.2.222:8882/index.php/api/credit/index?id=4&accessToken=MrZLMBQRotaPY1UhzaR6dQiiFaRIrzuo&_format=json&page=1"
            }
        },
        "_meta": {
            "totalCount": 13,
            "pageCount": 1,
            "currentPage": 1,
            "perPage": 20
        }
    }
}
```

## 积分消费 
GET /api/credit/create

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| member_id | int | 是 |  | 会员 |
| operation | null/string | 是 |  | 操作类型 |
| related_key | null/string | 否 | | 外部关联数据 |
| credits | int | 是 | | 积分数量 |
| remark | string | 否 | | 备注 |