<?php

namespace app\modules\admin\forms;

use Yii;
use yii\base\Model;

/**
 * 积分表单
 */
class CreditForm extends Model
{

    public $credits;
    public $remark;

    public function rules()
    {
        return [
            [['credits'], 'required'],
            [['credits'], 'integer'],
            [['remark'], 'safe'],
        ];
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'credits' => Yii::t('userCreditLog', 'Credits'),
            'remark' => Yii::t('userCreditLog', 'Remark'),
        ];
    }

}
