内置接口说明
===========
> 接口返回统一格式为
> ```json
> {
>     success: true,
>     data: {
>         ...
>     }
> }
> ```
>
> 或者
>
> ```json
> {
>     success: false,
>     error: {
>         message: "ERROR MESSAGE."
>     }
> }
> ```
>
> 在客户端，我们总可以判断返回数据中的 success 来作为数据是否成功返回的依据。以便进行下一步的操作。

## GET /api/category
### 说明
分类数据获取接口
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| sign | null/string | 否 | 无 | 后台设置的标记字符 |
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

## GET /api/lookup?name=:name
### 说明
常规设定项目取值
### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| name | string | 是 | null | 常规设定的键名 |

## GET /api/wxapp/login
### 说明
微信小程序认证
### 参数说明
无

## GET /api/wxapp/check-session
### 说明
微信小程序 session 检测
### 参数说明
无
