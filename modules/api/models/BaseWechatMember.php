<?php

namespace app\modules\api\models;

/**
 * Class BaseWechatMember
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseWechatMember extends \app\models\WechatMember
{

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'memberId' => 'member_id',
            'subscribe' => function () {
                return $this->subscribe ? true : false;
            },
            'openid',
            'nickname',
            'sex',
            'country',
            'province',
            'city',
            'language',
            'headImgUrl' => 'headimgurl',
            'subscribeTime' => 'subscribe_time',
            'unionid',
        ];
    }

}
