<?php

namespace app\modules\api\modules\link\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\link\models\Link;
use app\modules\api\modules\link\models\LinkSearch;

/**
 * /api/link/default
 *
 * @package app\modules\api\modules\link\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends ActiveController
{

    public $modelClass = Link::class;

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     * @throws \Throwable
     */
    public function prepareDataProvider()
    {
        $search = new LinkSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

}
