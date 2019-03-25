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

    /**
     * 业务逻辑处理代码
     * 返回 true 表示处理正常，如果处理过程中产生异常的话请返回 false，接口端会根据您的返回结果通知微信支付回调接口。
     * 返回 false 则会告知微信支付接口业务端逻辑处理失败，支付接口会重新发起回调。
     *
     * @param TradeOrder $order
     * @return bool
     */
    public function process(TradeOrder $order);

}