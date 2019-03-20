<?php

namespace app\modules\api\modules\wechat\business;

/**
 * Class BusinessInterface
 * 业务处理接口类
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
interface BusinessInterface
{

    public function process(TradeOrder $order);

}