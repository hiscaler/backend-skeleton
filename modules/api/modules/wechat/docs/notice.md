模板消息
=======

## 发送
GET /api/wechat/notice/send

### 接口说明
发送模板消息

### 参数说明
| 参数 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | :---: | :---: | :---: | :---: | --- |
| templateId | string | | | Y | 模板编号 |
| openId | string | | | Y | 会员的 openid |
| url | string | | | | 消息详情查看地址 |
| data | string | | | Y | 模板消息（json 编码格式） |