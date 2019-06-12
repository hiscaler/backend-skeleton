二维码
=====

## 创建临时二维码
GET /api/wechat/qrcode/temporary

### 参数说明
| 参数 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | :---: | :---: | :---: | :---: | --- |
| sceneValue | string | | | Y | 场景值 |
| expireSeconds | int | | 7 天 | | 多少秒后过期 |

## 创建永久二维码
GET /api/wechat/qrcode/forever

### 参数说明
| 参数 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | :---: | :---: | :---: | :---: | --- |
| sceneValue | string | | | Y | 场景值 |