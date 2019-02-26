<?php

namespace app\modules\api\modules\wechat\models;

class BaseResponse
{

    const DEFAULT_RETURN_MESSAGE = '';

    /**
     * 文本消息处理
     *
     * @param $message
     * @return string
     */
    public static function text($message)
    {
        return static::DEFAULT_RETURN_MESSAGE;
    }

    /**
     * 地理位置消息处理
     *
     * @param $message
     * @return string
     */
    public static function location($message)
    {
        return static::DEFAULT_RETURN_MESSAGE;
    }

}