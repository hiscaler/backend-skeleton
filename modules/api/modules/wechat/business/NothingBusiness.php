<?php

namespace app\modules\api\modules\wechat\business;

use app\modules\api\modules\wechat\models\Order;

/**
 * 业务处理接口类
 *
 * @package app\modules\api\modules\wechat\business
 * @author hiscaler <hiscaler@gmail.com>
 */
class NothingBusiness implements BusinessInterface
{

    public function process(Order $order)
    {
        return true;
    }

}