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
            'member_id',
            'subscribe' => function ($model) {
                return $model->subscribe ? true : false;
            },
            'openid',
            'nickname',
            'sex',
            'sex_formatted' => function ($model) {
                $options = Option::sexes();

                return isset($options[$model->sex]) ? $options[$model->sex] : null;
            },
            'country',
            'province',
            'city',
            'language',
            'headimgurl',
            'subscribe_time',
            'unionid',
        ];
    }

}
