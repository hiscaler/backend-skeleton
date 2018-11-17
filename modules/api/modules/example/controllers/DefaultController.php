<?php

namespace app\modules\api\modules\example\controllers;

/**
 * `example` 模块接口
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    /**
     * Hello World.
     *
     * @api api/example/default/index
     *
     * @return array
     */
    public function actionIndex()
    {
        return [
            'name' => 'hello',
            'value' => 'world.',
        ];
    }

}
