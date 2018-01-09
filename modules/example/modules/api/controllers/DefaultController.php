<?php

namespace app\modules\example\modules\api\controllers;

/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
{

    /**
     * Hello World.
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
