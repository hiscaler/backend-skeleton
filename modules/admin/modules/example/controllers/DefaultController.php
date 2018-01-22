<?php

namespace app\modules\admin\modules\example\controllers;

use app\modules\admin\extensions\BaseController;

/**
 * Default controller for the `example` module
 */
class DefaultController extends BaseController
{

    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
