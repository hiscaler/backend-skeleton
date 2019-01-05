<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;

/**
 * Class DefaultController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    public function actionIndex()
    {
        return 'Ok';
    }

}