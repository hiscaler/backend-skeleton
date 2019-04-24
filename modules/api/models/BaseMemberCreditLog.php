<?php

namespace app\modules\api\models;

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
        return [
            'id',
            'member_id',
            'operation',
            'operation_formatted' => function ($model) {
                $options = MemberCreditLog::operationOptions();

                return isset($options[$model->operation]) ? $options[$model->operation] : null;
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