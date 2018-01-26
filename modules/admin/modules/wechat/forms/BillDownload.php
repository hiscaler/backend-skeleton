<?php

namespace app\modules\admin\modules\wechat\forms;

/**
 * 对账单下载
 *
 * @package app\modules\admin\modules\wechat\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class BillDownload extends \yii\base\Model
{

    public $type;
    public $date;

    public function init()
    {
        parent::init();
        $this->type = \app\modules\admin\modules\wechat\models\Bill::TYPE_ALL;
        $this->date = date('Ymd') - 1;
    }

    public function rules()
    {
        return [
            [['type', 'date'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => '类型',
            'date' => '日期',
        ];
    }

}