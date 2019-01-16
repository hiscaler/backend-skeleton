<?php

namespace app\modules\api\modules\slide\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\slide\models\Slide;
use app\modules\api\modules\slide\models\SlideSearch;

/**
 * /api/slide/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends ActiveController
{

    public $modelClass = Slide::class;

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
        $search = new SlideSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

}
