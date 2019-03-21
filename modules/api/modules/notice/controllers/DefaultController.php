<?php

namespace app\modules\api\modules\notice\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\notice\models\Notice;
use app\modules\api\modules\notice\models\NoticeSearch;

/**
 * /api/notice/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends ActiveController
{

    public $modelClass = Notice::class;

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
        $search = new NoticeSearch();

        return $search->search(\Yii::$app->getRequest()->getQueryParams());
    }

}
