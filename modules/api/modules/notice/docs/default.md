通知管理
=======

## 列表
GET /api/notice/default/index?accessToken=:accessToken

## 创建通知
POST /api/notice/default/create?accessToken=:accessToken

## 更新通知
PUT|PATCH /api/notice/default/update?id=:id&accessToken=:accessToken

## 删除
DELETE /api/notice/default/delete?id=:id&accessToken=:accessToken

### <span id="params">提交参数</span>
| 参数 | 键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | --- | :---: | :---: | :---: | :---: | --- |
| 分类 | category_id | int | | 0 | 否 | |
| 标题 | title | string | | | 是 | |
| 问题描述 | description | string | | | 是 | |
| 正文内容 | content | string | | | 是 | |
| 激活 |  enabled | boolean | | | 否 | |
| 发布时间 | published_at | datetime | | | 是 | |
| 排序 | ordering | int | | | 否 | |
| 查看权限 | view_permission | int | | | 是 | 0：所有人员, 1: 指定的人员, 3: 根据会员级别 |
| 允许查看的会员 | view_member_id_list | string | | | 否 | 允许查看的会员 id，多个之间使用小写的逗号进行分隔 |
