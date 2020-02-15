<?php

namespace app\business;

/**
 * 短信消息处理
 *
 * @package app\business
 * @author hiscaler <hiscaler@gmail.com>
 */
class SmsCaptchaBusiness extends SmsBusinessAbstract
{

    public function build()
    {
        $seconds = 600;
        $length = 4; // 四位长度的验证码
        $captcha = mt_rand((int) str_pad("1", $length, "0"), (int) str_pad("9", $length, "9"));
        $this->setContent('您的验证码为{code}，该验证码 ' . ($seconds / 60) . ' 分钟有效！')
            ->setData('code', $captcha)
            ->setUseCache(true)
            ->setCacheValue($captcha)
            ->setCacheDuration($seconds);

        return $this;
    }

}