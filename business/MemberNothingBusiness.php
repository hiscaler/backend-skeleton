<?php

namespace app\business;

use app\models\Member;

/**
 * 业务处理接口类
 *
 * @package app\business
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberNothingBusiness implements MemberBusinessInterface
{

    /**
     * @param Member $member
     * @param $insert
     * @param $changedAttributes
     */
    public function process(Member $member, $insert, $changedAttributes)
    {
        // @todo
    }

}