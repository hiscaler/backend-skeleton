文件处理
=======

## 上传
POST /api/file/uploading

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| key | string,array,file | 是 | file | 上传对象名称 |
| single | int | 否 | 1 | 是否返回所有的错误信息 |
| generate_thumbnail | int | 否 | 1 | 是否生成缩略图 |
| thumbnail_size | string | 否 | 100x100 | 缩略图尺寸，宽度x高度 |

> 允许上传的文件格式为以下四种：
> 1. Base64 格式的图片文件
> 2. 一个文件对象
> 3. 多个文件对象组成的数组
> 4. 网络文件地址，比如 https://pics7.baidu.com/feed/dcc451da81cb39dbe7d2055bccb31f23aa1830dc.jpeg?token=f2c1be804c9bc36ea5f7258bb3c74d4c
>
> 无论传递的是何种格式的文件数据，接口端都会对接收到的数据进行解析，分析出您上传的对象并进行相应的处理。
>
> 需要注意的是，在上传多个文件的时候，即使多个文件上传中出现错误，默认情况下只会返回一个文件的出错信息，如果需要返回多个，请传递 `single=0` 参数，提交该参数后，系统将会以 Map 的形式返回所有上传过程中出现的错误信息。其中 Map 的键值以您传递的 key 值开头，并且加上下划线以及文件的顺序号，例如：`file_0`。接收到错误信息后，您可以根据返回数据以及页面的需求做进一步的处理。
>
> 如果上传的文件为图片，则默认情况下会生成图片 100x100 尺寸大小的缩略图，如果您不需要生成缩略图，则需要明确的传递 `generate_thumbnail=0` 参数给接口

### 返回格式
```json
{
    "success": true,
    "data": {
        "original_name": "4.jpg",
        "real_name": "7e88adbf17b08f6b4ff66c9ef536d873.jpg",
        "path": "/uploads/2019/6/25/7e88adbf17b08f6b4ff66c9ef536d873.jpg",
        "full_path": "http://192.168.2.222:8882/uploads/2019/6/25/7e88adbf17b08f6b4ff66c9ef536d873.jpg",
        "size": 110004,
        "mime_type": null,
        "thumbnail": {
            "name": "7e88adbf17b08f6b4ff66c9ef536d873_thumb.jpg",
            "path": "/uploads/2019/6/25/7e88adbf17b08f6b4ff66c9ef536d873_thumb.jpg",
            "full_path": "http://192.168.2.222:8882/uploads/2019/6/25/7e88adbf17b08f6b4ff66c9ef536d873_thumb.jpg",
            "base64": "data:image/jpeg;base64..."
        }
    }
}
```
如果没有传递了 `generate_thumbnail=0` 参数给接口，则接口返回的数据格式如下：
```json
{
    "success": true,
    "data": {
        "original_name": "4.jpg",
        "real_name": "7e88adbf17b08f6b4ff66c9ef536d873.jpg",
        "path": "/uploads/2019/6/25/7e88adbf17b08f6b4ff66c9ef536d873.jpg",
        "full_path": "http://192.168.2.222:8882/uploads/2019/6/25/7e88adbf17b08f6b4ff66c9ef536d873.jpg",
        "size": 110004,
        "mime_type": null,       
        "base64": "data:image/jpeg;base64..."
    }
}
```

## 删除
DELETE /api/file/delete

### 参数说明
| 参数 | 类型 | 必填 | 默认值 | 说明 |
|---|:---:|:---:|:---:|---|
| url | string | 是 | | 需要删除的文件路径 |