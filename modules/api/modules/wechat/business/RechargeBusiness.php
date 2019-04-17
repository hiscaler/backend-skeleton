<?php

namespace app\modules\api\modules\wechat\business;

use app\modules\api\modules\finance\models\Finance;
use app\modules\api\modules\wechat\models\Order;

/**
 * 会员充值处理
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
        $finance->remark = trim($order->body . "  " . $order->detail);

        return $finance->save();
    }

}