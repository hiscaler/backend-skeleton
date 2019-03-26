<?php

namespace app\modules\api\modules\wechat\models;

/**
 * 统一下单表单
 * Class Order
 *
 * @package app\modules\api\modules\wechat\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class PrepareOrder extends Order
{

    /**
     * @var string 回调通知地址
     */
    public $notify_url;

    public function rules()
    {
        $rules = parent::rules();
        unset($rules['required']);
        array_unshift($rules, [['total_fee'], 'required']);
        $rules[] = ['notify_url', 'trim'];
        $rules[] = ['notify_url', 'string', 'max' => 256];

        return $rules;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'notify_url' => '通知地址',
        ]);
    }

}