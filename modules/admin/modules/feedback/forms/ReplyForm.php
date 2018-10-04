<?php

namespace app\modules\admin\modules\feedback\forms;

/**
 * 反馈回复表单
 *
 * @package app\modules\admin\modules\feedback\forms
 */
class ReplyForm extends \yii\base\Model
{

    public $message;

    public function rules()
    {
        return [
            ['message', 'required'],
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