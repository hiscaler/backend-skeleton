会员积分
=======

## 列表
GET /api/member-credit-log/index

### 返回结果
TODO

### 查询参数
TODO

## 添加
POST /api/member-credit-log/create

### <span id="params">参数</span>
键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | :---: | :---: | :---: | :---: | --- |
|member_id|int| | |Y|会员 id|
|operation|string|1~20| |Y|操作类型|
|related_key|string|1~60| | |外部关联数据|
|credits|int| | |Y|积分（非零的正负数）|
|remark|string| | | |备注|

## 详情
GET /api/member-credit-log/view?id=:id
