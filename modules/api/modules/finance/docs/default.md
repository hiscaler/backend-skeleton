财务管理
=======

## 列表
GET /api/finance/default/index?accessToken=:accessToken

## 创建财务记录
POST /api/finance/default/create?accessToken=:accessToken

### <span id="params">提交参数</span>
| 参数 | 键名称 | 值类型 | 长度 | 默认值 | 必填 | 备注 |
| --- | --- | :---: | :---: | :---: | :---: | --- |
| 类型 | type | int | | | 是 | 0: 入账, 1: 退款 |
| 金额 | money | int | | | 是 | 以“分”为最小单位 |
| 来源 | source | int | | | 是 | 0: 其他, 1: 现金, 2: 微信, 3: 支付宝 |
| 汇款凭单 | remittance_slip | file | | | | |
| 关联业务 | related_key | string | | | | |
| 备注 | remark | string | | | | |
| 会员 id | member_id | int | | | 是 | |