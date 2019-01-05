<?php

namespace app\modules\api\models;

use app\models\MemberProfile;

/**
 * Class BaseMemberProfile
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseMemberProfile extends MemberProfile
{

    public function fields()
    {
        return [
            'memberId' => 'member_id',
            'tel',
            'address',
            'zipCode' => 'zip_code',
            'status',
        ];
    }

}