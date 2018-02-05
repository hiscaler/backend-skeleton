内置接口说明
===========
> 接口返回统一格式为
> ```json
> {
>     success: true,
>     data: {
>         items: [
>             {
>                 // items
>             }
>         ]
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

## GET /api/category
### 说明
分类数据获取接口
### 参数说明
|参数|类型|必填|说明|
|---|:---:|:---:|---|
|sign | null/string|否|后台设置的标记字符|
### 返回格式

```json
{
    success: true,
    data: {
        items: [
            {
                // items
            }
        ]
    }
}
```

