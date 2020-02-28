<?php

namespace app\modules\api\models;

use app\modules\api\traits\MemberTrait;

/**
 * Class BaseBackendMember
 *
 * @package app\modules\api\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseBackendMember extends \app\models\BackendMember
{

    use MemberTrait;
}