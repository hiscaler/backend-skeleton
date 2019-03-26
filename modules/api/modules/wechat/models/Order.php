<?php

namespace app\modules\api\modules\wechat\models;

class Order extends \app\modules\admin\modules\wechat\models\Order
{

    public function fields()
    {
        return [
            'id',
            'appid',
            'mch_id',
            'device_info',
            'nonce_str',
            'sign',
            'sign_type',
            'transaction_id',
            'out_trade_no',
            'body',
            'detail',
            'attach',
            'fee_type',
            'total_fee',
            'spbill_create_ip',
            'time_start',
            'time_expire',
            'time_end',
            'goods_tag',
            'trade_type',
            'product_id',
            'limit_pay',
            'openid',
            'trade_state',
            'trade_state_desc',
            'status',
            'status_formatted' => function ($model) {
                $options = self::statusOptions();

                return isset($options[$model->status]) ? $options[$model->status] : null;
            },
            'member_id',
        ];
    }

}