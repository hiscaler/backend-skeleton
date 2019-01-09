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
            'memberId' => 'member_id',
            'operation',
            'relatedKey' => 'related_key',
            'credits',
            'remark',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
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