<?php

namespace app\modules\api\modules\wechat\business;

use app\modules\api\modules\wechat\models\Order;

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
     * 当业务处理遇到问题时，请抛出异常，调用端会截获到您抛出的异常，并记录到日志中，方便排查问题。
     *
     * false 与抛出异常的区别在于返回 false，调用端不会知道失败的具体原因，而只是简单的回应微信业务处理失败。
     *
     * @param Order $order
     * @return bool
     */
    public function process(Order $order);

}