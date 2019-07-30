<?php

namespace app\modules\admin\modules\feedback\forms;

/**
 * 反馈回复表单
 *
 * @package app\modules\admin\modules\feedback\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class ReplyForm extends \yii\base\Model
{

    /**
     * @var string 回复内容
     */
    public $message;

    public function rules()
    {
        return [
            ['message', 'required'],
            ['message', 'trim'],
            ['message', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'message' => '回复内容',
        ];
    }

}