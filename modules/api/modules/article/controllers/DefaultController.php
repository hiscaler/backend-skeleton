<?php

namespace app\modules\api\modules\article\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\article\models\Article;
use yii\web\NotFoundHttpException;

/**
 * /api/article/default
 *
 * @package app\modules\api\modules\article\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends ActiveController
{

    public $modelClass = Article::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);

        return $actions;
    }

    /**
     * 文章详情
     *
     * @param $id
     * @return Article|array|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->findModel($id);
    }

    /**
     * @param $id
     * @return Article|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        if (is_numeric($id)) {
            $condition = ['id' => (int) $id];
        } else {
            $condition = ['alias' => trim($id)];
        }
        $model = Article::find()->where($condition)->one();
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $model;
    }

}
