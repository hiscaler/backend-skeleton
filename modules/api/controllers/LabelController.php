<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\Label;

/**
 * 推送位
 * Class LabelController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class LabelController extends ActiveController
{

    public $modelClass = Label::class;

}