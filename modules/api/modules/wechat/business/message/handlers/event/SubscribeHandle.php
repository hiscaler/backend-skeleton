<?php

namespace app\modules\api\modules\wechat\business\message\handlers\event;

use app\modules\api\modules\wechat\business\message\handlers\HandleInterface;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Collection;

class SubscribeHandle implements HandleInterface
{

    /**
     * 消息处理代码
     * 在处理消息过程中，请使用 try catch 进行包裹，并抛出异常，调用端会截获到您抛出的异常，并记录到日志中，方便排查问题。
     *
     * @param Collection $message
     * @param Application $application
     */
    public function process(Collection $message, Application $application)
    {
    }

}