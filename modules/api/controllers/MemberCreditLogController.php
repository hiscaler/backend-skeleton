<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\MemberCreditLog;

/**
 * Class MemberCreditLogController
 *
 * @package app\modules\api\controllers\
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberCreditLogController extends ActiveController
{

    public $modelClass = MemberCreditLog::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);

        return $actions;
    }

}