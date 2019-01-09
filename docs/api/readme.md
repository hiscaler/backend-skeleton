接口说明
=======
## 返回格式
```json
{
    success: true,
    data: {
        ...
    }
}
或者
{
    success: false,
    error: {
        message: "ERROR MESSAGE."
    }
}
```

>
> 在客户端，我们总可以判断返回数据中的 success 来作为数据是否成功返回的依据。以便进行下一步的操作。
>
> 需要认证的接口控制器请继承于 app\modules\api\extensions\AuthController 控制器。