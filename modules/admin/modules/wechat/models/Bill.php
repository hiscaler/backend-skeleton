<?php

namespace app\modules\admin\modules\wechat\models;

/**
 * Class Bill
 *
 * @package app\modules\admin\modules\wechat\models
 * @author hisclaer <hiscaler@gmail.com>
 */
class Bill
{

    /**
     * 账单类型
     */
    const TYPE_ALL = 'ALL';
    const TYPE_SUCCESS = 'SUCCESS';
    const TYPE_REFUND = 'REFUND';
    const TYPE_REVOKED = 'REVOKED';

    /**
     * 账单类型选项
     *
     * @return array
     */
    public static function typeOptions()
    {
        return [
            self::TYPE_ALL => '所有订单',
            self::TYPE_SUCCESS => '成功支付的订单',
            self::TYPE_REFUND => '退款的订单',
            self::TYPE_REVOKED => '撤销的订单',
        ];
    }

}