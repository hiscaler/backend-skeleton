<?php

namespace app\modules\api\modules\wechat\models;

class Response extends BaseResponse
{

    public static function location($message)
    {
        return $message->Label;
    }

}