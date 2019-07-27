<?php

namespace app\modules\api\models;

use app\modules\api\extensions\Formatter;
use Yii;

/**
 * Class BaseMemberCreditLog
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseMemberCreditLog extends \app\models\MemberCreditLog
{

    public function fields()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->getFormatter();

        return [
            'id',
            'member_id',
            'operation',
            'operation_formatted' => function ($model) use ($formatter) {
                return $formatter->asMemberCreditOperation($model->operation);
            },
            'related_key',
            'credits',
            'remark',
            'created_at',
            'created_by',
        ];
    }

    /**
     * 所属会员
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }

    public function extraFields()
    {
        return ['member'];
    }

}