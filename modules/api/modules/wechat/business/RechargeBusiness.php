<?php

namespace app\modules\api\modules\wechat\business;

use app\modules\api\modules\finance\models\Finance;
use app\modules\api\modules\wechat\models\Order;

/**
 * 会员微信充值处理
 *
 * @package app\modules\api\modules\wechat\business
 * @author hiscaler <hiscaler@gmail.com>
 */
class RechargeBusiness implements BusinessInterface
{

    public function process(Order $order)
    {
        $finance = new Finance();
        $finance->type = Finance::TYPE_INCOME;
        $finance->money = $order->total_fee;
        $finance->source = Finance::SOURCE_WECHAT;
        $finance->member_id = $order->member_id;
        $finance->related_key = $order->transaction_id;
        $finance->status = Finance::STATUS_VALID;
        $remarks = [];
        $order->transaction_id && $remarks[] = "微信订单号：{$order->transaction_id}";
        $order->out_trade_no && $remarks[] = "商户订单号：{$order->out_trade_no}";
        $order->body && $remarks[] = "　商品描述：{$order->body}";
        $order->detail && $remarks[] = "　商品详情：{$order->detail}";
        $finance->remark = implode(PHP_EOL, $remarks);

        return $finance->save();
    }

}