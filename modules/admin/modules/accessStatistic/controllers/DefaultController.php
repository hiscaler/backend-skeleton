<?php

namespace app\modules\admin\modules\accessStatistic\controllers;

use app\modules\admin\extensions\BaseController;

/**
 * `accessStatistic` 子模块
 */
class DefaultController extends BaseController
{

    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->redirect('../sites/index');
    }
}
