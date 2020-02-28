<?php

namespace app\modules\api\models;

use app\modules\api\traits\MemberTrait;

/**
 * Class BaseFrontendMember
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseFrontendMember extends \app\models\FrontendMember
{

    use MemberTrait;
}