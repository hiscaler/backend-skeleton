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
        // Aliyun
        $length = 4; // 四位长度的验证码
        $captcha = mt_rand((int) str_pad("1", $length, "0"), (int) str_pad("9", $length, "9"));
        $this->setContent('您的验证码为${code},该验证码5分钟有效！')
            ->setData('code', $captcha)
            ->setCached(true)
            ->setCacheValue($captcha)
            ->setCacheDuration(10 * 60);

        return $this;
    }

}