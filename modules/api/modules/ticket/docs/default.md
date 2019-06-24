工单管理
=======

## 列表
GET /api/ticket/default/index?accessToken=:accessToken

## 创建工单
POST /api/ticket/default/create?accessToken=:accessToken

## 更新工单
PUT|PATCH /api/ticket/default/update?id=:id&accessToken=:accessToken

## 删除
DELETE /api/ticket/default/delete?id=:id&accessToken=:accessToken

### <span id="params">提交参数</span>
| 参数 | 键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | --- | :---: | :---: | :---: | :---: | --- |
| 问题类型 | category_id | int | | 0 | 否 | |
| 问题描述 | description | string | | | 是 | |
| 机密信息 | confidential_information | string | | | 否 | |
| 手机号码 | mobile_phone | string | | | 否 | |
| 邮箱 | email | string | | | 否 | |
| 附件 | attachment_list | array | | | 否 | 多个文件上传多项或者应上传文件的路径列表 |
