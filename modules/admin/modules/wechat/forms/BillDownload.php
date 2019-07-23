<?php

namespace app\modules\admin\modules\wechat\forms;

use app\modules\admin\modules\wechat\models\Bill;
use DateTime;

/**
 * 对账单下载表单
 *
 * @package app\modules\admin\modules\wechat\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class BillDownload extends \yii\base\Model
{

    /**
     * @var string 账单类型
     */
    public $type;

    /**
     * @var string 账单日期
     */
    public $date;

    public function init()
    {
        parent::init();
        $this->type = Bill::TYPE_ALL;
        $this->date = (new Datetime())->modify("-1 day")->format('Ymd');
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