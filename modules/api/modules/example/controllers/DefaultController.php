<?php

namespace app\modules\api\modules\example\controllers;

/**
 * `example` 模块接口
 *
 * @package app\modules\api\modules\example\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    /**
     * Hello World.
     *
     * @return array
     * @api api/example/default/index
     *
     */
    public function actionIndex()
    {
        return [
            'name' => 'hello',
            'value' => 'world.',
        ];
    }

}
