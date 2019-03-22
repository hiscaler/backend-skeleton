<?php

namespace app\modules\api\modules\wechat\business;

/**
 * Class BusinessInterface
 * 业务处理接口类
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class NothingBusiness implements BusinessInterface
{

    public function process(TradeOrder $order)
    {
        return true;
    }

}