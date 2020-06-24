<?php

namespace app\business;

use app\models\BaseMember;

/**
 * 业务处理接口类
 *
 * @package app\business
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberNothingBusiness implements MemberBusinessInterface
{

    /**
     * @param BaseMember $member
     * @param $insert
     * @param array $changedAttributes
     * @param array $params
     */
    public function process(BaseMember $member, $insert, array $changedAttributes, array $params)
    {
        // @todo
    }

}