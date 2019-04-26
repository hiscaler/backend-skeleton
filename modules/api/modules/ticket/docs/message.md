工单消息管理
==========

## 列表
GET /api/ticket/message/index?accessToken=:accessToken

## 回复消息
POST /api/ticket/message/create?accessToken=:accessToken

## 更新消息
PUT|PATCH /api/ticket/message/update?id=:id&accessToken=:accessToken

## 删除消息
DELETE /api/ticket/message/delete?id=:id&accessToken=:accessToken

### <span id="params">提交参数</span>
| 参数 | 键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | --- | :---: | :---: | :---: | :---: | --- |
| 工单编号 | ticket_id | int | | | 是 | |
| 类型 | type | int | | 1 | 是 | 0: 会员 1: 客服 |
| 消息 | content | string | | | 是 | |
| 引用消息 | parent_id | int | | | 否 | |
