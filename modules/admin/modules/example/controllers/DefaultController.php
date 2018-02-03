<?php

namespace app\modules\admin\modules\example\controllers;

use app\modules\admin\extensions\BaseController;

/**
 * `example` 子模块
 */
class DefaultController extends BaseController
{

    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex($key)
    {
        return $this->render('index', [
            'key' => $key,
        ]);
    }
}
